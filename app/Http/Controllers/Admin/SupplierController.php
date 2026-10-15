<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    // List + Create form
    public function index()
    {
        $suppliers = Supplier::orderBy('id','desc')->paginate(10);
        return view('backEnd.suppliers.index', compact('suppliers'));
    }

    // Store new supplier
    public function store(Request $request)
    {
        $request->validate([
            'name'   => 'required|string|max:255',
            'phone'  => 'nullable|string|max:50',
            'email'  => 'nullable|email|max:100',
            'address'=> 'nullable|string|max:255',
        ]);

        Supplier::create([
            'name'            => $request->name,
            'phone'           => $request->phone,
            'email'           => $request->email,
            'address'         => $request->address,
            'opening_balance' => 0,
            'current_due'     => 0,
        ]);

        return back()->with('success','Supplier created successfully!');
    }

    // Edit page
    public function edit($id)
    {
        try {
            $supplier  = Supplier::findOrFail($id);
            $suppliers = Supplier::orderBy('id','desc')->paginate(20);

            // একই পেইজে edit form দেখাবো
            return view('backEnd.suppliers.index', compact('supplier','suppliers'));
        } catch (\Exception $e) {
            return redirect()->route('admin.suppliers.index')
                ->with('error', 'Supplier not found: ' . $e->getMessage());
        }
    }

    // Update supplier
    public function update(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);

        $request->validate([
            'name'   => 'required|string|max:255',
            'phone'  => 'nullable|string|max:50',
            'email'  => 'nullable|email|max:100',
            'address'=> 'nullable|string|max:255',
        ]);

        $supplier->update([
            'name'    => $request->name,
            'phone'   => $request->phone,
            'email'   => $request->email,
            'address' => $request->address,
        ]);

        return redirect()
            ->route('admin.suppliers.index')
            ->with('success','Supplier updated successfully!');
    }

    // Delete supplier
    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);
        
        // Check if supplier has any purchases
        if ($supplier->purchases()->count() > 0) {
            return back()->with('error', 'Cannot delete supplier. This supplier has purchase records.');
        }
        
        // Check if supplier has any payments
        if ($supplier->payments()->count() > 0) {
            return back()->with('error', 'Cannot delete supplier. This supplier has payment records.');
        }
        
        $supplier->delete();
        
        return back()->with('success', 'Supplier deleted successfully!');
    }
}
