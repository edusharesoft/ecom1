<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SaleNotification;
use App\Models\NotificationSetting;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;

class SaleNotificationController extends Controller
{
    public function index()
    {
        $setting  = NotificationSetting::instance();
        $products = Product::select('id', 'name', 'slug')->orderBy('name')->get();

        $customNotifs = SaleNotification::where('is_real', 0)
            ->orderBy('display_order')
            ->orderByDesc('id')
            ->get();

        $realNotifs = SaleNotification::where('is_real', 1)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        return view('backEnd.sale_notification.index',
            compact('setting', 'customNotifs', 'realNotifs', 'products'));
    }

    public function syncRealOrders()
    {
        try {
            $orders = Order::with([
                    'shipping:id,order_id,name,phone',
                    'orderdetails' => fn($q) => $q->with(['product:id,name,slug', 'product.image'])->limit(1),
                ])
                ->latest()
                ->limit(200)
                ->get();

            $count = 0;
            foreach ($orders as $order) {
                $detail       = $order->orderdetails->first();
                $product      = $detail?->product;
                $productName  = $detail?->product_name ?? ($product?->name ?? 'একটি পণ্য');
                $productSlug  = $product?->slug ?? null;
                $image        = $product?->image?->image ?? null;

                $customerName = $order->shipping?->name;
                if (empty($customerName)) {
                    $phone = $order->shipping?->phone;
                    $customerName = $phone ? 'Customer (' . substr($phone, -4) . ')' : 'Customer';
                }

                $firstName = explode(' ', trim($customerName))[0];

                SaleNotification::updateOrCreate(
                    ['order_id' => $order->id],
                    [
                        'customer_name' => $firstName,
                        'product_name'  => \Str::limit($productName, 100),
                        'product_image' => $image,
                        'product_url'   => $productSlug ? route('product', $productSlug) : null,
                        'is_real'       => 1,
                        'is_active'     => 1,
                        'created_at'    => $order->created_at,
                        'updated_at'    => now(),
                    ]
                );
                $count++;
            }

            return response()->json(['success' => true, 'synced' => $count]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function saveSettings(Request $request)
    {
        $setting = NotificationSetting::instance();
        $setting->update([
            'is_enabled'       => $request->has('is_enabled')       ? 1 : 0,
            'show_real_orders' => $request->has('show_real_orders') ? 1 : 0,
            'show_fake_orders' => $request->has('show_fake_orders') ? 1 : 0,
            'display_duration' => (int) ($request->display_duration ?? 5),
            'interval_min'     => (int) ($request->interval_min     ?? 8),
            'interval_max'     => (int) ($request->interval_max     ?? 15),
        ]);
        Toastr::success('Settings saved!', 'Success');
        return back();
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:100',
            'product_name'  => 'required|string|max:255',
        ]);
        SaleNotification::create([
            'customer_name' => trim($request->customer_name),
            'product_name'  => trim($request->product_name),
            'product_image' => $request->product_image ?? null,
            'product_url'   => $request->product_url   ?? null,
            'is_real'       => 0,
            'is_active'     => 1,
            'display_order' => 0,
        ]);
        Toastr::success('Notification added!', 'Success');
        return back();
    }

    public function bulkStore(Request $request)
    {
        $request->validate(['bulk_data' => 'required|string']);
        $lines = explode("\n", trim($request->bulk_data));
        $count = 0;
        foreach ($lines as $line) {
            $line = trim($line);
            if (!$line) continue;
            $parts = preg_split('/\s*[\|,\t]\s*/', $line, 2);
            $customerName = trim($parts[0] ?? '');
            $productName  = trim($parts[1] ?? '');
            if (!$customerName || !$productName) continue;
            SaleNotification::create([
                'customer_name' => $customerName,
                'product_name'  => $productName,
                'is_real'       => 0,
                'is_active'     => 1,
            ]);
            $count++;
        }
        Toastr::success("{$count} টি notification যোগ হয়েছে!", 'Success');
        return back();
    }

    public function toggle($id)
    {
        $n = SaleNotification::findOrFail($id);
        $n->update(['is_active' => !$n->is_active]);
        return response()->json(['success' => true, 'is_active' => (bool)$n->is_active]);
    }

    public function reorder(Request $request)
    {
        $ids = $request->input('ids', []);
        foreach ($ids as $order => $id) {
            SaleNotification::where('id', $id)->update(['display_order' => $order]);
        }
        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        SaleNotification::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }

    public function destroyAll()
    {
        SaleNotification::where('is_real', 0)->delete();
        Toastr::success('All custom notifications deleted!', 'Success');
        return back();
    }

    public function destroyAllReal()
    {
        SaleNotification::where('is_real', 1)->delete();
        return response()->json(['success' => true]);
    }
}
