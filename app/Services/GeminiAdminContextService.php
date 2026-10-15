<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Customer;
use App\Models\EmployeeSalary;
use App\Models\Expense;
use App\Models\FundTransaction;
use App\Models\GeneralSetting;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\OrderStatus;
use App\Models\Product;
use App\Models\Review;
use App\Models\Vendor;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class GeminiAdminContextService
{
    protected const STATIC_CACHE_KEY = 'gemini_admin_static_context_v4';

    public function __construct(
        protected GeminiCodebaseContextService $codebaseContext
    ) {}

    /** @var array<int, string> */
    protected array $keyTables = [
        'orders',
        'order_details',
        'products',
        'customers',
        'vendors',
        'categories',
        'subcategories',
        'childcategories',
        'shippings',
        'payments',
        'general_settings',
        'reviews',
        'coupons',
        'users',
        'sale_notifications',
        'notification_settings',
        'gemini_ai_settings',
    ];

    /** @var array<string, string> */
    protected array $orderStatusMap = [
        '1' => 'Pending',
        '2' => 'Confirmed',
        '3' => 'Processing',
        '4' => 'Picked',
        '5' => 'Shipped',
        '6' => 'Delivered',
        '7' => 'Cancelled',
    ];

    public function buildSystemInstruction(?string $userMessage = null): string
    {
        $static = Cache::remember(self::STATIC_CACHE_KEY, 3600, function () {
            return $this->compileStaticContext();
        });

        $liveStats = $this->liveStats();
        $queryContext = $userMessage ? $this->buildQueryContext($userMessage) : '';
        $codebaseIndex = $this->codebaseContext->buildCodebaseIndex();
        $codeSnippets = $this->codebaseContext->searchRelevantFiles($userMessage);
        $timestamp = now()->format('Y-m-d H:i:s');

        $statsJson = json_encode($liveStats, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $queryBlock = $queryContext !== '' ? "\n\n## Question-specific database data\n{$queryContext}" : '';
        $codeBlock = $codeSnippets !== '' ? "\n\n{$codeSnippets}" : '';

        return <<<INSTRUCTION
{$static}

## Codebase access (Laravel project source)
You have read-only access to this website's full Laravel source code index below.
Use it to explain how features work, find the right controller/route/file, debug errors, and suggest exact fixes.
- Project files live under: app/, routes/, resources/views/backEnd/, config/
- When admin asks how something works or reports a bug, cite exact file paths and methods from the index/snippets.
- You may suggest code changes with file path and line references — admin will apply them.
- NEVER say you cannot see the codebase when the index/snippets are provided below.
- Do NOT expose .env secrets, API keys, or passwords. Config values only as names, not values.

{$codebaseIndex}
{$codeBlock}

## Live database stats (real-time snapshot at {$timestamp})
The backend queried the database moments ago. Use these numbers as facts.
{$statsJson}
{$queryBlock}

## How to answer data questions
- You HAVE read-only access to this site's database via the snapshots above. The server runs fresh queries for every admin message.
- NEVER say you lack database access, cannot query data, or have no connection to the site — use the stats and question-specific sections.
- Answer counts, revenue, order status, product stock, etc. directly from the provided JSON.
- If the admin asks for something not in the snapshot, share related data you do have and name the exact admin menu path to find the rest.
- Do not invent numbers. If a field is missing from the snapshot, say so honestly.
- Never expose full customer phone numbers — mask middle digits (e.g. 017****5678).
- For destructive actions (delete, bulk update), explain risks and recommend the admin UI instead of raw SQL.
- Reply in the same language the admin uses (Bengali বাংলা or English). Be practical and use bullet points for steps.

## Profit & loss (লাভ-ক্ষতি) — IMPORTANT
- The "finance" object in live stats contains PRE-CALCULATED profit/loss for today, this month, this year, and all time.
- Formula (same as Reports → Profit & Loss): Sales − COGS (purchase_price × qty) = Gross Profit; Gross Profit − Expenses = Net Profit.
- When admin asks about profit, loss, লাভ, ক্ষতি, আয়-খরচ, or ব্যবসার হিসাব — answer DIRECTLY with the BDT numbers from finance.periods.
- Clearly state net profit or net loss. Example: "এই মাসে নিট লাভ ১২,৫০০ টাকা" or "আজ ক্ষতি ২,০০০ টাকা".
- Also mention finance.delivered_gross_profit for delivered-order gross margin (dashboard style).
- Point to detailed report: Admin → Reports → Profit & Loss (/admin/reports/profit-loss).
- NEVER refuse profit/loss questions when finance data is present in the JSON below.
INSTRUCTION;
    }

    public function refreshContext(): void
    {
        Cache::forget(self::STATIC_CACHE_KEY);
        Cache::forget('gemini_admin_system_context_v1');
        $this->codebaseContext->refreshIndex();
    }

    protected function compileStaticContext(): string
    {
        $gs = GeneralSetting::first();
        $siteName = $gs->name ?? config('app.name', 'E-commerce');
        $siteUrl = url('/');
        $siteInfo = $this->siteSettingsSummary($gs);
        $schema = $this->databaseSchemaSummary();
        $modules = $this->adminModulesGuide();

        return <<<STATIC
You are "Gemini Admin Assistant" — an expert internal support AI for the Bangladesh e-commerce admin panel of "{$siteName}".

Your job: help admins manage the website, understand live data, troubleshoot issues, solve technical problems, and complete admin tasks using both database snapshots and source code knowledge.

## Website
- Name: {$siteName}
- URL: {$siteUrl}
- Admin panel: {$siteUrl}/admin
- Stack: Laravel PHP e-commerce (orders, products, vendors, customers, courier, payments, SEO, Gemini AI)

## Site settings (from database)
{$siteInfo}

## Database schema summary
{$schema}

## Admin panel modules & how to help
{$modules}
STATIC;
    }

    protected function siteSettingsSummary(?GeneralSetting $gs): string
    {
        if (! $gs) {
            return 'General settings not loaded.';
        }

        $lines = [];
        $fields = [
            'name' => 'Site name',
            'phone' => 'Phone',
            'email' => 'Email',
            'address' => 'Address',
            'facebook' => 'Facebook',
            'whatsapp' => 'WhatsApp',
        ];

        foreach ($fields as $key => $label) {
            $value = trim((string) ($gs->{$key} ?? ''));
            if ($value !== '') {
                $lines[] = "- {$label}: {$value}";
            }
        }

        return $lines !== [] ? implode("\n", $lines) : 'No general settings fields found.';
    }

    protected function liveStats(): array
    {
        try {
            $today = Carbon::today();
            $statusBreakdown = Order::select('order_status', DB::raw('count(*) as total'))
                ->groupBy('order_status')
                ->pluck('total', 'order_status')
                ->mapWithKeys(function ($count, $status) {
                    $key = (string) $status;
                    $label = $this->orderStatusMap[$key] ?? "Status {$key}";

                    return [$label => (int) $count];
                })
                ->all();

            $recentOrders = Order::query()
                ->latest('id')
                ->limit(8)
                ->get(['id', 'invoice_id', 'amount', 'order_status', 'created_at'])
                ->map(function (Order $order) {
                    $statusKey = (string) $order->order_status;

                    return [
                        'id'          => $order->id,
                        'invoice_id'  => $order->invoice_id,
                        'amount'      => (int) $order->amount,
                        'status'      => $this->orderStatusMap[$statusKey] ?? $statusKey,
                        'created_at'  => optional($order->created_at)->format('Y-m-d H:i'),
                    ];
                })
                ->values()
                ->all();

            $lowStockProducts = Product::query()
                ->where('stock', '<', 10)
                ->orderBy('stock')
                ->limit(10)
                ->get(['id', 'name', 'stock', 'status', 'approval_status'])
                ->map(static fn (Product $p) => [
                    'id'     => $p->id,
                    'name'   => $p->name,
                    'stock'  => (int) $p->stock,
                    'status' => (int) $p->status,
                ])
                ->values()
                ->all();

            $pendingProducts = Product::query()
                ->where('approval_status', 'pending')
                ->latest('id')
                ->limit(8)
                ->get(['id', 'name', 'vendor_id', 'created_at'])
                ->map(static fn (Product $p) => [
                    'id'        => $p->id,
                    'name'      => $p->name,
                    'vendor_id' => $p->vendor_id,
                    'created'   => optional($p->created_at)->format('Y-m-d'),
                ])
                ->values()
                ->all();

            $topCategories = Category::withCount('products')
                ->orderByDesc('products_count')
                ->limit(8)
                ->get(['id', 'name'])
                ->map(static fn (Category $c) => [
                    'name'     => $c->name,
                    'products' => (int) $c->products_count,
                ])
                ->values()
                ->all();

            return [
                'orders' => [
                    'total'              => Order::count(),
                    'today'              => Order::whereDate('created_at', $today)->count(),
                    'pending'            => Order::whereNotIn('order_status', ['6', '7'])->count(),
                    'delivered_total'    => Order::where('order_status', '6')->count(),
                    'delivered_today'    => Order::where('order_status', '6')->whereDate('updated_at', $today)->count(),
                    'cancelled_total'    => Order::where('order_status', '7')->count(),
                    'by_status'          => $statusBreakdown,
                    'recent'             => $recentOrders,
                ],
                'revenue' => [
                    'total_delivered_bdt'     => (int) Order::where('order_status', '6')->sum('amount'),
                    'today_all_orders_bdt'    => (int) Order::whereDate('created_at', $today)->sum('amount'),
                    'today_delivered_bdt'     => (int) Order::where('order_status', '6')->whereDate('updated_at', $today)->sum('amount'),
                    'this_month_delivered_bdt' => (int) Order::where('order_status', '6')
                        ->whereYear('updated_at', $today->year)
                        ->whereMonth('updated_at', $today->month)
                        ->sum('amount'),
                ],
                'products' => [
                    'total'             => Product::count(),
                    'active_approved'   => Product::where('status', 1)->where('approval_status', 'approved')->count(),
                    'pending_approval'  => Product::where('approval_status', 'pending')->count(),
                    'low_stock_count'   => Product::where('stock', '<', 10)->count(),
                    'low_stock_samples' => $lowStockProducts,
                    'pending_samples'   => $pendingProducts,
                ],
                'vendors' => [
                    'active'   => Vendor::where('status', 1)->count(),
                    'approved' => Vendor::where('status', 1)->where('verification_status', 'approved')->count(),
                    'pending'  => Vendor::where('status', 1)->where('verification_status', '!=', 'approved')->count(),
                ],
                'customers' => [
                    'total' => Customer::count(),
                    'today' => Customer::whereDate('created_at', $today)->count(),
                ],
                'reviews' => [
                    'pending' => Review::where('status', 'pending')->count(),
                ],
                'categories' => [
                    'total' => Category::count(),
                    'top'   => $topCategories,
                ],
                'finance' => $this->financialStats(),
            ];
        } catch (\Throwable $e) {
            return ['error' => 'Could not load live stats: ' . $e->getMessage()];
        }
    }

    protected function financialStats(): array
    {
        try {
            $now = Carbon::now();
            $todayStart = $now->copy()->startOfDay();
            $todayEnd = $now->copy()->endOfDay();
            $monthStart = $now->copy()->startOfMonth();
            $monthEnd = $now->copy()->endOfMonth();
            $yearStart = $now->copy()->startOfYear();
            $yearEnd = $now->copy()->endOfYear();

            $fundIn = (int) FundTransaction::where('direction', 'in')->sum('amount');
            $fundOut = (int) FundTransaction::where('direction', 'out')->sum('amount');

            $salariesThisMonth = 0;
            if (Schema::hasTable('employee_salaries')) {
                $salariesThisMonth = (int) EmployeeSalary::query()
                    ->whereYear('salary_month', $now->year)
                    ->whereMonth('salary_month', $now->month)
                    ->sum('net_salary');
            }

            return [
                'currency'        => 'BDT',
                'report_path'     => url('/admin/reports/profit-loss'),
                'report_note'     => 'Same calculation as Admin → Reports → Profit & Loss',
                'fund_balance_bdt' => $fundIn - $fundOut,
                'total_expenses_all_time_bdt' => (int) Expense::sum('amount'),
                'admin_commission_all_time_bdt' => (int) OrderDetails::sum('admin_commission'),
                'reseller_profit_all_time_bdt' => Schema::hasColumn('orders', 'reseller_profit')
                    ? (int) Order::sum('reseller_profit')
                    : 0,
                'employee_salaries_this_month_bdt' => $salariesThisMonth,
                'periods' => [
                    'today'      => $this->calculateProfitLoss($todayStart, $todayEnd, 'Today'),
                    'this_month' => $this->calculateProfitLoss($monthStart, $monthEnd, 'This month'),
                    'this_year'  => $this->calculateProfitLoss($yearStart, $yearEnd, 'This year'),
                    'all_time'   => $this->calculateProfitLoss(
                        Carbon::create(2000, 1, 1)->startOfDay(),
                        $now->copy()->endOfDay(),
                        'All time'
                    ),
                ],
                'delivered_gross_profit' => [
                    'note' => 'Delivered orders only (status 6), by delivery/update date — matches dashboard',
                    'today'      => $this->calculateDeliveredGrossProfit($todayStart, $todayEnd),
                    'this_month' => $this->calculateDeliveredGrossProfit($monthStart, $monthEnd),
                    'this_year'  => $this->calculateDeliveredGrossProfit($yearStart, $yearEnd),
                ],
            ];
        } catch (\Throwable $e) {
            return ['error' => 'Could not load financial stats: ' . $e->getMessage()];
        }
    }

    /**
     * @return array<string, int|string|bool>
     */
    protected function calculateProfitLoss(Carbon $from, Carbon $to, string $label): array
    {
        $ordersQuery = Order::query()->whereBetween('created_at', [$from, $to]);

        if (Schema::hasColumn('orders', 'status')) {
            $ordersQuery->where('status', '!=', 'canceled');
        }

        $ordersQuery->where('order_status', '!=', '7');

        $orders = $ordersQuery->get(['id', 'amount', 'discount', 'shipping_charge', 'reseller_profit']);
        $orderIds = $orders->pluck('id');

        $sales = (float) $orders->sum(fn (Order $order) => $this->resolveOrderTotal($order));

        $cogs = 0.0;
        if ($orderIds->isNotEmpty()) {
            $details = OrderDetails::query()
                ->whereIn('order_id', $orderIds)
                ->with('product:id,purchase_price')
                ->get(['id', 'order_id', 'qty', 'purchase_price', 'product_id']);

            foreach ($details as $detail) {
                $purchasePrice = $detail->purchase_price ?? ($detail->product->purchase_price ?? 0);
                $cogs += (float) $purchasePrice * (float) ($detail->qty ?? 0);
            }
        }

        $expenseQuery = Expense::query();
        if (Schema::hasColumn('expenses', 'expense_date')) {
            $expenseQuery->whereBetween('expense_date', [$from->toDateString(), $to->toDateString()]);
        } else {
            $expenseQuery->whereBetween('created_at', [$from, $to]);
        }

        $expenses = (float) $expenseQuery->sum('amount');
        $grossProfit = $sales - $cogs;
        $netProfit = $grossProfit - $expenses;

        return [
            'label'            => $label,
            'from'             => $from->format('Y-m-d'),
            'to'               => $to->format('Y-m-d'),
            'orders_count'     => $orders->count(),
            'sales_bdt'        => (int) round($sales),
            'cogs_bdt'         => (int) round($cogs),
            'gross_profit_bdt' => (int) round($grossProfit),
            'expenses_bdt'     => (int) round($expenses),
            'net_profit_bdt'   => (int) round($netProfit),
            'is_profit'        => $netProfit >= 0,
            'result'           => $netProfit >= 0 ? 'profit' : 'loss',
        ];
    }

    /**
     * @return array<string, int|string>
     */
    protected function calculateDeliveredGrossProfit(Carbon $from, Carbon $to): array
    {
        $orders = Order::query()
            ->where('order_status', '6')
            ->whereBetween('updated_at', [$from, $to])
            ->get(['id', 'amount']);

        $orderIds = $orders->pluck('id');
        $sales = (float) $orders->sum('amount');

        $cogs = 0.0;
        if ($orderIds->isNotEmpty()) {
            $details = OrderDetails::query()
                ->whereIn('order_id', $orderIds)
                ->with('product:id,purchase_price')
                ->get(['order_id', 'qty', 'purchase_price', 'product_id']);

            foreach ($details as $detail) {
                $purchasePrice = $detail->purchase_price ?? ($detail->product->purchase_price ?? 0);
                $cogs += (float) $purchasePrice * (float) ($detail->qty ?? 0);
            }
        }

        $gross = $sales - $cogs;

        return [
            'delivered_orders' => $orders->count(),
            'sales_bdt'        => (int) round($sales),
            'cogs_bdt'         => (int) round($cogs),
            'gross_profit_bdt' => (int) round($gross),
            'is_profit'        => $gross >= 0,
        ];
    }

    protected function resolveOrderTotal(Order $order): float
    {
        if (isset($order->amount) && is_numeric($order->amount)) {
            return (float) $order->amount;
        }

        return 0.0;
    }

    protected function buildQueryContext(string $message): string
    {
        $sections = [];
        $normalized = mb_strtolower($message);

        if ($this->matchesAny($normalized, [
            'order', 'অর্ডার', 'invoice', 'ইনভয়েস', 'delivery', 'ডেলিভারি', 'ship', 'pending', 'cancel',
            'revenue', 'sales', 'বিক্র', 'আয়', 'রেভিনিউ', 'হয়েছে', 'কত', 'টাকা',
        ])) {
            $sections[] = $this->orderDeepContext();
        }

        if ($this->matchesAny($normalized, [
            'product', 'প্রোডাক্ট', 'stock', 'স্টক', 'category', 'ক্যাটাগরি', 'approve', 'অ্যাপ্রুভ',
        ])) {
            $sections[] = $this->productDeepContext();
        }

        if ($this->matchesAny($normalized, [
            'vendor', 'ভেন্ডর', 'merchant', 'দোকান', 'shop',
        ])) {
            $sections[] = $this->vendorDeepContext();
        }

        if ($this->matchesAny($normalized, [
            'customer', 'কাস্টমার', 'গ্রাহক', 'user', 'ইউজার',
        ])) {
            $sections[] = $this->customerDeepContext();
        }

        if ($this->matchesAny($normalized, [
            'profit', 'loss', 'লাভ', 'ক্ষতি', 'খরচ', 'expense', 'finance', 'আর্থিক', 'হিসাব',
            'p&l', 'pnl', 'gross', 'net', 'cogs', 'ব্যবসা', 'আয়', 'ব্যয়', 'মুনাফা',
        ])) {
            $sections[] = $this->financialDeepContext();
        }

        if (preg_match('/\b(?:inv[-_]?)?[a-z0-9]{4,}\b/i', $message, $invoiceMatch)) {
            $lookup = $this->lookupOrderByReference($invoiceMatch[0]);
            if ($lookup !== null) {
                $sections[] = $lookup;
            }
        }

        if (preg_match('/\b(\d{1,6})\b/', $message, $idMatch) && $this->matchesAny($normalized, ['order', 'অর্ডার', 'id'])) {
            $lookup = $this->lookupOrderById((int) $idMatch[1]);
            if ($lookup !== null) {
                $sections[] = $lookup;
            }
        }

        return implode("\n\n", array_filter($sections));
    }

    protected function orderDeepContext(): string
    {
        $lines = ['### Orders (extended)'];

        try {
            $statuses = OrderStatus::query()->orderBy('id')->get(['id', 'name', 'slug']);
            foreach ($statuses as $status) {
                $count = Order::where('order_status', (string) $status->id)->count();
                $lines[] = "- {$status->name} (id {$status->id}): {$count} orders";
            }

            $last7 = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);
                $last7[] = $date->format('M d') . ': ' . Order::whereDate('created_at', $date)->count() . ' orders';
            }
            $lines[] = 'Last 7 days new orders: ' . implode(', ', $last7);

            $traffic = Order::query()
                ->select('traffic_source', DB::raw('count(*) as total'))
                ->whereNotNull('traffic_source')
                ->groupBy('traffic_source')
                ->orderByDesc('total')
                ->limit(8)
                ->get();

            if ($traffic->isNotEmpty()) {
                $lines[] = 'Traffic sources: ' . $traffic->map(fn ($row) => ($row->traffic_source ?: 'unknown') . '=' . $row->total)->implode(', ');
            }
        } catch (\Throwable $e) {
            $lines[] = 'Extended order data unavailable: ' . $e->getMessage();
        }

        return implode("\n", $lines);
    }

    protected function productDeepContext(): string
    {
        $lines = ['### Products (extended)'];

        try {
            $lines[] = '- Inactive products: ' . Product::where('status', '!=', 1)->count();
            $lines[] = '- Rejected products: ' . Product::where('approval_status', 'rejected')->count();

            $latest = Product::query()
                ->latest('id')
                ->limit(8)
                ->get(['id', 'name', 'price', 'stock', 'approval_status', 'status']);

            foreach ($latest as $product) {
                $lines[] = "- #{$product->id} {$product->name} | price {$product->price} | stock {$product->stock} | {$product->approval_status}";
            }
        } catch (\Throwable $e) {
            $lines[] = 'Extended product data unavailable: ' . $e->getMessage();
        }

        return implode("\n", $lines);
    }

    protected function vendorDeepContext(): string
    {
        $lines = ['### Vendors (extended)'];

        try {
            $latest = Vendor::query()
                ->latest('id')
                ->limit(8)
                ->get(['id', 'shop_name', 'status', 'verification_status']);

            foreach ($latest as $vendor) {
                $lines[] = "- #{$vendor->id} {$vendor->shop_name} | status {$vendor->status} | verification {$vendor->verification_status}";
            }
        } catch (\Throwable $e) {
            $lines[] = 'Extended vendor data unavailable: ' . $e->getMessage();
        }

        return implode("\n", $lines);
    }

    protected function financialDeepContext(): string
    {
        $lines = ['### Finance (extended)'];
        $finance = $this->financialStats();

        if (isset($finance['error'])) {
            return '### Finance (extended)' . "\n" . $finance['error'];
        }

        try {
            $now = Carbon::now();
            $monthStart = $now->copy()->startOfMonth();
            $monthEnd = $now->copy()->endOfMonth();

            $expenseQuery = Expense::query();
            if (Schema::hasColumn('expenses', 'expense_date')) {
                $expenseQuery->whereBetween('expense_date', [$monthStart->toDateString(), $monthEnd->toDateString()]);
            } else {
                $expenseQuery->whereBetween('created_at', [$monthStart, $monthEnd]);
            }

            $byCategory = $expenseQuery
                ->select('category', DB::raw('SUM(amount) as total'))
                ->groupBy('category')
                ->orderByDesc('total')
                ->limit(10)
                ->get();

            if ($byCategory->isNotEmpty()) {
                $lines[] = 'This month expenses by category:';
                foreach ($byCategory as $row) {
                    $lines[] = '- ' . ($row->category ?: 'Uncategorized') . ': ' . (int) $row->total . ' BDT';
                }
            }

            $lines[] = 'Detailed report: ' . ($finance['report_path'] ?? url('/admin/reports/profit-loss'));
        } catch (\Throwable $e) {
            $lines[] = 'Extended finance data unavailable: ' . $e->getMessage();
        }

        return implode("\n", $lines);
    }

    protected function customerDeepContext(): string
    {
        $lines = ['### Customers (extended)'];

        try {
            $latest = Customer::query()
                ->latest('id')
                ->limit(8)
                ->get(['id', 'name', 'phone', 'created_at']);

            foreach ($latest as $customer) {
                $lines[] = "- #{$customer->id} {$customer->name} | phone " . $this->maskPhone((string) $customer->phone) . ' | joined ' . optional($customer->created_at)->format('Y-m-d');
            }
        } catch (\Throwable $e) {
            $lines[] = 'Extended customer data unavailable: ' . $e->getMessage();
        }

        return implode("\n", $lines);
    }

    protected function lookupOrderByReference(string $reference): ?string
    {
        try {
            $order = Order::query()
                ->where('invoice_id', 'like', '%' . $reference . '%')
                ->with(['shipping:id,order_id,name,phone', 'status:id,name'])
                ->first();

            if (! $order) {
                return null;
            }

            return $this->formatOrderLookup($order);
        } catch (\Throwable) {
            return null;
        }
    }

    protected function lookupOrderById(int $id): ?string
    {
        try {
            $order = Order::query()
                ->where('id', $id)
                ->with(['shipping:id,order_id,name,phone', 'status:id,name'])
                ->first();

            if (! $order) {
                return null;
            }

            return $this->formatOrderLookup($order);
        } catch (\Throwable) {
            return null;
        }
    }

    protected function formatOrderLookup(Order $order): string
    {
        $statusKey = (string) $order->order_status;
        $statusName = $order->status->name ?? ($this->orderStatusMap[$statusKey] ?? $statusKey);
        $phone = $this->maskPhone((string) optional($order->shipping)->phone);
        $customerName = optional($order->shipping)->name ?: 'N/A';
        $itemCount = OrderDetails::where('order_id', $order->id)->count();

        return implode("\n", [
            '### Matched order lookup',
            "- Order ID: {$order->id}",
            "- Invoice: {$order->invoice_id}",
            "- Amount: {$order->amount} BDT",
            "- Status: {$statusName}",
            "- Customer: {$customerName}",
            "- Phone: {$phone}",
            "- Items: {$itemCount}",
            '- Created: ' . optional($order->created_at)->format('Y-m-d H:i'),
        ]);
    }

    protected function maskPhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?: $phone;
        $len = strlen($digits);

        if ($len < 6) {
            return $phone;
        }

        return substr($digits, 0, 3) . str_repeat('*', max(3, $len - 6)) . substr($digits, -3);
    }

    /**
     * @param  array<int, string>  $needles
     */
    protected function matchesAny(string $haystack, array $needles): bool
    {
        foreach ($needles as $needle) {
            if ($needle !== '' && str_contains($haystack, mb_strtolower($needle))) {
                return true;
            }
        }

        return false;
    }

    protected function databaseSchemaSummary(): string
    {
        $lines = [];

        try {
            $allTables = $this->allTableNames();
            $lines[] = 'All tables (' . count($allTables) . '): ' . implode(', ', array_slice($allTables, 0, 80));
            if (count($allTables) > 80) {
                $lines[] = '... and ' . (count($allTables) - 80) . ' more tables';
            }
            $lines[] = '';
            $lines[] = 'Key table columns:';

            foreach ($this->keyTables as $table) {
                if (! Schema::hasTable($table)) {
                    continue;
                }
                $columns = Schema::getColumnListing($table);
                $lines[] = "- {$table}: " . implode(', ', array_slice($columns, 0, 40));
                if (count($columns) > 40) {
                    $lines[] = '  ... +' . (count($columns) - 40) . ' more columns';
                }
            }
        } catch (\Throwable $e) {
            $lines[] = 'Schema unavailable: ' . $e->getMessage();
        }

        return implode("\n", $lines);
    }

    protected function allTableNames(): array
    {
        $database = DB::connection()->getDatabaseName();
        $rows = DB::select('SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = ? ORDER BY TABLE_NAME', [$database]);

        return array_map(static fn ($row) => $row->TABLE_NAME, $rows);
    }

    protected function adminModulesGuide(): string
    {
        return <<<'GUIDE'
- Dashboard: overview, new orders popup
- Orders: all orders, statuses, incomplete orders, reseller orders, POS, fraud check (BD Courier), duplicate order check, AI risk assessment
- Products: inhouse/vendor products, pending approval, wholesale, categories, brands, colors, sizes
- Vendors: vendor shops, verification, withdrawals
- Customers: customer list, IP block
- Site Setting: general settings, sales notification popup, pages, contact, social media
- API Integration: payment gateway, SMS, courier API, Facebook CAPI, Gemini AI (API key)
- Gemini AI: API settings, Admin Chat Assistant (this chat)
- Banner & Sliders, Popup Offer, SEO settings
- Coupons, Reports (including Profit & Loss at /admin/reports/profit-loss), Delivery module, Fraud API settings
- Product create/edit: "AI দিয়ে লিখুন" generates Bengali SEO descriptions

Common admin tasks you can explain:
1. How to approve vendor products (Products → Pending)
2. How to configure Gemini API (API Integration → Gemini AI)
3. How to check customer fraud before shipping (Orders → fraud check / AI risk)
4. How to enable sales notification popup (Site Setting → Sales Notification)
5. How to manage order status workflow
6. How wholesale products and variants work
7. How to fix code errors — identify controller/service/view file and suggest the change
8. Profit & Loss report at Reports → Profit & Loss
GUIDE;
    }
}
