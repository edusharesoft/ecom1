<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Product;
use App\Models\Customer;
use App\Models\ResellerWalletTransaction;
use Carbon\Carbon;

class ResellerDashboardController extends Controller
{
    /**
     * Display reseller dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::guard('admin')->user();

        // Get orders where this reseller is involved
        // First check by user_id (if reseller placed order directly)
        // Then fallback to customer email matching (for old orders)
        $orders = Order::whereNotNull('reseller_profit')
            ->where(function($query) use ($user) {
                $query->where('user_id', $user->id) // Orders placed by this reseller
                      ->orWhereHas('customer', function($q) use ($user) {
                          // Fallback: Orders where customer email matches reseller email (old method)
                          $q->where('email', $user->email);
                      });
            })
            ->with(['customer', 'status', 'payment', 'shipping', 'orderdetails.product'])
            ->latest()
            ->get();

        // Calculate statistics
        $totalOrders = $orders->count();
        $totalProfit = $orders->sum('reseller_profit') ?? 0;
        $totalPayable = $orders->sum('customer_payable_amount') ?? 0;
        $pendingOrders = $orders->filter(function($order) {
            $status = (string)$order->order_status;
            return $status !== '6' && $status !== '11'; // Exclude delivered (6) and cancelled (11)
        })->count();

        // Get unique customers count
        $customerIds = $orders->pluck('customer_id')->unique()->filter();
        $totalCustomers = $customerIds->count();

        // Get products with reseller_price (popular products)
        $popularProducts = Product::whereNotNull('reseller_price')
            ->where('status', 1)
            ->where('approval_status', 'approved')
            ->where('stock', '>', 0)
            ->with(['image'])
            ->withCount(['sizes', 'colors'])
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get()
            ->map(function($product) {
                // Calculate profit (difference between reseller_price and new_price)
                $profit = $product->new_price - ($product->reseller_price ?? 0);
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'reseller_price' => $product->reseller_price,
                    'new_price' => $product->new_price,
                    'profit' => $profit > 0 ? $profit : 0,
                    'stock' => $product->stock,
                    'image' => $product->image->image ?? null,
                    'sizes_count' => $product->sizes_count ?? 0,
                    'colors_count' => $product->colors_count ?? 0,
                ];
            });

        // Weekly profit chart data (last 7 days)
        $weeklyProfit = [];
        $weeklyLabels = [];
        $dayNames = ['রবি', 'সোম', 'মঙ্গল', 'বুধ', 'বৃহঃ', 'শুক্র', 'শনি'];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dayProfit = $orders->filter(function($order) use ($date) {
                return Carbon::parse($order->created_at)->isSameDay($date);
            })->sum('reseller_profit');
            
            $weeklyProfit[] = round($dayProfit, 2);
            $dayOfWeek = $date->dayOfWeek; // 0 = Sunday, 6 = Saturday
            $weeklyLabels[] = $dayNames[$dayOfWeek];
        }

        // Monthly sales growth
        $currentMonthProfit = $orders->filter(function($order) {
            return Carbon::parse($order->created_at)->isCurrentMonth();
        })->sum('reseller_profit');
        
        $lastMonthProfit = $orders->filter(function($order) {
            return Carbon::parse($order->created_at)->isSameMonth(Carbon::now()->subMonth());
        })->sum('reseller_profit');
        
        $salesGrowth = 0;
        if ($lastMonthProfit > 0) {
            $salesGrowth = (($currentMonthProfit - $lastMonthProfit) / $lastMonthProfit) * 100;
        } elseif ($currentMonthProfit > 0) {
            $salesGrowth = 100;
        }

        // Pending balance (from pending orders)
        $pendingBalance = $orders->filter(function($order) {
            $status = (string)$order->order_status;
            return $status !== '6' && $status !== '11';
        })->sum('reseller_profit');

        return view('reseller.dashboard', compact(
            'user',
            'orders',
            'totalOrders',
            'totalProfit',
            'totalPayable',
            'pendingOrders',
            'totalCustomers',
            'popularProducts',
            'weeklyProfit',
            'weeklyLabels',
            'salesGrowth',
            'pendingBalance'
        ));
    }

    /**
     * Display reseller's orders.
     *
     * @return \Illuminate\View\View
     */
    public function orders()
    {
        $user = Auth::guard('admin')->user();

        // Get orders where this reseller is involved
        // First check by user_id (if reseller placed order directly)
        // Then fallback to customer email matching (for old orders)
        $orders = Order::whereNotNull('reseller_profit')
            ->where(function($query) use ($user) {
                $query->where('user_id', $user->id) // Orders placed by this reseller
                      ->orWhereHas('customer', function($q) use ($user) {
                          // Fallback: Orders where customer email matches reseller email (old method)
                          $q->where('email', $user->email);
                      });
            })
            ->with(['customer', 'status', 'payment', 'shipping', 'orderdetails.product:id,slug,name', 'orderdetails.product.image', 'orderdetails.image'])
            ->latest()
            ->paginate(10);

        return view('reseller.orders', compact('user', 'orders'));
    }

    /**
     * Display reseller's wallet balance and transactions.
     *
     * @return \Illuminate\View\View
     */
    public function wallet()
    {
        $user = Auth::guard('admin')->user();

        // Get orders with profit for wallet calculation
        // First check by user_id (if reseller placed order directly)
        // Then fallback to customer email matching (for old orders)
        $ordersQuery = Order::whereNotNull('reseller_profit')
            ->where(function($query) use ($user) {
                $query->where('user_id', $user->id) // Orders placed by this reseller
                      ->orWhereHas('customer', function($q) use ($user) {
                          // Fallback: Orders where customer email matches reseller email (old method)
                          $q->where('email', $user->email);
                      });
            })
            ->with(['customer', 'status']);

        // Calculate wallet statistics from all orders (before pagination)
        $allOrders = $ordersQuery->get();
        $walletBalance = $user->wallet_balance ?? 0;
        $totalEarned = $allOrders->sum('reseller_profit') ?? 0;
        $totalOrders = $allOrders->count();

        // Wallet transactions (deposits, order profit, delivery charge deduct, withdrawals)
        $transactions = ResellerWalletTransaction::where('user_id', $user->id)
            ->latest()
            ->paginate(20);

        // মোট ডেলিভারি চার্জ কেটে নেওয়া (ক্যান্সেলের জন্য)
        $totalDeliveryChargeDeducted = ResellerWalletTransaction::where('user_id', $user->id)
            ->where('type', 'delivery_charge_deduct')
            ->sum('amount');
        $totalDeliveryChargeDeducted = abs((float) $totalDeliveryChargeDeducted);

        return view('reseller.wallet', compact('user', 'transactions', 'walletBalance', 'totalEarned', 'totalOrders', 'totalDeliveryChargeDeducted'));
    }

    /**
     * Display reseller's customers list.
     *
     * @return \Illuminate\View\View
     */
    public function customers(Request $request)
    {
        $user = Auth::guard('admin')->user();

        // Get all orders where this reseller is involved
        // First check by user_id (if reseller placed order directly)
        // Then fallback to customer email matching (for old orders)
        $ordersQuery = Order::whereNotNull('reseller_profit')
            ->where(function($query) use ($user) {
                $query->where('user_id', $user->id) // Orders placed by this reseller
                      ->orWhereHas('customer', function($q) use ($user) {
                          // Fallback: Orders where customer email matches reseller email (old method)
                          $q->where('email', $user->email);
                      });
            })
            ->with(['customer', 'status', 'payment']);

        // Get unique customer IDs from orders
        $customerIds = $ordersQuery->pluck('customer_id')->unique()->filter();

        // Get customers with their order statistics
        $customersQuery = Customer::whereIn('id', $customerIds)
            ->withCount(['orders' => function($query) use ($user) {
                $query->whereNotNull('reseller_profit')
                    ->where(function($q) use ($user) {
                        $q->where('user_id', $user->id)
                          ->orWhereHas('customer', function($subQ) use ($user) {
                              $subQ->where('email', $user->email);
                          });
                    });
            }]);

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $customersQuery->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('phone', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        // Get customer statistics
        $customers = $customersQuery->latest()->paginate(10);

        // Add additional statistics for each customer
        $customers->getCollection()->transform(function($customer) use ($user) {
            $customerOrders = Order::whereNotNull('reseller_profit')
                ->where(function($query) use ($user) {
                    $query->where('user_id', $user->id)
                          ->orWhereHas('customer', function($q) use ($user) {
                              $q->where('email', $user->email);
                          });
                })
                ->where('customer_id', $customer->id)
                ->get();

            $customer->total_orders = $customerOrders->count();
            $customer->total_spent = $customerOrders->sum('amount') ?? 0;
            $customer->total_profit = $customerOrders->sum('reseller_profit') ?? 0;
            $customer->last_order_date = $customerOrders->max('created_at');

            return $customer;
        });

        return view('reseller.customers', compact('user', 'customers'));
    }
}
