<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryBoy;
use App\Models\DeliveryBoySalaryPayment;
use App\Models\DeliveryBoyWithdrawal;
use App\Services\DeliveryBoyWalletService;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DeliveryBoyController extends Controller
{
    public function index()
    {
        $rows = DeliveryBoy::query()->orderByDesc('id')->paginate(20);

        return view('backEnd.delivery_boys.index', compact('rows'));
    }

    public function create()
    {
        return view('backEnd.delivery_boys.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'                    => 'required|string|max:191',
            'phone'                   => 'required|string|max:20|unique:delivery_boys,phone',
            'email'                   => 'nullable|email|unique:delivery_boys,email',
            'password'                => 'required|string|min:6',
            'commission_per_delivery' => 'required|numeric|min:0',
            'monthly_salary_amount'   => 'nullable|numeric|min:0',
            'status'                  => 'required|in:0,1',
            'image'                   => 'nullable|image|max:2048',
        ]);

        $payload = [
            'name'                    => $request->name,
            'phone'                   => $request->phone,
            'email'                   => $request->email,
            'password'                => Hash::make($request->password),
            'commission_per_delivery' => $request->commission_per_delivery,
            'monthly_salary_amount'   => $request->monthly_salary_amount ?? 0,
            'status'                  => (int) $request->status,
        ];
        if ($request->hasFile('image')) {
            $payload['image'] = $this->uploadImage($request->file('image'));
        }
        DeliveryBoy::create($payload);

        Toastr::success('Delivery person created');
        return redirect()->route('admin.delivery-boys.index');
    }

    private function uploadImage($file): string
    {
        $name = 'delivery_'.time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
        $path = 'public/uploads/delivery_boys';
        if (! is_dir(base_path($path))) {
            mkdir(base_path($path), 0755, true);
        }
        $file->move(base_path($path), $name);

        return 'public/uploads/delivery_boys/'.$name;
    }

    public function edit(int $id)
    {
        $edit_data = DeliveryBoy::findOrFail($id);

        return view('backEnd.delivery_boys.edit', compact('edit_data'));
    }

    public function update(Request $request, int $id)
    {
        $row = DeliveryBoy::findOrFail($id);
        $request->validate([
            'name'                    => 'required|string|max:191',
            'phone'                   => 'required|string|max:20|unique:delivery_boys,phone,'.$id,
            'email'                   => 'nullable|email|unique:delivery_boys,email,'.$id,
            'password'                => 'nullable|string|min:6',
            'commission_per_delivery' => 'required|numeric|min:0',
            'monthly_salary_amount'   => 'nullable|numeric|min:0',
            'status'                  => 'required|in:0,1',
            'image'                   => 'nullable|image|max:2048',
        ]);

        $row->name = $request->name;
        $row->phone = $request->phone;
        $row->email = $request->email;
        $row->commission_per_delivery = $request->commission_per_delivery;
        $row->monthly_salary_amount = $request->monthly_salary_amount ?? 0;
        $row->status = (int) $request->status;
        if ($request->filled('password')) {
            $row->password = Hash::make($request->password);
        }
        if ($request->hasFile('image')) {
            $row->image = $this->uploadImage($request->file('image'));
        }
        $row->save();

        Toastr::success('Updated');
        return redirect()->route('admin.delivery-boys.index');
    }

    public function wallet(int $id)
    {
        $boy = DeliveryBoy::findOrFail($id);
        $tx = $boy->walletTransactions()->orderByDesc('id')->paginate(30);

        return view('backEnd.delivery_boys.wallet', compact('boy', 'tx'));
    }

    public function paySalary(Request $request, DeliveryBoyWalletService $wallet)
    {
        $request->validate([
            'delivery_boy_id' => 'required|exists:delivery_boys,id',
            'amount'          => 'required|numeric|min:1',
            'salary_month'    => 'required|regex:/^\d{4}-\d{2}$/',
            'note'            => 'nullable|string|max:500',
        ]);

        $boy = DeliveryBoy::findOrFail($request->delivery_boy_id);

        DeliveryBoySalaryPayment::create([
            'delivery_boy_id' => $boy->id,
            'amount'          => $request->amount,
            'salary_month'    => $request->salary_month,
            'note'            => $request->note,
            'created_by'      => auth()->id(),
        ]);

        $wallet->credit(
            $boy,
            'salary',
            (float) $request->amount,
            null,
            'Salary '.$request->salary_month.($request->note ? ' — '.$request->note : '')
        );

        Toastr::success('Salary credited to wallet');
        return redirect()->back();
    }

    public function withdrawals()
    {
        $rows = DeliveryBoyWithdrawal::with('deliveryBoy')->orderByDesc('id')->paginate(30);

        return view('backEnd.delivery_boys.withdrawals', compact('rows'));
    }

    public function approveWithdrawal(Request $request, DeliveryBoyWalletService $wallet)
    {
        $request->validate([
            'id'         => 'required|exists:delivery_boy_withdrawals,id',
            'admin_note' => 'nullable|string|max:500',
        ]);
        $w = DeliveryBoyWithdrawal::findOrFail($request->id);
        if ($w->status !== 'pending') {
            Toastr::error('Already processed');
            return redirect()->back();
        }
        if ($request->has('admin_note')) {
            $w->admin_note = $request->admin_note;
            $w->save();
        }
        try {
            $wallet->approveWithdrawal($w);
            Toastr::success('Withdrawal approved & deducted from wallet');
        } catch (\Throwable $e) {
            Toastr::error($e->getMessage());
        }

        return redirect()->back();
    }

    public function rejectWithdrawal(Request $request)
    {
        $request->validate(['id' => 'required|exists:delivery_boy_withdrawals,id']);
        $w = DeliveryBoyWithdrawal::findOrFail($request->id);
        if ($w->status !== 'pending') {
            return redirect()->back();
        }
        $w->status = 'rejected';
        $w->processed_at = now();
        $w->processed_by = auth()->id();
        $w->admin_note = $request->input('admin_note');
        $w->save();
        Toastr::info('Rejected');

        return redirect()->back();
    }
}
