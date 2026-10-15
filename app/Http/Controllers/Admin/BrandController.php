<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\ImageOptimizer;
use Illuminate\Http\Request;
use App\Models\Brand;
use File;
use Toastr;

class BrandController extends Controller
{
    
    public function index(Request $request)
    {
        $data = Brand::orderBy('id','DESC')->get();
        return view('backEnd.brand.index',compact('data'));
    }
    public function create()
    {
        return view('backEnd.brand.create');
    }
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'status' => 'required',
        ]);

        $imageUrl = null;
        if ($request->hasFile('image')) {
            $imageUrl = ImageOptimizer::store($request->file('image'), 'public/uploads/brand/');
        }

        $input = $request->all();
        $input['slug'] = strtolower(preg_replace('/\s+/u', '-', trim($request->name)));
        $input['image'] = $imageUrl;
        Brand::create($input);
        Toastr::success('Success','Data insert successfully');
        return redirect()->route('brands.index');
    }
    
    public function edit($id)
    {
        $edit_data = Brand::find($id);
        return view('backEnd.brand.edit',compact('edit_data'));
    }
    
    public function update(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);
        $update_data = Brand::find($request->id);
        $input = $request->all();
        if ($request->hasFile('image')) {
            $input['image'] = ImageOptimizer::store($request->file('image'), 'public/uploads/brand/');
            File::delete($update_data->image);
        } else {
            $input['image'] = $update_data->image;
        }
        $input['status'] = $request->status?1:0;
        $update_data->update($input);

        Toastr::success('Success','Data update successfully');
        return redirect()->route('brands.index');
    }
 
    public function inactive(Request $request)
    {
        $inactive = Brand::find($request->hidden_id);
        $inactive->status = 0;
        $inactive->save();
        Toastr::success('Success','Data inactive successfully');
        return redirect()->back();
    }
    public function active(Request $request)
    {
        $active = Brand::find($request->hidden_id);
        $active->status = 1;
        $active->save();
        Toastr::success('Success','Data active successfully');
        return redirect()->back();
    }
    public function destroy(Request $request)
    {
        $delete_data = Brand::find($request->hidden_id);
        $delete_data->delete();
        Toastr::success('Success','Data delete successfully');
        return redirect()->back();
    }
}
