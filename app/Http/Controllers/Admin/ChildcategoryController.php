<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Childcategory;
use App\Models\Subcategory;
use Toastr;
use Image;
use File;
use Str;
use DB;

class ChildcategoryController extends Controller
{
    public function getSubCategory(Request $request){
        $category = DB::table("subcategories")
        ->where("subcategorytype", $request->childcategorytype)
        ->pluck('subcategoryName', 'id');
        return response()->json($category);
    }        

    function __construct()
    {
        $this->middleware('permission:childcategory-list|childcategory-create|childcategory-edit|childcategory-delete', ['only' => ['index','store']]);
        $this->middleware('permission:childcategory-create', ['only' => ['create','store']]);
        $this->middleware('permission:childcategory-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:childcategory-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $data = Childcategory::orderBy('id','DESC')->with('subcategory')->get();
        return view('backEnd.childcategory.index', compact('data'));
    }
    public function create()
    {
        return view('backEnd.childcategory.create');
    }
    public function store(Request $request)
    {
        $this->validate($request, [
            'subcategory_id' => 'required',
            'childcategoryName' => 'required',
            'status' => 'required',
        ]);
        // image with intervention 
        
        
        $input = $request->all();

        $input['slug'] = strtolower(preg_replace('/\s+/', '-', $request->childcategoryName));
        $input['slug'] = str_replace('/', '', $input['slug']);
        
        Childcategory::create($input);
        Toastr::success('Success','Data insert successfully');
        return redirect()->route('childcategories.index');
    }
    
    public function edit($id)
    {
        $edit_data = Childcategory::find($id);
        $categories = Subcategory::select('id','subcategoryName')->get();
        return view('backEnd.childcategory.edit',compact('edit_data','categories'));
    }
    
    public function update(Request $request)
    {
        $this->validate($request, [
            'subcategory_id' => 'required',
            'childcategoryName' => 'required',
            'status' => 'required',
        ]);
        
        // Fix: Use hidden_id instead of id (form may send hidden_id)
        $update_data = Childcategory::find($request->hidden_id ?? $request->id);
        
        if (!$update_data) {
            Toastr::error('Error','Record not found');
            return redirect()->back();
        }
        
        $input = $request->except('hidden_id');
        
       
        $input['slug'] = strtolower(preg_replace('/\s+/', '-', $request->childcategoryName));
        $input['slug'] = str_replace('/', '', $input['slug']);
        $input['status'] = $request->status?1:0;
        
        $update_data->update($input);

        Toastr::success('Success','Data update successfully');
        return redirect()->route('childcategories.index');
    }
 
    public function inactive(Request $request)
    {
        $inactive = Childcategory::find($request->hidden_id);
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
        $active = Childcategory::find($request->hidden_id);
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
        $request->validate(['hidden_id' => 'required']);
        $delete_data = Childcategory::find($request->hidden_id);

        if (!$delete_data) {
            Toastr::error('Error', 'Child category not found.');
            return redirect()->back();
        }

        $delete_data->delete();
        Toastr::success('Success', 'Child category deleted successfully.');
        return redirect()->back();
    }
}
