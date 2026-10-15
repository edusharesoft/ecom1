<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use App\Models\Order;
use App\Models\OrderDetails;   // ✅ এইটাই এখন ইউজ হবে
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Expense;

class ReportController extends Controller
{
    /**
     * Common date range helper
     * type = today / month / year / range
     */
    protected function getDateRange(Request $request)
    {
        $type   = $request->get('type', 'today'); // default: today
        $now    = Carbon::now();
        $from   = null;
        $to     = null;
        $label  = '';

        if ($type === 'today') {
            $from  = $now->copy()->startOfDay();
            $to    = $now->copy()->endOfDay();
            $label = 'Today - ' . $now->format('d M, Y');
        } elseif ($type === 'month') {
            $year  = (int) $request->get('year', $now->year);
            $month = (int) $request->get('month', $now->month);

            $from  = Carbon::create($year, $month, 1)->startOfDay();
            $to    = $from->copy()->endOfMonth();
            $label = 'Month - ' . $from->format('F Y');
        } elseif ($type === 'year') {
            $year  = (int) $request->get('year', $now->year);
            $from  = Carbon::create($year, 1, 1)->startOfDay();
            $to    = Carbon::create($year, 12, 31)->endOfDay();
            $label = 'Year - ' . $year;
        } else { // range
            $fromInput = $request->get('from_date');
            $toInput   = $request->get('to_date');

            $from = $fromInput
                ? Carbon::parse($fromInput)->startOfDay()
                : $now->copy()->startOfMonth();

            $to = $toInput
                ? Carbon::parse($toInput)->endOfDay()
                : $now->copy()->endOfDay();

            $label = 'From ' . $from->format('d M, Y') . ' To ' . $to->format('d M, Y');
        }

        return [$from, $to, $label, $type];
    }

    /**
     * অর্ডার থেকে numeric shipping amount বের করার helper
     */
    protected function resolveOrderShipping($order)
    {
        if (isset($order->shipping_amount) && is_numeric($order->shipping_amount)) {
            return (float) $order->shipping_amount;
        }

        if (isset($order->shipping_charge) && is_numeric($order->shipping_charge)) {
            return (float) $order->shipping_charge;
        }

        if (isset($order->shipping_cost) && is_numeric($order->shipping_cost)) {
            return (float) $order->shipping_cost;
        }

        if (isset($order->shipping) && is_numeric($order->shipping)) {
            return (float) $order->shipping;
        }

        return 0.0;
    }

    /**
     * অর্ডার থেকে numeric total বের করার helper
     * 👉 তোমার DB অনুসারে মূল টোটাল কলামটা `amount`
     */
    protected function resolveOrderTotal($order)
    {
        if (isset($order->amount) && is_numeric($order->amount)) {
            return (float) $order->amount;
        }

        if (isset($order->total) && is_numeric($order->total)) {
            return (float) $order->total;
        }

        if (isset($order->total_amount) && is_numeric($order->total_amount)) {
            return (float) $order->total_amount;
        }

        if (isset($order->grand_total) && is_numeric($order->grand_total)) {
            return (float) $order->grand_total;
        }

        if (isset($order->subtotal) && is_numeric($order->subtotal)) {
            return (float) $order->subtotal;
        }

        return 0.0;
    }

    /**
     * PURCHASE helper গুলো
     */
    protected function resolvePurchaseTotal($purchase)
    {
        if (isset($purchase->total) && is_numeric($purchase->total)) {
            return (float) $purchase->total;
        }
        if (isset($purchase->grand_total) && is_numeric($purchase->grand_total)) {
            return (float) $purchase->grand_total;
        }
        if (isset($purchase->total_amount) && is_numeric($purchase->total_amount)) {
            return (float) $purchase->total_amount;
        }
        if (isset($purchase->amount) && is_numeric($purchase->amount)) {
            return (float) $purchase->amount;
        }
        return 0.0;
    }

    protected function resolvePurchasePaid($purchase)
    {
        if (isset($purchase->paid) && is_numeric($purchase->paid)) {
            return (float) $purchase->paid;
        }
        if (isset($purchase->paid_amount) && is_numeric($purchase->paid_amount)) {
            return (float) $purchase->paid_amount;
        }
        if (isset($purchase->payment) && is_numeric($purchase->payment)) {
            return (float) $purchase->payment;
        }
        return 0.0;
    }

    protected function resolvePurchaseDue($purchase)
    {
        if (isset($purchase->due) && is_numeric($purchase->due)) {
            return (float) $purchase->due;
        }
        if (isset($purchase->due_amount) && is_numeric($purchase->due_amount)) {
            return (float) $purchase->due_amount;
        }
        if (isset($purchase->balance) && is_numeric($purchase->balance)) {
            return (float) $purchase->balance;
        }
        return 0.0;
    }

    /* =======================
     *  ORDER REPORT
     * ======================= */
    public function orders(Request $request)
    {
        [$from, $to, $label, $type] = $this->getDateRange($request);

$query = Order::whereBetween('created_at', [$from, $to])
    ->orderBy('created_at', 'desc');

$orders = $query->paginate(20)->withQueryString();

        $totalOrders = $orders->count();

        // Total / Discount / Shipping summary
        $totalAmount = $orders->sum(function ($order) {
            return $this->resolveOrderTotal($order);
        });

        $totalDiscount = $orders->sum(function ($order) {
            if (isset($order->discount) && is_numeric($order->discount)) {
                return (float) $order->discount;
            }
            if (isset($order->discount_amount) && is_numeric($order->discount_amount)) {
                return (float) $order->discount_amount;
            }
            if (isset($order->coupon_discount) && is_numeric($order->coupon_discount)) {
                return (float) $order->coupon_discount;
            }
            return 0.0;
        });

        $totalShipping = $orders->sum(function ($order) {
            return $this->resolveOrderShipping($order);
        });

        // CSV Export
        if ($request->get('export') === 'csv') {
            $fileName = 'order-report-' . now()->format('Ymd_His') . '.csv';

            $headers = [
                'Content-Type'        => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$fileName\"",
            ];

            $self = $this;

            $callback = function () use ($orders, $label, $self) {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, ['Order Report', $label]);
                fputcsv($handle, []);
                fputcsv($handle, ['Invoice', 'Customer', 'Total', 'Discount', 'Shipping', 'Status', 'Date']);

                foreach ($orders as $order) {
                    $shipping = $self->resolveOrderShipping($order);
                    $total    = $self->resolveOrderTotal($order);

                    $discount = 0.0;
                    if (isset($order->discount) && is_numeric($order->discount)) {
                        $discount = (float) $order->discount;
                    } elseif (isset($order->discount_amount) && is_numeric($order->discount_amount)) {
                        $discount = (float) $order->discount_amount;
                    } elseif (isset($order->coupon_discount) && is_numeric($order->coupon_discount)) {
                        $discount = (float) $order->coupon_discount;
                    }

                    fputcsv($handle, [
                        $order->invoice_id ?? $order->id,
                        $order->customer_name ?? ($order->customer->name ?? ''),
                        $total,
                        $discount,
                        $shipping,
                        is_object($order->status) ? ($order->status->name ?? '') : ($order->status ?? ''),
                        optional($order->created_at)->format('Y-m-d H:i'),
                    ]);
                }

                fclose($handle);
            };

            return response()->stream($callback, 200, $headers);
        }

        return view('backEnd.reports.orders', compact(
            'orders',
            'from',
            'to',
            'label',
            'type',
            'totalOrders',
            'totalAmount',
            'totalDiscount',
            'totalShipping'
        ));
    }

    /* =======================
     *  PURCHASE REPORT
     * ======================= */
    public function purchases(Request $request)
    {
        [$from, $to, $label, $type] = $this->getDateRange($request);

        $query = Purchase::query();

        // purchase_date থাকলে সেটা ব্যবহার, না থাকলে created_at
        if (Schema::hasColumn('purchases', 'purchase_date')) {
            $query->whereBetween('purchase_date', [$from->toDateString(), $to->toDateString()]);
        } else {
            $query->whereBetween('created_at', [$from, $to]);
        }

 $purchases = $query->orderBy('id', 'desc')
                   ->paginate(20)
                   ->withQueryString();
        // summary
        $totalPurchaseAmount = $purchases->sum(function ($p) {
            return $this->resolvePurchaseTotal($p);
        });

        $totalPaid = $purchases->sum(function ($p) {
            return $this->resolvePurchasePaid($p);
        });

        $totalDue = $purchases->sum(function ($p) {
            return $this->resolvePurchaseDue($p);
        });

        // Blade-এ দেখানোর জন্য ইন-মেমরি ভ্যালু সেট করে দিচ্ছি
        foreach ($purchases as $p) {
            $p->total = $this->resolvePurchaseTotal($p);
            $p->paid  = $this->resolvePurchasePaid($p);
            $p->due   = $this->resolvePurchaseDue($p);
        }

        // CSV Export
        if ($request->get('export') === 'csv') {
            $fileName = 'purchase-report-' . now()->format('Ymd_His') . '.csv';

            $headers = [
                'Content-Type'        => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$fileName\"",
            ];

            $callback = function () use ($purchases, $label) {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, ['Purchase Report', $label]);
                fputcsv($handle, []);
                fputcsv($handle, ['Invoice', 'Supplier', 'Total', 'Paid', 'Due', 'Date']);

                foreach ($purchases as $p) {
                    $dateValue = $p->purchase_date ?? $p->created_at;
                    $dateStr   = $dateValue ? Carbon::parse($dateValue)->format('Y-m-d') : '';

                    fputcsv($handle, [
                        $p->invoice_no ?? $p->id,
                        $p->supplier->name ?? '',
                        $p->total ?? 0,
                        $p->paid ?? 0,
                        $p->due ?? 0,
                        $dateStr,
                    ]);
                }

                fclose($handle);
            };

            return response()->stream($callback, 200, $headers);
        }

        return view('backEnd.reports.purchases', compact(
            'purchases',
            'from',
            'to',
            'label',
            'type',
            'totalPurchaseAmount',
            'totalPaid',
            'totalDue'
        ));
    }

    /* =======================
     *  EXPENSE REPORT
     * ======================= */
    public function expenses(Request $request)
    {
        [$from, $to, $label, $type] = $this->getDateRange($request);

        $query = Expense::query();

        if (Schema::hasColumn('expenses', 'expense_date')) {
            $query->whereBetween('expense_date', [$from->toDateString(), $to->toDateString()]);
        } else {
            $query->whereBetween('created_at', [$from, $to]);
        }

$expenses = $query->orderBy('id', 'desc')
                  ->paginate(20)
                  ->withQueryString();

$totalExpense = $expenses->sum('amount');


        if ($request->get('export') === 'csv') {
            $fileName = 'expense-report-' . now()->format('Ymd_His') . '.csv';

            $headers = [
                'Content-Type'        => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$fileName\"",
            ];

            $callback = function () use ($expenses, $label) {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, ['Expense Report', $label]);
                fputcsv($handle, []);
                fputcsv($handle, ['Date', 'Title', 'Category', 'Amount', 'Note']);

                foreach ($expenses as $e) {
                    $dateValue = $e->expense_date ?? $e->created_at;
                    $dateStr   = $dateValue ? Carbon::parse($dateValue)->format('Y-m-d') : '';

                    fputcsv($handle, [
                        $dateStr,
                        $e->title,
                        $e->category,
                        $e->amount,
                        $e->note,
                    ]);
                }

                fclose($handle);
            };

            return response()->stream($callback, 200, $headers);
        }

        return view('backEnd.reports.expenses', compact(
            'expenses',
            'from',
            'to',
            'label',
            'type',
            'totalExpense'
        ));
    }

    /* =======================
     *  STOCK REPORT (LIVE STOCK)
     * ======================= */
    public function stock(Request $request)
    {
        $products = Product::orderBy('name')
                   ->paginate(20)
                   ->withQueryString();

        $totalStockQty   = $products->sum('stock');
        $totalStockValue = $products->sum(function ($p) {
            $purchasePrice = $p->purchase_price ?? 0;
            return $purchasePrice * ($p->stock ?? 0);
        });

        if ($request->get('export') === 'csv') {
            $fileName = 'stock-report-' . now()->format('Ymd_His') . '.csv';

            $headers = [
                'Content-Type'        => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$fileName\"",
            ];

            $callback = function () use ($products) {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, ['Stock Report - Live']);
                fputcsv($handle, []);
                fputcsv($handle, ['Product', 'SKU', 'Stock', 'Purchase Price', 'Sale Price', 'Stock Value']);

                foreach ($products as $p) {
                    $purchasePrice = $p->purchase_price ?? 0;
                    $salePrice     = $p->new_price ?? $p->old_price ?? 0;
                    $stock         = $p->stock ?? 0;
                    $stockValue    = $purchasePrice * $stock;

                    fputcsv($handle, [
                        $p->name,
                        $p->sku ?? '',
                        $stock,
                        $purchasePrice,
                        $salePrice,
                        $stockValue,
                    ]);
                }

                fclose($handle);
            };

            return response()->stream($callback, 200, $headers);
        }

        return view('backEnd.reports.stock', compact(
            'products',
            'totalStockQty',
            'totalStockValue'
        ));
    }

    /* =======================
     *  PROFIT & LOSS REPORT
     * ======================= */
    public function profitLoss(Request $request)
    {
        [$from, $to, $label, $type] = $this->getDateRange($request);

        // 1) SALES (Orders)
        $ordersQuery = Order::whereBetween('created_at', [$from, $to]);

        // orders টেবিলে যদি status কলাম থাকে তখনই ফিল্টার করব
        if (Schema::hasColumn('orders', 'status')) {
            $ordersQuery->where('status', '!=', 'canceled');
        }

        $orders = $ordersQuery->get();

        $salesAmount = $orders->sum(function ($order) {
            return $this->resolveOrderTotal($order);
        });

        // 2) COGS (Cost of Goods Sold)
        $orderDetails = OrderDetails::whereIn('order_id', $orders->pluck('id'))
            ->with('product:id,purchase_price') // ✅ Eager load to avoid N+1
            ->get(); // ✅ এখানে plural মডেল

        $cogs = 0;
        foreach ($orderDetails as $od) {
            // order_details টেবিলে purchase_price থাকলে সেটাই use করা ভালো
            $purchasePrice = $od->purchase_price ?? null;

            if ($purchasePrice === null) {
                // fallback – eager loaded product থেকে নিন
                $purchasePrice = $od->product->purchase_price ?? 0;
            }

            $cogs += $purchasePrice * ($od->qty ?? 0);
        }

        // 3) EXPENSES
        $expQuery = Expense::query();
        if (Schema::hasColumn('expenses', 'expense_date')) {
            $expQuery->whereBetween('expense_date', [$from->toDateString(), $to->toDateString()]);
        } else {
            $expQuery->whereBetween('created_at', [$from, $to]);
        }

        $expenses     = $expQuery->get();
        $totalExpense = $expenses->sum('amount');

        // 4) GROSS & NET
        $grossProfit = $salesAmount - $cogs;
        $netProfit   = $grossProfit - $totalExpense;

        if ($request->get('export') === 'csv') {
            $fileName = 'profit-loss-' . now()->format('Ymd_His') . '.csv';

            $headers = [
                'Content-Type'        => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$fileName\"",
            ];

            $callback = function () use ($label, $salesAmount, $cogs, $totalExpense, $grossProfit, $netProfit) {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, ['Profit & Loss Report', $label]);
                fputcsv($handle, []);
                fputcsv($handle, ['Sales', $salesAmount]);
                fputcsv($handle, ['COGS', $cogs]);
                fputcsv($handle, ['Gross Profit', $grossProfit]);
                fputcsv($handle, ['Expenses', $totalExpense]);
                fputcsv($handle, ['Net Profit', $netProfit]);
                fclose($handle);
            };

            return response()->stream($callback, 200, $headers);
        }

        return view('backEnd.reports.profit_loss', compact(
            'from',
            'to',
            'label',
            'type',
            'salesAmount',
            'cogs',
            'totalExpense',
            'grossProfit',
            'netProfit'
        ));
    }
}
