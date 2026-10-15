<?php

namespace App\Http\Controllers\DeliveryBoy;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $boy = Auth::guard('delivery_boy')->user();

        $pending = Order::where('delivery_boy_id', $boy->id)
            ->whereNull('rider_delivered_at')
            ->where('order_status', '!=', 11)
            ->count();
        $doneTotal = Order::where('delivery_boy_id', $boy->id)->whereNotNull('rider_delivered_at')->count();
        $today = Order::where('delivery_boy_id', $boy->id)
            ->whereNotNull('rider_delivered_at')
            ->whereDate('rider_delivered_at', today())
            ->count();

        $recent = Order::with('shipping')
            ->where('delivery_boy_id', $boy->id)
            ->whereNull('rider_delivered_at')
            ->where('order_status', '!=', 11)
            ->orderByDesc('delivery_assigned_at')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        return view('delivery.dashboard', compact('boy', 'pending', 'doneTotal', 'today', 'recent'));
    }
}
