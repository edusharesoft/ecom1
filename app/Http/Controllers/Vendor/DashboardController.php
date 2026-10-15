<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display vendor dashboard with vendor's products and orders.
     */
    public function index()
    {
        // Get authenticated user and vendor_id
        $user = Auth::user();
        $vendorId = $user->vendor_id;

        if (!$vendorId) {
            return redirect()->route('home')
                ->with('error', 'Vendor profile not found. Please contact administrator.');
        }

        // Get vendor details
        $vendor = Vendor::findOrFail($vendorId);

        // ✅ Limit products for dashboard to show only recent items (avoid loading all)
        $products = Product::where('vendor_id', $vendorId)
            ->with(['image', 'category', 'brand'])
            ->latest()
            ->limit(20) // Show only recent 20 products on dashboard
            ->get();

        // Get product IDs for this vendor (filtered by vendor_id)
        $productIds = $products->pluck('id')->toArray();

        // Get orders that contain products from this vendor
        // Orders are filtered by products that belong to this vendor (vendor_id constraint)
        $orderIds = OrderDetails::whereIn('product_id', function($query) use ($vendorId) {
                $query->select('id')
                      ->from('products')
                      ->where('vendor_id', $vendorId);
            })
            ->distinct()
            ->pluck('order_id')
            ->toArray();

        // Get orders with their details (filtered by vendor_id through products)
        $orders = collect([]); // Default empty collection
        if (!empty($orderIds)) {
            $orders = Order::whereIn('id', $orderIds)
                ->with([
                    'orderdetails' => function($query) use ($vendorId) {
                        // Only include order details for products belonging to this vendor
                        $query->whereHas('product', function($q) use ($vendorId) {
                            $q->where('vendor_id', $vendorId);
                        })->with('product');
                    },
                    'customer',
                    'status',
                    'payment',
                    'shipping'
                ])
                ->latest()
                ->get();
        }

        // Calculate statistics
        $totalProducts = $products->count();
        $totalOrders = $orders->count();
        // Handle order_status as string or integer
        $totalSales = $orders->filter(function($order) {
            $status = (string)$order->order_status;
            return $status === '6' || $status === 6;
        })->sum('amount'); // Status 6 = Delivered
        $pendingOrders = $orders->filter(function($order) {
            $status = (string)$order->order_status;
            return $status !== '6' && $status !== '11';
        })->count(); // Exclude delivered (6) and cancelled (11)

        // Category wise product count
        $categoryWiseProducts = $products->groupBy('category_id')->map(function($categoryProducts) {
            return [
                'category' => $categoryProducts->first()->category->name ?? 'Uncategorized',
                'count' => $categoryProducts->count()
            ];
        })->values();

        // Order status counts
        $newOrders = $orders->filter(function($order) {
            $status = (string)$order->order_status;
            return $status !== '6' && $status !== '11';
        })->count();
        $cancelledOrders = $orders->filter(function($order) {
            $status = (string)$order->order_status;
            return $status === '11';
        })->count();
        $onDeliveryOrders = $orders->filter(function($order) {
            $status = (string)$order->order_status;
            return $status === '4';
        })->count();
        $deliveredOrders = $orders->filter(function($order) {
            $status = (string)$order->order_status;
            return $status === '6';
        })->count();

        // Monthly Sales (Current Month)
        $monthlySales = $orders->filter(function($order) {
            $status = (string)$order->order_status;
            return ($status === '6' || $status === 6) && 
                   Carbon::parse($order->updated_at)->isCurrentMonth();
        })->sum('amount');

        // Last Month Sales
        $lastMonthSales = Order::whereIn('id', $orderIds)
            ->where('order_status', '6')
            ->whereYear('updated_at', Carbon::now()->subMonth()->year)
            ->whereMonth('updated_at', Carbon::now()->subMonth()->month)
            ->sum('amount');

        // Weekly Sales Data for Chart (Last 7 days)
        $weeklySales = [];
        $weeklyLabels = [];
        $dayNames = ['রবি', 'সোম', 'মঙ্গল', 'বুধ', 'বৃহঃ', 'শুক্র', 'শনি'];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $daySales = $orders->filter(function($order) use ($date) {
                $status = (string)$order->order_status;
                return ($status === '6' || $status === 6) && 
                       Carbon::parse($order->updated_at)->isSameDay($date);
            })->sum('amount');
            
            $weeklySales[] = round($daySales, 2);
            $dayOfWeek = $date->dayOfWeek; // 0 = Sunday, 6 = Saturday
            $weeklyLabels[] = $dayNames[$dayOfWeek];
        }

        // Monthly Sales Data for Chart (Last 30 days or current month)
        $monthlySalesData = [];
        $monthlyLabels = [];
        
        // Get last 30 days data (only if we have orders)
        if (!empty($orderIds)) {
            for ($i = 29; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $daySales = Order::whereIn('id', $orderIds)
                    ->where('order_status', '6')
                    ->whereDate('updated_at', $date->format('Y-m-d'))
                    ->sum('amount');
                
                $monthlySalesData[] = round($daySales, 2);
                $monthlyLabels[] = $date->format('d M');
            }
        } else {
            // If no orders, fill with zeros
            for ($i = 29; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $monthlySalesData[] = 0;
                $monthlyLabels[] = $date->format('d M');
            }
        }

        // Popular Products (Top 3 by sales)
        $popularProducts = OrderDetails::whereIn('order_id', $orderIds)
            ->whereHas('order', function($q) {
                $q->where('order_status', '6');
            })
            ->select('product_id', DB::raw('SUM(qty) as total_sold'), DB::raw('SUM(sale_price * qty) as total_revenue'))
            ->groupBy('product_id')
            ->with(['product:id,name,slug', 'product.image:id,product_id,image'])
            ->orderBy('total_sold', 'desc')
            ->limit(3)
            ->get();

        // Recent Orders (Last 5)
        $recentOrders = $orders->take(5);

        // Return Orders Count (Refunds)
        $returnOrders = DB::table('refunds')
            ->whereIn('order_id', $orderIds)
            ->where('status', 'pending')
            ->count();

        // Calculate sales growth percentage
        $salesGrowth = 0;
        if ($lastMonthSales > 0) {
            $salesGrowth = (($monthlySales - $lastMonthSales) / $lastMonthSales) * 100;
        } elseif ($monthlySales > 0) {
            $salesGrowth = 100;
        }

        return view('vendor.dashboard', compact(
            'vendor',
            'products',
            'orders',
            'totalProducts',
            'totalOrders',
            'totalSales',
            'pendingOrders',
            'categoryWiseProducts',
            'newOrders',
            'cancelledOrders',
            'onDeliveryOrders',
            'deliveredOrders',
            'monthlySales',
            'lastMonthSales',
            'weeklySales',
            'weeklyLabels',
            'monthlySalesData',
            'monthlyLabels',
            'popularProducts',
            'recentOrders',
            'returnOrders',
            'salesGrowth'
        ));
    }

    /**
     * Display vendor's products.
     */
    public function products()
    {
        // Redirect to ProductController index
        return redirect()->route('vendor.products.index');
    }

    /**
     * Display vendor's orders.
     */
    public function orders(Request $request)
    {
        $user = Auth::user();
        $vendorId = $user->vendor_id;

        if (!$vendorId) {
            return redirect()->route('home')
                ->with('error', 'Vendor profile not found.');
        }

        $vendor = Vendor::findOrFail($vendorId);

        // Get order IDs for this vendor
        $orderIds = OrderDetails::whereIn('product_id', function($query) use ($vendorId) {
                $query->select('id')
                      ->from('products')
                      ->where('vendor_id', $vendorId);
            })
            ->distinct()
            ->pluck('order_id')
            ->toArray();

        // Build query
        $query = Order::whereIn('id', $orderIds)
            ->with([
                'orderdetails' => function($query) use ($vendorId) {
                    $query->whereHas('product', function($q) use ($vendorId) {
                        $q->where('vendor_id', $vendorId);
                    })->with(['product.image', 'product.images', 'color', 'size']);
                },
                'customer',
                'status',
                'payment',
                'shipping'
            ]);

        // Search by keyword (Order ID or Customer name)
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function($q) use ($keyword) {
                $q->where('invoice_id', 'LIKE', "%{$keyword}%")
                  ->orWhere('id', 'LIKE', "%{$keyword}%")
                  ->orWhereHas('customer', function($customerQuery) use ($keyword) {
                      $customerQuery->where('name', 'LIKE', "%{$keyword}%")
                                    ->orWhere('phone', 'LIKE', "%{$keyword}%");
                  });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $statusFilter = $request->status;
            if ($statusFilter == 'pending') {
                $query->whereIn('order_status', ['1', '2', '3']);
            } elseif ($statusFilter == 'delivered') {
                $query->where('order_status', '6');
            } elseif ($statusFilter == 'cancelled') {
                $query->whereIn('order_status', ['8', '11']);
            }
        }

        // Filter by date
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $orders = $query->latest()->paginate(20);

        // Calculate total revenue (all orders, not just paginated)
        $totalRevenue = Order::whereIn('id', $orderIds)
            ->where('order_status', '6') // Only delivered orders
            ->sum('amount');

        return view('vendor.orders', compact('vendor', 'orders', 'totalRevenue'));
    }

    /**
     * Export orders to CSV
     */
    public function exportOrders(Request $request)
    {
        $user = Auth::user();
        $vendorId = $user->vendor_id;

        if (!$vendorId) {
            return redirect()->route('home')
                ->with('error', 'Vendor profile not found.');
        }

        // Get order IDs for this vendor
        $orderIds = OrderDetails::whereIn('product_id', function($query) use ($vendorId) {
                $query->select('id')
                      ->from('products')
                      ->where('vendor_id', $vendorId);
            })
            ->distinct()
            ->pluck('order_id')
            ->toArray();

        // Build query (same as orders method)
        $query = Order::whereIn('id', $orderIds)
            ->with([
                'orderdetails' => function($query) use ($vendorId) {
                    $query->whereHas('product', function($q) use ($vendorId) {
                        $q->where('vendor_id', $vendorId);
                    })->with(['product.image', 'product.images', 'color', 'size']);
                },
                'customer',
                'status',
                'payment',
                'shipping'
            ]);

        // Apply same filters as orders method
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function($q) use ($keyword) {
                $q->where('invoice_id', 'LIKE', "%{$keyword}%")
                  ->orWhere('id', 'LIKE', "%{$keyword}%")
                  ->orWhereHas('customer', function($customerQuery) use ($keyword) {
                      $customerQuery->where('name', 'LIKE', "%{$keyword}%")
                                    ->orWhere('phone', 'LIKE', "%{$keyword}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $statusFilter = $request->status;
            if ($statusFilter == 'pending') {
                $query->whereIn('order_status', ['1', '2', '3']);
            } elseif ($statusFilter == 'delivered') {
                $query->where('order_status', '6');
            } elseif ($statusFilter == 'cancelled') {
                $query->whereIn('order_status', ['8', '11']);
            }
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $orders = $query->latest()->get();

        // Generate CSV
        $filename = 'vendor_orders_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // CSV Headers
            fputcsv($file, [
                'Order ID',
                'Invoice ID',
                'Customer Name',
                'Customer Phone',
                'Customer Email',
                'Order Date',
                'Total Amount',
                'Payment Method',
                'Status',
                'Product Count',
                'Shipping Address'
            ]);

            // CSV Data
            foreach ($orders as $order) {
                $statusName = 'Unknown';
                $status = (string)($order->order_status ?? '');
                if ($status == '1' || $status == 1) {
                    $statusName = 'Pending';
                } elseif ($status == '2' || $status == 2) {
                    $statusName = 'Processing';
                } elseif ($status == '3' || $status == 3) {
                    $statusName = 'Shipping';
                } elseif ($status == '6' || $status == 6) {
                    $statusName = 'Delivered';
                } elseif ($status == '8' || $status == 8 || $status == '11' || $status == 11) {
                    $statusName = 'Cancelled';
                } else {
                    $statusName = $order->status->name ?? 'Unknown';
                }

                fputcsv($file, [
                    $order->id,
                    $order->invoice_id ?? 'N/A',
                    $order->customer->name ?? 'Guest',
                    $order->customer->phone ?? 'N/A',
                    $order->customer->email ?? 'N/A',
                    $order->created_at->format('Y-m-d H:i:s'),
                    number_format($order->amount, 2),
                    $order->payment->payment_method ?? 'COD',
                    $statusName,
                    $order->orderdetails->count(),
                    $order->shipping->address ?? 'N/A'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Display vendor analytics page.
     */
    public function analytics()
    {
        $user = Auth::user();
        $vendorId = $user->vendor_id;

        if (!$vendorId) {
            return redirect()->route('home')
                ->with('error', 'Vendor profile not found.');
        }

        $vendor = Vendor::findOrFail($vendorId);

        // Get order IDs for this vendor
        $orderIds = OrderDetails::whereIn('product_id', function($query) use ($vendorId) {
                $query->select('id')
                      ->from('products')
                      ->where('vendor_id', $vendorId);
            })
            ->distinct()
            ->pluck('order_id')
            ->toArray();

        // Total Sales (All time)
        $totalSales = Order::whereIn('id', $orderIds)
            ->where('order_status', '6')
            ->sum('amount');

        // Monthly Sales (Last 12 months)
        $monthlySales = [];
        $monthlyLabels = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthSales = Order::whereIn('id', $orderIds)
                ->where('order_status', '6')
                ->whereYear('updated_at', $date->year)
                ->whereMonth('updated_at', $date->month)
                ->sum('amount');
            
            $monthlySales[] = round($monthSales, 2);
            $monthlyLabels[] = $date->format('M Y');
        }

        // Daily Sales (Last 30 days)
        $dailySales = [];
        $dailyLabels = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $daySales = Order::whereIn('id', $orderIds)
                ->where('order_status', '6')
                ->whereDate('updated_at', $date->format('Y-m-d'))
                ->sum('amount');
            
            $dailySales[] = round($daySales, 2);
            $dailyLabels[] = $date->format('d M');
        }

        // Top Selling Products (Last 30 days)
        $topProducts = OrderDetails::whereIn('order_id', $orderIds)
            ->whereHas('order', function($q) {
                $q->where('order_status', '6')
                  ->where('updated_at', '>=', Carbon::now()->subDays(30));
            })
            ->select('product_id', DB::raw('SUM(qty) as total_sold'), DB::raw('SUM(sale_price * qty) as total_revenue'))
            ->groupBy('product_id')
            ->with(['product:id,name,slug', 'product.image:id,product_id,image'])
            ->orderBy('total_sold', 'desc')
            ->limit(10)
            ->get();

        // Order Status Distribution
        $orderStatusData = [];
        $statuses = [
            '1' => 'Pending',
            '2' => 'Processing',
            '3' => 'Shipping',
            '6' => 'Delivered',
            '8' => 'Cancelled',
            '11' => 'Cancelled'
        ];
        
        foreach ($statuses as $statusId => $statusName) {
            $count = Order::whereIn('id', $orderIds)
                ->where('order_status', $statusId)
                ->count();
            if ($count > 0) {
                $orderStatusData[] = [
                    'status' => $statusName,
                    'count' => $count
                ];
            }
        }

        // Revenue by Month (Current Year)
        $currentYearRevenue = [];
        $currentYearLabels = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthSales = Order::whereIn('id', $orderIds)
                ->where('order_status', '6')
                ->whereYear('updated_at', Carbon::now()->year)
                ->whereMonth('updated_at', $i)
                ->sum('amount');
            
            $currentYearRevenue[] = round($monthSales, 2);
            $currentYearLabels[] = Carbon::create(null, $i, 1)->format('M');
        }

        // Total Orders
        $totalOrders = Order::whereIn('id', $orderIds)->count();
        $deliveredOrders = Order::whereIn('id', $orderIds)->where('order_status', '6')->count();
        $pendingOrders = Order::whereIn('id', $orderIds)->whereIn('order_status', ['1', '2', '3'])->count();
        $cancelledOrders = Order::whereIn('id', $orderIds)->whereIn('order_status', ['8', '11'])->count();

        // Average Order Value
        $avgOrderValue = $deliveredOrders > 0 ? round($totalSales / $deliveredOrders, 2) : 0;

        // Conversion Rate (if applicable)
        $totalProducts = Product::where('vendor_id', $vendorId)->count();

        return view('vendor.analytics', compact(
            'vendor',
            'totalSales',
            'monthlySales',
            'monthlyLabels',
            'dailySales',
            'dailyLabels',
            'topProducts',
            'orderStatusData',
            'currentYearRevenue',
            'currentYearLabels',
            'totalOrders',
            'deliveredOrders',
            'pendingOrders',
            'cancelledOrders',
            'avgOrderValue',
            'totalProducts'
        ));
    }

    /**
     * Display vendor's customers (who ordered vendor's products).
     */
    public function customers(Request $request)
    {
        $user = Auth::user();
        $vendorId = $user->vendor_id;

        if (!$vendorId) {
            return redirect()->route('home')
                ->with('error', 'Vendor profile not found.');
        }

        $vendor = Vendor::findOrFail($vendorId);

        // Get order IDs for this vendor
        $orderIds = OrderDetails::whereIn('product_id', function($query) use ($vendorId) {
                $query->select('id')
                      ->from('products')
                      ->where('vendor_id', $vendorId);
            })
            ->distinct()
            ->pluck('order_id')
            ->toArray();

        // Get unique customer IDs from orders
        $customerIds = Order::whereIn('id', $orderIds)
            ->whereNotNull('customer_id')
            ->distinct()
            ->pluck('customer_id')
            ->toArray();

        // Build query for customers
        $query = \App\Models\Customer::whereIn('id', $customerIds)
            ->withCount(['orders' => function($q) use ($orderIds) {
                $q->whereIn('id', $orderIds);
            }]);

        // Search by keyword (name, phone, email)
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function($q) use ($keyword) {
                $q->where('name', 'LIKE', "%{$keyword}%")
                  ->orWhere('phone', 'LIKE', "%{$keyword}%")
                  ->orWhere('email', 'LIKE', "%{$keyword}%");
            });
        }

        $customers = $query->latest()->paginate(20);

        // Calculate total spent per customer
        foreach ($customers as $customer) {
            $customerOrders = Order::whereIn('id', $orderIds)
                ->where('customer_id', $customer->id)
                ->where('order_status', '6') // Only delivered orders
                ->sum('amount');
            $customer->total_spent = $customerOrders;
        }

        return view('vendor.customers', compact('vendor', 'customers'));
    }

    /**
     * Display vendor settings.
     */
    public function settings()
    {
        // Redirect to SettingsController
        return redirect()->route('vendor.settings');
    }
}
