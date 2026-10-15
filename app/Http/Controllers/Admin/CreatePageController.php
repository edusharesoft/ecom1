<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CreatePage;
use Toastr;
use Str;
class CreatePageController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:page-list|page-create|page-edit|page-delete', ['only' => ['index','store']]);
         $this->middleware('permission:page-create', ['only' => ['create','store']]);
         $this->middleware('permission:page-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:page-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $show_data = CreatePage::orderBy('id','DESC')->get();
        return view('backEnd.createpage.index',compact('show_data'));
    }
    public function create()
    {
        return view('backEnd.createpage.create');
    }
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'title' => 'required',
            'description' => 'required',
            'status' => 'required',
        ]);

        $input = $request->all();
        $input['slug'] = strtolower(preg_replace('/\s+/', '-', $request->name));
        CreatePage::create($input);
        Toastr::success('Success','Data insert successfully');
        return redirect()->route('pages.index');
    }
    
    public function edit($id)
    {
        $edit_data = CreatePage::find($id);
        return view('backEnd.createpage.edit',compact('edit_data'));
    }
    
    public function update(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'title' => 'required',
            'description' => 'required',
        ]);
        $input = $request->except('hidden_id');
        $input['slug'] = strtolower(preg_replace('/\s+/', '-', $request->name));
        $update_data = CreatePage::find($request->hidden_id);
        if (!$update_data) {
            Toastr::error('Error','Record not found');
            return redirect()->back();
        }
        $update_data->update($input);

        Toastr::success('Success','Data update successfully');
        return redirect()->route('pages.index');
    }
 
    public function inactive(Request $request)
    {
        $inactive = CreatePage::find($request->hidden_id);
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
        $active = CreatePage::find($request->hidden_id);
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
        $delete_data = CreatePage::find($request->hidden_id);
        if (!$delete_data) {
            Toastr::error('Error','Record not found');
            return redirect()->back();
        }
        $delete_data->delete();
        Toastr::success('Success','Data delete successfully');
        return redirect()->back();
    }
}
