<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Color;
use Toastr;

class ColorController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:color-list|color-create|color-edit|color-delete', ['only' => ['index','show']]);
         $this->middleware('permission:color-create', ['only' => ['create','store']]);
         $this->middleware('permission:color-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:color-delete', ['only' => ['destroy']]);
    }
    public function index(Request $request)
    {
        $show_data = Color::orderBy('colorName','ASC')->get();
        return view('backEnd.color.index',compact('show_data'));
    }
    public function create()
    {
        return view('backEnd.color.create');
    }
    public function store(Request $request)
    {
        $this->validate($request, [
            'status' => 'required',
        ]);
        
        $input = $request->all();
        
        Color::create($input);        
        
        Toastr::success('Success','Data insert successfully');
        return redirect()->route('colors.index');
    }
    
    public function edit($id)
    {
        $edit_data = Color::find($id);
        return view('backEnd.color.edit',compact('edit_data'));
    }
    
    public function update(Request $request)
    { 
        $this->validate($request, [
            'status' => 'required',
        ]);
        // image one
        $update_data = Color::find($request->id);
        $input = $request->all();
        $update_data->update($input);

        Toastr::success('Success','Data update successfully');
        return redirect()->route('colors.index');
    }
 
    public function inactive(Request $request)
    {
        $inactive = Color::find($request->hidden_id);
        $inactive->status = 0;
        $inactive->save();
        Toastr::success('Success','Data inactive successfully');
        return redirect()->back();
    }
    public function active(Request $request)
    {
        $active = Color::find($request->hidden_id);
        $active->status = 1;
        $active->save();
        Toastr::success('Success','Data active successfully');
        return redirect()->back();
    }
    public function destroy(Request $request)
    {       
        $delete_data = Color::find($request->hidden_id);
        $delete_data->delete();
        Toastr::success('Success','Data delete successfully');
        return redirect()->back();
    }
}
