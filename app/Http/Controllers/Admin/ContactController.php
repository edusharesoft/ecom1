<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contact;
use Toastr;
class ContactController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:contact-list|contact-create|contact-edit|contact-delete', ['only' => ['index','store']]);
         $this->middleware('permission:contact-create', ['only' => ['create','store']]);
         $this->middleware('permission:contact-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:contact-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        // ✅ Get single contact (no loop needed - only one contact record exists)
        $contact = Contact::first();
        
        // If no contact exists, create one
        if (!$contact) {
            $contact = Contact::create([
                'phone' => '',
                'email' => '',
                'address' => '',
                'status' => 1,
            ]);
        }
        
        return view('backEnd.contact.index', compact('contact'));
    }
    public function create()
    {
        return view('backEnd.contact.create');
    }
    public function store(Request $request)
    {
        $this->validate($request, [
            'phone' => 'required',
            'email' => 'required',
            'address' => 'required',
            'status' => 'required',
        ]);
        $input = $request->all();
        Contact::create($input);
        Toastr::success('Success','Data insert successfully');
        return redirect()->route('contact.index');
    }
    
    public function edit($id)
    {
        $edit_data = Contact::find($id);
        return view('backEnd.contact.edit',compact('edit_data'));
    }
    
    public function update(Request $request)
    {
        $this->validate($request, [
            'phone' => 'required',
            'email' => 'required|email',
            'address' => 'required',
        ]);
        
        $input = $request->except(['hidden_id', 'status']);
        // স্টোরফ্রন্টে কন্ট্যাক্ট সবসময় চালু (ভিজিবিলিটি টগল UI সরানো হয়েছে)
        $input['status'] = 1;
        
        $update_data = Contact::find($request->hidden_id);
        if (!$update_data) {
            Toastr::error('Error','Record not found');
            return redirect()->back();
        }
        $update_data->update($input);

        Toastr::success('Success','Contact details updated successfully');
        return redirect()->route('contact.index');
    }
 
    public function inactive(Request $request)
    {
        $inactive = Contact::find($request->hidden_id);
        if (!$inactive) {
            Toastr::error('Error','Record not found');
            return redirect()->back();
        }
        $inactive->status = 0;
        $inactive->save();
        Toastr::success('Success','Data inactive successfully');
        return redirect()->back();
    }
    public function active(Request $request)
    {
        $active = Contact::find($request->hidden_id);
        if (!$active) {
            Toastr::error('Error','Record not found');
            return redirect()->back();
        }
        $active->status = 1;
        $active->save();
        Toastr::success('Success','Data active successfully');
        return redirect()->back();
    }
    public function destroy(Request $request)
    {
        $delete_data = Contact::find($request->hidden_id);
        if (!$delete_data) {
            Toastr::error('Error','Record not found');
            return redirect()->back();
        }
        $delete_data->delete();
        Toastr::success('Success','Data delete successfully');
        return redirect()->back();
    }
}
