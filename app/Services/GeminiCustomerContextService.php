<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Customer;
use App\Models\GeminiAiSetting;
use App\Models\GeneralSetting;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\ShippingCharge;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class GeminiCustomerContextService
{
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

    public function isEnabled(): bool
    {
        return Cache::remember('gemini_chat_enabled', 300, function () {
            try {
                $setting = GeminiAiSetting::first();

                return $setting
                    && $setting->status
                    && ($setting->customer_chat_enabled ?? true)
                    && $setting->hasApiKey();
            } catch (\Throwable) {
                return false;
            }
        });
    }

    public function welcomeMessage(): string
    {
        $setting = GeminiAiSetting::first();
        $custom = trim((string) ($setting->customer_chat_welcome ?? ''));

        if ($custom !== '') {
            return $custom;
        }

        $site = GeneralSetting::value('name') ?? config('app.name');

        return "স্বাগতম {$site}-এ! আমি আপনার শপিং সহকারী। প্রোডাক্ট খুঁজতে, অর্ডার ট্র্যাক করতে, রিটার্ন/রিফান্ড বা কমপ্লেইন—যেকোনো বিষয়ে জিজ্ঞাসা করুন।";
    }

    public function buildSystemInstruction(string $message, ?Customer $customer = null): string
    {
        $gs = GeneralSetting::first();
        $contact = Contact::first();
        $siteName = $gs->name ?? config('app.name');
        $siteUrl = url('/');

        $policies = $this->storePolicies($contact);
        $products = $this->searchProductsForMessage($message);
        $productJson = json_encode($products, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $queryContext = $this->buildQueryContext($message, $customer);
        $customerBlock = $customer
            ? "Logged-in customer: {$customer->name} (ID {$customer->id}, phone masked)"
            : 'Guest customer (not logged in)';

        return <<<INSTRUCTION
You are "{$siteName} Shopping Assistant" — a friendly 24/7 AI customer support chatbot (like tawk.to live chat).

## Your role
Help customers with: product search, recommendations, comparison, stock/size/price, delivery charge, return & refund policy, order tracking, complaints, and general shopping questions.
Reply naturally in Bengali বাংলা or English — match the customer's language.
Be warm, helpful, and concise. Use bullet points when listing options.

## STRICT privacy rules — NEVER reveal
- Admin panel data, revenue, profit, costs, vendor earnings, commissions
- Other customers' orders or personal data
- Internal business statistics or database structure
- API keys or admin URLs (/admin paths)

## Store info
- Website: {$siteUrl}
- {$policies}

## Customer session
{$customerBlock}

## Matched products from catalog (use these for search/recommend/compare)
{$productJson}

{$queryContext}

## Capabilities guide
1. **Smart search**: Use matched products above. Suggest 2-5 best items with name, price (BDT), stock status, and tell customer to click product cards in chat.
2. **Order tracking**: If order lookup data is above, share status clearly. Otherwise ask for invoice ID or phone used in order.
3. **Complaints**: If customer has an issue, empathize, explain return policy, and tell them to use the "কমপ্লেইন" button in chat or visit {$siteUrl}/complaint
4. **Refund**: Explain they can request refund from account → Refunds after delivery. Link: {$siteUrl}/customer/refunds (login required)
5. **Review summary**: If review summary is above, share it in 2-3 Bengali sentences.
6. **Product comparison**: Compare products from matched list by price, features, stock — recommend based on budget.
7. **Recommendations**: Suggest related products from recommendations section if available.

When mentioning products, always include the product name and price in BDT.
INSTRUCTION;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function searchProductsForMessage(string $message, int $limit = 10): array
    {
        $message = trim($message);
        if ($message === '') {
            return $this->formatProducts(
                Product::query()
                    ->where('status', 1)
                    ->where('approval_status', 'approved')
                    ->with(['image:id,product_id,image', 'category:id,name'])
                    ->latest('id')
                    ->limit(6)
                    ->get()
            );
        }

        $maxPrice = $this->extractMaxPrice($message);
        $minPrice = $this->extractMinPrice($message);
        $terms = $this->extractSearchTerms($message);

        $query = Product::query()
            ->where('status', 1)
            ->where('approval_status', 'approved')
            ->with(['image:id,product_id,image', 'category:id,name']);

        if ($maxPrice !== null) {
            $query->where('new_price', '<=', $maxPrice);
        }
        if ($minPrice !== null) {
            $query->where('new_price', '>=', $minPrice);
        }

        if ($terms !== []) {
            $query->where(function ($q) use ($terms) {
                foreach ($terms as $term) {
                    $q->orWhere('name', 'like', '%' . $term . '%')
                        ->orWhere('description', 'like', '%' . $term . '%')
                        ->orWhere('product_code', 'like', '%' . $term . '%');
                }
            });
        }

        $products = $query->orderByDesc('id')->limit($limit)->get();

        if ($products->isEmpty() && $terms !== []) {
            $products = Product::query()
                ->where('status', 1)
                ->where('approval_status', 'approved')
                ->with(['image:id,product_id,image', 'category:id,name'])
                ->where(function ($q) use ($terms) {
                    foreach ($terms as $term) {
                        $q->orWhere('name', 'like', '%' . $term . '%');
                    }
                })
                ->limit($limit)
                ->get();
        }

        return $this->formatProducts($products);
    }

    protected function buildQueryContext(string $message, ?Customer $customer): string
    {
        $sections = [];
        $normalized = mb_strtolower($message);

        if ($this->matchesAny($normalized, ['order', 'অর্ডার', 'track', 'ট্র্যাক', 'invoice', 'ইনভয়েস', 'ডেলিভারি', 'delivery'])) {
            $lookup = $this->lookupOrdersFromMessage($message, $customer);
            if ($lookup !== '') {
                $sections[] = $lookup;
            }
        }

        if ($this->matchesAny($normalized, ['review', 'রিভিউ', 'রেটিং', 'rating', 'মতামত'])) {
            $summary = $this->reviewSummaryFromMessage($message);
            if ($summary !== '') {
                $sections[] = $summary;
            }
        }

        if ($this->matchesAny($normalized, ['compare', 'তুলনা', 'পার্থক্য', 'vs', 'নাকি', 'বা'])) {
            $compare = $this->compareProductsFromMessage($message);
            if ($compare !== '') {
                $sections[] = $compare;
            }
        }

        if ($this->matchesAny($normalized, ['recommend', 'সাজেস্ট', 'সাজেশন', 'দেখাও', 'কিনলে', 'related', 'একই'])) {
            $recs = $this->recommendationsFromMessage($message);
            if ($recs !== '') {
                $sections[] = $recs;
            }
        }

        if ($customer) {
            $sections[] = $this->customerOrdersSummary($customer);
        }

        return $sections !== [] ? "## Additional context\n" . implode("\n\n", $sections) : '';
    }

    protected function storePolicies(?Contact $contact): string
    {
        $lines = [];
        $gs = GeneralSetting::first();

        if ($gs) {
            if (filled($gs->phone ?? null)) {
                $lines[] = 'Hotline: ' . $gs->phone;
            }
            if (filled($gs->email ?? null)) {
                $lines[] = 'Email: ' . $gs->email;
            }
        }

        if ($contact) {
            if (filled($contact->hotline ?? null)) {
                $lines[] = 'Support hotline: ' . $contact->hotline;
            }
            if (filled($contact->whatsapp ?? null)) {
                $lines[] = 'WhatsApp: ' . $contact->whatsapp;
            }
        }

        $charges = ShippingCharge::query()->orderBy('id')->limit(10)->get();
        if ($charges->isNotEmpty()) {
            $lines[] = 'Delivery charges: ' . $charges->map(fn ($c) => ($c->name ?? 'Area') . '=' . ($c->amount ?? 0) . ' BDT')->implode(', ');
        }

        $lines[] = 'Order track: ' . url('/customer/order-track');
        $lines[] = 'Complaint page: ' . url('/complaint');
        $lines[] = 'Return policy: ৩-৭ দিনের মধ্যে পণ্য ত্রুটিপূর্ণ/ভুল হলে কমপ্লেইন ফর্মে জানান। রিফান্ড লগইন করে অ্যাকাউন্ট → Refunds থেকে করুন।';
        $lines[] = 'Categories: ' . Category::query()->where('status', 1)->limit(15)->pluck('name')->implode(', ');

        return implode("\n", $lines);
    }

    protected function lookupOrdersFromMessage(string $message, ?Customer $customer): string
    {
        $invoice = null;
        $phone = null;

        if (preg_match('/\b(?:inv[-_]?)?[a-z0-9]{4,}\b/i', $message, $m)) {
            $invoice = $m[0];
        }
        if (preg_match('/(?:01[3-9]\d{8})/', preg_replace('/\D+/', '', $message), $pm)) {
            $phone = $pm[0];
        }

        if ($invoice === null && $phone === null && $customer) {
            $phone = preg_replace('/\D+/', '', (string) $customer->phone);
        }

        if ($invoice === null && $phone === null) {
            return '';
        }

        $query = Order::query()->with(['status:id,name', 'shipping:id,order_id,name,phone']);

        if ($invoice) {
            $query->where('invoice_id', 'like', '%' . $invoice . '%');
        }
        if ($phone) {
            $digits = preg_replace('/\D+/', '', $phone);
            $query->whereHas('shipping', fn ($q) => $q->where('phone', 'like', '%' . $digits . '%'));
        }

        $orders = $query->latest('id')->limit(5)->get(['id', 'invoice_id', 'amount', 'order_status', 'created_at']);

        if ($orders->isEmpty()) {
            return "### Order lookup\nNo order found with given info.";
        }

        $lines = ['### Order lookup (customer-safe)'];
        foreach ($orders as $order) {
            $statusKey = (string) $order->order_status;
            $status = $order->status->name ?? ($this->orderStatusMap[$statusKey] ?? $statusKey);
            $lines[] = "- Invoice: {$order->invoice_id} | Amount: {$order->amount} BDT | Status: {$status} | Date: " . optional($order->created_at)->format('d M Y');
        }

        return implode("\n", $lines);
    }

    protected function customerOrdersSummary(Customer $customer): string
    {
        $orders = Order::query()
            ->where('customer_id', $customer->id)
            ->with('status:id,name')
            ->latest('id')
            ->limit(5)
            ->get(['id', 'invoice_id', 'amount', 'order_status', 'created_at']);

        if ($orders->isEmpty()) {
            return "### Your recent orders\nNo orders found on this account.";
        }

        $lines = ['### Your recent orders'];
        foreach ($orders as $order) {
            $statusKey = (string) $order->order_status;
            $status = $order->status->name ?? ($this->orderStatusMap[$statusKey] ?? $statusKey);
            $lines[] = "- {$order->invoice_id}: {$status}, {$order->amount} BDT";
        }

        return implode("\n", $lines);
    }

    protected function reviewSummaryFromMessage(string $message): string
    {
        $product = $this->findProductByMessage($message);
        if (! $product) {
            return '';
        }

        $reviews = Review::query()
            ->where('product_id', $product->id)
            ->where('status', 'active')
            ->latest('id')
            ->limit(30)
            ->get(['ratting', 'review', 'name']);

        if ($reviews->isEmpty()) {
            return "### Reviews for {$product->name}\nNo reviews yet.";
        }

        $avg = round($reviews->avg(fn ($r) => (float) $r->ratting), 1);
        $samples = $reviews->take(8)->map(fn ($r) => '- ' . Str::limit(strip_tags((string) $r->review), 120))->implode("\n");

        return "### Reviews for {$product->name}\nAverage rating: {$avg}/5\nSample reviews:\n{$samples}\n(Summarize these for the customer in Bengali)";
    }

    protected function compareProductsFromMessage(string $message): string
    {
        $products = Product::query()
            ->where('status', 1)
            ->where('approval_status', 'approved')
            ->with('category:id,name')
            ->get(['id', 'name', 'new_price', 'old_price', 'stock', 'category_id', 'description']);

        $matched = [];
        $lower = mb_strtolower($message);

        foreach ($products as $product) {
            $name = mb_strtolower((string) $product->name);
            if (str_contains($lower, $name) || str_contains($name, mb_substr($lower, 0, 20))) {
                $matched[] = $product;
            }
        }

        if (count($matched) < 2) {
            $terms = $this->extractSearchTerms($message);
            foreach ($products as $product) {
                foreach ($terms as $term) {
                    if (strlen($term) >= 3 && str_contains(mb_strtolower($product->name), $term)) {
                        $matched[$product->id] = $product;
                    }
                }
            }
            $matched = array_values($matched);
        }

        if (count($matched) < 2) {
            return '';
        }

        $matched = array_slice($matched, 0, 3);
        $lines = ['### Product comparison data'];
        foreach ($matched as $p) {
            $lines[] = "- {$p->name}: Price {$p->new_price} BDT, Stock {$p->stock}, Category " . ($p->category->name ?? 'N/A');
        }

        return implode("\n", $lines);
    }

    protected function recommendationsFromMessage(string $message): string
    {
        $product = $this->findProductByMessage($message);
        if (! $product) {
            return '';
        }

        $related = Product::query()
            ->where('status', 1)
            ->where('approval_status', 'approved')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with('image:id,product_id,image')
            ->inRandomOrder()
            ->limit(6)
            ->get();

        if ($related->isEmpty()) {
            return '';
        }

        $formatted = $this->formatProducts($related);

        return "### Recommendations related to {$product->name}\n" . json_encode($formatted, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    protected function findProductByMessage(string $message): ?Product
    {
        $terms = $this->extractSearchTerms($message);
        if ($terms === []) {
            return null;
        }

        $query = Product::query()->where('status', 1)->where('approval_status', 'approved');
        $query->where(function ($q) use ($terms) {
            foreach ($terms as $term) {
                $q->orWhere('name', 'like', '%' . $term . '%');
            }
        });

        return $query->first();
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Product>|array<int, Product>  $products
     * @return array<int, array<string, mixed>>
     */
    protected function formatProducts($products): array
    {
        $items = [];

        foreach ($products as $product) {
            $image = optional($product->image)->image;
            $items[] = [
                'id'       => $product->id,
                'name'     => $product->name,
                'price'    => (float) ($product->new_price ?? $product->price ?? 0),
                'old_price'=> (float) ($product->old_price ?? 0),
                'stock'    => (int) ($product->stock ?? 0),
                'in_stock' => (int) ($product->stock ?? 0) > 0,
                'category' => optional($product->category)->name,
                'url'      => route('product', $product->slug ?? $product->id),
                'image'    => $image ? asset($image) : null,
            ];
        }

        return $items;
    }

  protected function extractMaxPrice(string $message): ?float
    {
        if (preg_match('/(\d[\d,]*)\s*টাকার?\s*মধ্যে/u', $message, $m)) {
            return (float) str_replace(',', '', $m[1]);
        }
        if (preg_match('/under\s*(\d[\d,]*)/i', $message, $m)) {
            return (float) str_replace(',', '', $m[1]);
        }
        if (preg_match('/budget\s*(\d[\d,]*)/i', $message, $m)) {
            return (float) str_replace(',', '', $m[1]);
        }

        return null;
    }

    protected function extractMinPrice(string $message): ?float
    {
        if (preg_match('/(\d[\d,]*)\s*টাকার?\s*উপর/u', $message, $m)) {
            return (float) str_replace(',', '', $m[1]);
        }

        return null;
    }

    /**
     * @return array<int, string>
     */
    protected function extractSearchTerms(string $message): array
    {
        $map = [
            'শার্ট' => 'shirt', 'সুতি' => 'cotton', 'অফিস' => 'office',
            'স্মার্টওয়াচ' => 'watch', 'ঘড়ি' => 'watch', 'ব্যাগ' => 'bag', 'জুতা' => 'shoe',
            'টিশার্ট' => 'tshirt', 'প্যান্ট' => 'pant', 'মোবাইল' => 'mobile', 'ল্যাপটপ' => 'laptop',
        ];

        foreach ($map as $bn => $en) {
            if (str_contains($message, $bn)) {
                $message .= ' ' . $en . ' ' . $bn;
            }
        }

        $stop = ['the', 'for', 'and', 'with', 'আমি', 'চাই', 'কিছু', 'ভালো', 'একটি', 'একটা', 'দাম', 'টাকা', 'মধ্যে', 'কোন', 'কি', 'কী', 'হবে', 'পারে', 'জন্য', 'about', 'please', 'need', 'want'];

        $tokens = preg_split('/[^\p{L}\p{N}]+/u', mb_strtolower($message)) ?: [];
        $terms = [];

        foreach ($tokens as $token) {
            $token = trim($token);
            if (mb_strlen($token) < 2 || in_array($token, $stop, true)) {
                continue;
            }
            if (is_numeric($token)) {
                continue;
            }
            $terms[] = $token;
        }

        return array_values(array_unique(array_slice($terms, 0, 12)));
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
}
