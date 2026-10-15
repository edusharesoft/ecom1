<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\User;
use App\Models\Customer;
use App\Models\FundTransaction;
use App\Models\OrderDetails;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Brian2694\Toastr\Facades\Toastr;

class ResellerOrderController extends Controller
{
    /**
     * Display all reseller orders.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Get all orders with reseller_profit (reseller orders)
        // Exclude completed/delivered orders (status = 6) - they should only appear in main order list
        $query = Order::whereNotNull('reseller_profit')
            ->where('order_status', '!=', 6) // Exclude completed/delivered orders
            ->with([
                'customer:id,name,phone,email',
                'status',
                'payment',
                'shipping',
                'orderdetails.product:id,slug,name',
                'orderdetails.product.image',
                'orderdetails.image',
                'user:id,name,email' // Reseller user
            ])
            ->latest();

        // Filter by status (but still exclude completed orders)
        if ($request->filled('status')) {
            $status = (int) $request->status;
            // Don't show completed orders even if requested
            if ($status != 6) {
                $query->where('order_status', $status);
            }
        }

        // Search by invoice ID, customer name, phone, or reseller name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('invoice_id', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($customerQuery) use ($search) {
                      $customerQuery->where('name', 'like', "%{$search}%")
                                    ->orWhere('phone', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by reseller
        if ($request->filled('reseller_id')) {
            $query->where('user_id', $request->reseller_id);
        }

        $orders = $query->paginate(20)->withQueryString();

        // Get all order statuses
        $orderStatuses = OrderStatus::orderBy('id')->get();

        // Get all resellers (users with reseller role)
        $resellers = User::where(function($q) {
            $q->where('role', 'reseller')
              ->orWhereHas('roles', function($roleQuery) {
                  $roleQuery->where('name', 'reseller');
              });
        })->select('id', 'name', 'email')->get();

        return view('backEnd.reseller_orders.index', compact('orders', 'orderStatuses', 'resellers'));
    }

    /**
     * Update order status for reseller orders.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'order_status' => 'required|exists:order_statuses,id',
        ]);

        $order = Order::findOrFail($request->order_id);
        
        // Check if this is a reseller order
        if (!$order->reseller_profit) {
            Toastr::error('This is not a reseller order.', 'Error!');
            return redirect()->back();
        }

        $oldStatus = (int) $order->order_status;
        $newStatus = (int) $request->order_status;

        $order->order_status = $newStatus;
        $order->save();

        // Handle stock change (similar to OrderController)
        $this->handleStockChange($order, $oldStatus, $newStatus);

        if ($newStatus == 11) {
            \App\Helpers\ResellerOrderHelper::deductDeliveryChargeOnCancel($order);
        }

        // If order is delivered/completed (status = 6)
        if ($newStatus == 6 && $oldStatus != 6) {
            // Add money to fund
            FundTransaction::create([
                'direction'  => 'in',
                'source'     => 'sale',
                'source_id'  => $order->id,
                'amount'     => $order->amount,
                'note'       => 'Reseller order complete (#' . $order->invoice_id . ')',
                'created_by' => Auth::id(),
            ]);

            // Credit reseller wallet
            $this->creditResellerWallet($order);
            
            // Redirect to main order list (completed section) since this order is now complete
            // Find delivered status slug
            $deliveredStatus = OrderStatus::find(6);
            $slug = $deliveredStatus ? $deliveredStatus->slug : 'all';
            Toastr::success('Order status updated to completed! Order moved to main order list.', 'Success!');
            return redirect()->route('admin.orders', ['slug' => $slug])->with('message', 'Order completed and moved to main order list.');
        }

        // If order is cancelled (status = 11), stock will be restored by handleStockChange
        // No additional action needed as handleStockChange handles it

        Toastr::success('Order status updated successfully!', 'Success!');
        return redirect()->back();
    }

    /**
     * Bulk update order status.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'order_ids' => 'required|array',
            'order_ids.*' => 'exists:orders,id',
            'order_status' => 'required|exists:order_statuses,id',
        ]);

        $orders = Order::whereIn('id', $request->order_ids)
            ->whereNotNull('reseller_profit')
            ->get();

        $targetStatus = (int) $request->order_status;
        $completedCount = 0;

        foreach ($orders as $order) {
            $oldStatus = (int) $order->order_status;
            
            $order->order_status = $targetStatus;
            $order->save();

            // Handle stock change
            $this->handleStockChange($order, $oldStatus, $targetStatus);

            if ($targetStatus == 11) {
                \App\Helpers\ResellerOrderHelper::deductDeliveryChargeOnCancel($order);
            }

            // If order is delivered/completed (status = 6)
            if ($targetStatus == 6 && $oldStatus != 6) {
                // Add money to fund
                FundTransaction::create([
                    'direction'  => 'in',
                    'source'     => 'sale',
                    'source_id'  => $order->id,
                    'amount'     => $order->amount,
                    'note'       => 'Reseller order complete (#' . $order->invoice_id . ')',
                    'created_by' => Auth::id(),
                ]);

                // Credit reseller wallet
                $this->creditResellerWallet($order);
                $completedCount++;
            }
        }

        // If any orders were completed, redirect to main order list
        if ($targetStatus == 6 && $completedCount > 0) {
            // Find delivered status slug
            $deliveredStatus = OrderStatus::find(6);
            $slug = $deliveredStatus ? $deliveredStatus->slug : 'all';
            Toastr::success($completedCount . ' orders completed and moved to main order list!', 'Success!');
            return redirect()->route('admin.orders', ['slug' => $slug])->with('message', 'Completed orders moved to main order list.');
        }

        Toastr::success(count($orders) . ' orders status updated successfully!', 'Success!');
        return redirect()->back();
    }

    /**
     * Handle stock change when order status changes.
     */
    private function handleStockChange(Order $order, int $oldStatus, int $newStatus)
    {
        $activeStatuses = [1, 2, 3, 5, 6, 8];
        
        $wasActive = in_array($oldStatus, $activeStatuses);
        $isActive = in_array($newStatus, $activeStatuses);

        // 1) প্রথমবার active status এ ঢুকলে স্টক কমবে
        if ($isActive && !$wasActive) {
            $details = OrderDetails::where('order_id', $order->id)
                ->with('product:id,stock') // Eager load products to avoid N+1
                ->get();

            foreach ($details as $row) {
                if ($row->product) {
                    $row->product->stock = max(0, $row->product->stock - $row->qty);
                    $row->product->save();
                }
            }
        }

        // 2) cancel (11) হলে, যদি আগেরটা active group এ থাকে -> স্টক রিস্টোর
        if ($newStatus == 11 && $wasActive) {
            $details = OrderDetails::where('order_id', $order->id)
                ->with('product:id,stock') // Eager load products to avoid N+1
                ->get();

            foreach ($details as $row) {
                if ($row->product) {
                    $row->product->stock = $row->product->stock + $row->qty;
                    $row->product->save();
                }
            }
        }
    }

    /**
     * Credit reseller wallet when order is delivered.
     */
    private function creditResellerWallet(Order $order)
    {
        // Check if this is a reseller order
        if (!$order->reseller_profit || $order->reseller_profit <= 0) {
            return;
        }

        // Check if already credited
        if ($order->reseller_wallet_credited) {
            return;
        }

        // Get reseller user from order
        $resellerUser = null;
        if ($order->user_id) {
            $resellerUser = User::find($order->user_id);
            // Verify it's a reseller
            if ($resellerUser && 
                ($resellerUser->hasRole('reseller') || 
                 (isset($resellerUser->role) && strtolower($resellerUser->role) === 'reseller'))) {
                // Reseller found via user_id
            } else {
                $resellerUser = null;
            }
        }

        // Fallback: Check customer email (for old orders)
        if (!$resellerUser && $order->customer && $order->customer->email) {
            $resellerUser = User::where('email', $order->customer->email)
                ->where(function($query) {
                    $query->where('role', 'reseller')
                          ->orWhereHas('roles', function($q) {
                              $q->where('name', 'reseller');
                          });
                })
                ->first();
        }

        if (!$resellerUser) {
            return;
        }

        $resellerProfit = (float) $order->reseller_profit;
        
        if ($resellerProfit > 0) {
            // Update reseller wallet balance
            $resellerUser->wallet_balance = ($resellerUser->wallet_balance ?? 0) + $resellerProfit;
            $resellerUser->save();

            \App\Models\ResellerWalletTransaction::log(
                $resellerUser->id, 'order_profit', $resellerProfit,
                'Order', $order->id,
                'অর্ডার #' . ($order->invoice_id ?? $order->id) . ' প্রফিট'
            );

            // Mark order as credited to avoid double credit
            $order->reseller_wallet_credited = true;
            $order->save();
        }
    }
}
