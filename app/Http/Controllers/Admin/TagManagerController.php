<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GoogleTagManager;
use Illuminate\Support\Facades\Cache;
use Toastr;

class TagManagerController extends Controller
{
    public function index(Request $request)
    {
        $data = GoogleTagManager::orderBy('id','DESC')->get();
        return view('backEnd.tagmanager.index',compact('data'));
    }
    
    public function create()
    {
        return view('backEnd.tagmanager.create');
    }
    
    public function store(Request $request)
    {
        $this->validate($request, [
            'code' => 'required',
            'status' => 'required',
        ]);
        $input = $request->all();
        GoogleTagManager::create($input);
        Cache::forget('gtm_code_list');
        Toastr::success('Success','Data insert successfully');
        return redirect()->route('tagmanagers.index');
    }
    
    public function edit($id)
    {
        $edit_data = GoogleTagManager::find($id);
        return view('backEnd.tagmanager.edit',compact('edit_data'));
    }
    
    public function update(Request $request)
    {
        $this->validate($request, [
            'code' => 'required',
        ]);
        
        // Fix: Use hidden_id instead of id (form sends hidden_id)
        $update_data = GoogleTagManager::find($request->hidden_id ?? $request->id);
        
        if (!$update_data) {
            Toastr::error('Error','Record not found');
            return redirect()->back();
        }
        
        $input = $request->except('hidden_id');
        $input['status'] = $request->status ? 1 : 0;
        $update_data->update($input);
        Cache::forget('gtm_code_list');
        Toastr::success('Success','Data update successfully');
        return redirect()->route('tagmanagers.index');
    }
 
    public function inactive(Request $request)
    {
        $inactive = GoogleTagManager::find($request->hidden_id);
        if (!$inactive) {
            Toastr::error('Error','Record not found');
            return redirect()->back();
        }
        $inactive->status = 0;
        $inactive->save();
        Cache::forget('gtm_code_list');
        Toastr::success('Success','Data inactive successfully');
        return redirect()->back();
    }
    
    public function active(Request $request)
    {
        $active = GoogleTagManager::find($request->hidden_id);
        if (!$active) {
            Toastr::error('Error','Record not found');
            return redirect()->back();
        }
        $active->status = 1;
        $active->save();
        Cache::forget('gtm_code_list');
        Toastr::success('Success','Data active successfully');
        return redirect()->back();
    }
    
    public function destroy(Request $request)
    {
        $delete_data = GoogleTagManager::find($request->hidden_id);
        if (!$delete_data) {
            Toastr::error('Error','Record not found');
            return redirect()->back();
        }
        $delete_data->delete();
        Cache::forget('gtm_code_list');
        Toastr::success('Success','Data delete successfully');
        return redirect()->back();
    }
}
