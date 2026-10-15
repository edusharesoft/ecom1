<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ManualPaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ManualPaymentGatewayController extends Controller
{
    private const UPLOAD_REL_DIR = 'uploads/manual_payment';

    public function index()
    {
        $gateways = ManualPaymentGateway::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('backEnd.apiintegration.manual_payment_gateways', compact('gateways'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'        => ['required', 'string', 'max:191'],
            'logo'         => ['nullable', 'file', 'max:4096', 'mimes:jpeg,jpg,png,gif,webp,svg'],
            'instructions' => ['nullable', 'string', 'max:20000'],
            'sort_order'   => ['nullable', 'integer', 'min:0', 'max:65535'],
            'status'       => ['nullable', 'boolean'],
        ]);

        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['status']     = ($request->input('status') == '1' || $request->boolean('status')) ? 1 : 0;

        $logoPath = $this->storeUploadedLogo($request);

        ManualPaymentGateway::create([
            'title'        => $data['title'],
            'logo'         => $logoPath,
            'instructions' => $data['instructions'] ?? null,
            'sort_order'   => $data['sort_order'],
            'status'       => $data['status'],
        ]);

        \Toastr::success('ম্যানুয়াল গেটওয়ে যোগ হয়েছে।', 'সফল');
        return redirect()->route('manual-payment-gateway.manage');
    }

    public function update(Request $request)
    {
        $request->validate([
            'id'            => ['required', 'exists:manual_payment_gateways,id'],
            'title'         => ['required', 'string', 'max:191'],
            'logo'          => ['nullable', 'file', 'max:4096', 'mimes:jpeg,jpg,png,gif,webp,svg'],
            'remove_logo'   => ['nullable', 'boolean'],
            'instructions'  => ['nullable', 'string', 'max:20000'],
            'sort_order'    => ['nullable', 'integer', 'min:0', 'max:65535'],
            'status'        => ['nullable', 'boolean'],
        ]);

        $row = ManualPaymentGateway::findOrFail($request->id);
        $row->title        = $request->title;
        $row->instructions = $request->instructions;
        $row->sort_order   = $request->input('sort_order', 0);
        $row->status       = ($request->input('status') == '1' || $request->boolean('status')) ? 1 : 0;

        if ($request->boolean('remove_logo')) {
            $this->deleteStoredLogoFile($row->logo);
            $row->logo = null;
        }

        $newLogo = $this->storeUploadedLogo($request);
        if ($newLogo !== null) {
            $this->deleteStoredLogoFile($row->logo);
            $row->logo = $newLogo;
        }

        $row->save();

        \Toastr::success('আপডেট করা হয়েছে।', 'সফল');
        return redirect()->route('manual-payment-gateway.manage');
    }

    public function destroy(Request $request)
    {
        $request->validate(['id' => ['required', 'exists:manual_payment_gateways,id']]);
        $row = ManualPaymentGateway::find($request->id);
        if ($row) {
            $this->deleteStoredLogoFile($row->logo);
            $row->delete();
        }

        \Toastr::success('ডিলিট করা হয়েছে।', 'সফল');
        return redirect()->route('manual-payment-gateway.manage');
    }

    private function storeUploadedLogo(Request $request, string $field = 'logo'): ?string
    {
        if (! $request->hasFile($field)) {
            return null;
        }

        $file = $request->file($field);
        $dir  = public_path(self::UPLOAD_REL_DIR);
        File::ensureDirectoryExists($dir);

        $ext  = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'png');
        $ext  = preg_replace('/[^a-z0-9]/', '', $ext) ?: 'png';
        $name = 'mpg_' . uniqid('', true) . '_' . time() . '.' . $ext;
        $file->move($dir, $name);

        return self::UPLOAD_REL_DIR . '/' . $name;
    }

    private function deleteStoredLogoFile(?string $relativePath): void
    {
        if ($relativePath === null || $relativePath === '') {
            return;
        }
        // শুধু আমাদের ডিরেক্টরির ফাইল মুছব
        $rel = str_replace(['\\'], ['/'], trim($relativePath, '/'));
        if (! str_starts_with($rel, self::UPLOAD_REL_DIR . '/')) {
            return;
        }
        $full = public_path($rel);
        if (is_string($full) && is_file($full)) {
            @unlink($full);
        }
    }
}
