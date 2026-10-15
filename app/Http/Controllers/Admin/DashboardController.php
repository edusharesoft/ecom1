<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Models\Customer;
use App\Models\OrderDetails;
use App\Models\FundTransaction;
use App\Models\Expense;
use App\Models\Category;
use Carbon\Carbon;
use App\Support\AdminOrderNotification;
use Session;
use Toastr;
use Auth;
use DB;

class DashboardController extends Controller
{
    public function dashboard()
    {
        // ── Basic counts ──
        $total_order    = Order::count();
        $total_product  = Product::count();
        $total_customer = Customer::count();
        $total_delivery = Order::where('order_status', '6')->count();

        // ── Revenue & pending ──
        $total_revenue     = Order::where('order_status', '6')->sum('amount');
        $pending_orders    = Order::whereNotIn('order_status', ['6','7'])->count();
        $low_stock         = Product::where('stock', '<', 10)->count();

        // ── Today stats ──
        $today_order              = Order::whereDate('created_at', Carbon::today())->count();
        $today_revenue            = Order::whereDate('created_at', Carbon::today())->sum('amount');
        $today_delivered_revenue  = Order::where('order_status', '6')->whereDate('updated_at', Carbon::today())->sum('amount');
        $today_delivery           = Order::where('order_status', '6')->whereDate('updated_at', Carbon::today())->count();

        // ── Today profit ──
        $todayDeliveredOrders = Order::where('order_status', '6')->whereDate('updated_at', Carbon::today())->get();
        $todayOrderIds        = $todayDeliveredOrders->pluck('id');
        $todayDetails         = OrderDetails::whereIn('order_id', $todayOrderIds)->with('product:id,purchase_price')->get();
        $today_cogs           = $todayDetails->sum(function ($r) {
            return ($r->purchase_price ?? ($r->product->purchase_price ?? 0)) * $r->qty;
        });
        $today_profit = $todayDeliveredOrders->sum('amount') - $today_cogs;

        // ── Last 7 days trend ──
        $last7 = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $last7->push([
                'date'  => $date->format('M d'),
                'count' => Order::whereDate('created_at', $date)->count(),
            ]);
        }
        $trend_labels = $last7->pluck('date');
        $trend_data   = $last7->pluck('count');

        // ── Order status breakdown ──
        $statusGroups = Order::select('order_status', DB::raw('count(*) as total'))
            ->groupBy('order_status')->get();
        $statusMap    = ['1'=>'Pending','2'=>'Confirmed','3'=>'Processing','4'=>'Picked','5'=>'Shipped','6'=>'Delivered','7'=>'Cancelled'];
        $statusLabels = [];
        $statusData   = [];
        foreach ($statusGroups as $sg) {
            $statusLabels[] = $statusMap[$sg->order_status] ?? 'Status '.$sg->order_status;
            $statusData[]   = (int) $sg->total;
        }
        if (empty($statusLabels)) { $statusLabels = ['No Orders']; $statusData = [0]; }

        // ── Recent data ──
        $latest_order    = Order::latest()->with('customer', 'status')->limit(8)->get();
        $latest_products = Product::with('category', 'image')->latest()->limit(8)->get();
        $latest_customer = Customer::latest()->limit(5)->get();
        $categories      = Category::withCount('products')->orderBy('products_count', 'desc')->limit(8)->get();

        // ── Fund & expenses ──
        $fund_balance     = FundTransaction::where('direction', 'in')->sum('amount') - FundTransaction::where('direction', 'out')->sum('amount');
        $total_expenses   = Expense::sum('amount');
        $today_expenses   = Expense::whereDate('created_at', Carbon::today())->sum('amount');
        $monthly_expenses = Expense::whereYear('created_at', Carbon::now()->year)->whereMonth('created_at', Carbon::now()->month)->sum('amount');

        // ── Category sales (donut chart) ──
        $deliveredOrderIds = Order::where('order_status', '6')->pluck('id');
        $categorySales = OrderDetails::whereIn('order_id', $deliveredOrderIds)
            ->join('products',   'order_details.product_id',   '=', 'products.id')
            ->join('categories', 'products.category_id',       '=', 'categories.id')
            ->select('categories.name as category_name', DB::raw('SUM(order_details.sale_price * order_details.qty) as total_sales'))
            ->groupBy('categories.name')->orderBy('total_sales', 'DESC')->get();
        $categoryLabels = $categorySales->pluck('category_name')->toArray();
        $categorySeries = $categorySales->pluck('total_sales')->map(fn($v) => (float) number_format($v, 2, '.', ''))->toArray();
        if (empty($categoryLabels)) { $categoryLabels = ['No Sales']; $categorySeries = [0]; }

        // ── Monthly sale (legacy chart) ──
        $monthly_sale = Order::select(DB::raw('DATE(updated_at) as date'))->selectRaw('SUM(amount) as amount')
            ->where('order_status', '6')->groupBy('date')->orderBy('date', 'desc')->limit(30)->get();
        $last_week    = Order::where('order_status', '6')->whereBetween('updated_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();
        $last_month   = Order::where('order_status', '6')->whereYear('updated_at', Carbon::now()->subMonth()->year)->whereMonth('updated_at', Carbon::now()->subMonth()->month)->count();

        // ── Traffic Source stats ──
        $sourceIconMap = [
            'facebook'  => ['icon' => 'facebook',  'color' => '#1877F2', 'label' => 'Facebook'],
            'google'    => ['icon' => 'google',    'color' => '#EA4335', 'label' => 'Google'],
            'tiktok'    => ['icon' => 'tiktok',    'color' => '#010101', 'label' => 'TikTok'],
            'whatsapp'  => ['icon' => 'whatsapp',  'color' => '#25D366', 'label' => 'WhatsApp'],
            'instagram' => ['icon' => 'instagram', 'color' => '#E1306C', 'label' => 'Instagram'],
            'youtube'   => ['icon' => 'youtube',   'color' => '#FF0000', 'label' => 'YouTube'],
            'direct'    => ['icon' => 'direct',    'color' => '#6366f1', 'label' => 'Direct'],
            'twitter'   => ['icon' => 'twitter',   'color' => '#1DA1F2', 'label' => 'Twitter/X'],
            'bing'      => ['icon' => 'bing',      'color' => '#008373', 'label' => 'Bing'],
            'yahoo'     => ['icon' => 'yahoo',     'color' => '#6001D2', 'label' => 'Yahoo'],
            'other'     => ['icon' => 'other',     'color' => '#9ca3af', 'label' => 'Other'],
        ];

        $trafficRaw = Order::select('traffic_source', DB::raw('count(*) as total'))
            ->whereNotNull('traffic_source')
            ->groupBy('traffic_source')
            ->orderByDesc('total')
            ->get();

        $trafficSources = $trafficRaw->map(function ($row) use ($sourceIconMap) {
            $key  = strtolower($row->traffic_source ?? 'direct');
            $meta = $sourceIconMap[$key] ?? $sourceIconMap['other'];
            return [
                'source' => $key,
                'label'  => $meta['label'],
                'color'  => $meta['color'],
                'icon'   => $meta['icon'],
                'count'  => (int) $row->total,
            ];
        });

        $trafficTotal     = $trafficSources->sum('count') ?: 1; // avoid division by zero

        return view('backEnd.admin.dashboard', compact(
            'total_order', 'total_product', 'total_customer', 'total_delivery',
            'total_revenue', 'pending_orders', 'low_stock',
            'today_order', 'today_revenue', 'today_delivered_revenue', 'today_delivery',
            'today_profit', 'trend_labels', 'trend_data',
            'statusLabels', 'statusData',
            'latest_order', 'latest_products', 'latest_customer', 'categories',
            'fund_balance', 'total_expenses', 'today_expenses', 'monthly_expenses',
            'categoryLabels', 'categorySeries', 'monthly_sale', 'last_week', 'last_month',
            'trafficSources', 'trafficTotal'
        ));
    }

    /**
     * লাইভ পোল: লগইন অবস্থায় নতুন অর্ডার (after_id এর পরে)
     */
    public function pollNewOrders(Request $request)
    {
        $afterId = (int) $request->query('after_id', 0);

        if ($request->boolean('init') || $afterId <= 0) {
            $latestId = (int) (Order::max('id') ?? 0);
            return response()->json([
                'init'      => true,
                'latest_id' => $latestId,
                'orders'    => [],
            ]);
        }

        $orders = Order::query()
            ->where('id', '>', $afterId)
            ->with([
                'shipping:id,order_id,name,phone',
                'status:id,name',
                'customer:id,name,phone',
                'orderdetails' => fn ($q) => $q->select('id', 'order_id', 'product_id', 'product_name', 'qty'),
                'orderdetails.product.image',
                'orderdetails.image',
            ])
            ->orderBy('id', 'asc')
            ->limit(10)
            ->get();

        $mapped = $orders->map(fn (Order $order) => $this->mapOrderForNotification($order))->values();
        $latestId = $orders->isNotEmpty()
            ? (int) $orders->max('id')
            : (int) (Order::max('id') ?? $afterId);

        return response()->json([
            'orders'    => $mapped,
            'latest_id' => $latestId,
        ]);
    }

    /**
     * লগইনের পর ড্যাশবোর্ডে নতুন অর্ডার পপআপ (সর্বোচ্চ ১০টি)
     */
    public function newOrdersForPopup()
    {
        if (!AdminOrderNotification::shouldShowPopup()) {
            return response()->json(['show' => false, 'orders' => [], 'count' => 0, 'latest_id' => (int) (Order::max('id') ?? 0)]);
        }

        $orders = Order::query()
            ->with([
                'shipping:id,order_id,name,phone',
                'status:id,name',
                'customer:id,name,phone',
                'orderdetails' => fn ($q) => $q->select('id', 'order_id', 'product_id', 'product_name', 'qty'),
                'orderdetails.product.image',
                'orderdetails.image',
            ])
            ->latest('id')
            ->limit(10)
            ->get();

        $mapped = $orders->map(fn (Order $order) => $this->mapOrderForNotification($order));

        return response()->json([
            'show'      => $mapped->isNotEmpty(),
            'count'     => $mapped->count(),
            'orders'    => $mapped->values(),
            'latest_id' => (int) ($orders->max('id') ?? Order::max('id') ?? 0),
        ]);
    }

    private function mapOrderForNotification(Order $order): array
    {
        $name = $order->shipping->name ?? $order->customer->name ?? '—';
        $phone = $order->shipping->phone ?? $order->customer->phone ?? '—';
        $fallbackImg = asset('public/no-image.png');

        $products = [];
        $details = $order->relationLoaded('orderdetails') ? $order->orderdetails : collect();
        $totalItems = $details->count();

        foreach ($details->take(4) as $detail) {
            $imgPath = null;
            if ($detail->product && $detail->product->image) {
                $imgPath = $detail->product->image->image;
            } elseif ($detail->image) {
                $imgPath = $detail->image->image;
            }
            $products[] = [
                'name'  => $detail->product_name ?? 'পণ্য',
                'qty'   => (int) ($detail->qty ?? 1),
                'image' => $imgPath ? asset($imgPath) : $fallbackImg,
            ];
        }

        return [
            'id'            => $order->id,
            'invoice_id'    => $order->invoice_id,
            'customer_name' => $name,
            'phone'         => $phone,
            'amount'        => number_format((float) ($order->amount ?? 0), 0),
            'status_name'   => $order->status->name ?? '—',
            'created_at'    => optional($order->created_at)->format('d M, Y h:i A'),
            'process_url'   => route('admin.order.process', ['invoice_id' => $order->invoice_id]),
            'products'      => $products,
            'more_count'    => max(0, $totalItems - 4),
        ];
    }

    public function dismissNewOrdersPopup()
    {
        AdminOrderNotification::dismissPopup();

        return response()->json(['ok' => true]);
    }

    public function changepassword()
    {
        return view('backEnd.admin.changepassword');
    }

    public function newpassword(Request $request)
    {
        $this->validate($request, [
            'old_password'     => 'required',
            'new_password'     => 'required',
            'confirm_password' => 'required_with:new_password|same:new_password|'
        ]);

        $user     = User::find(Auth::id());
        $hashPass = $user->password;

        if (Hash::check($request->old_password, $hashPass)) {
            $user->fill(['password' => Hash::make($request->new_password)])->save();
            Toastr::success('Success', 'Password changed successfully!');
            return redirect()->route('dashboard');
        }

        Toastr::error('Failed', 'Old password not match!');
        return back();
    }

    public function locked()
    {
        Session::put('locked', true);
        return view('backEnd.auth.locked');
    }

    public function unlocked(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (Hash::check($request->password, Auth::user()->password)) {
            Session::forget('locked');
            Toastr::success('Success', 'You are logged in successfully!');
            return redirect()->route('dashboard');
        }

        Toastr::error('Failed', 'Your password not match!');
        return back();
    }
}
