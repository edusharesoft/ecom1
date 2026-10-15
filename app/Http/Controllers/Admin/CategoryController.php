<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\ImageOptimizer;
use Illuminate\Http\Request;
use App\Models\Category;
use Toastr;
use File;
use Str;

class CategoryController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:category-list|category-create|category-edit|category-delete', ['only' => ['index','store']]);
        $this->middleware('permission:category-create', ['only' => ['create','store']]);
        $this->middleware('permission:category-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:category-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $data = Category::orderBy('id','DESC')->with('category')->get();
        return view('backEnd.category.index',compact('data'));
    }

    public function create()
    {
        $categories = Category::orderBy('id','DESC')->select('id','name')->get();
        return view('backEnd.category.create',compact('categories'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name'   => 'required',
            'status' => 'required',
            // icon optional
            // 'icon'   => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        /* ========= Main Image Upload ========= */
        $imageUrl = null;
        if ($request->hasFile('image')) {
            $imageUrl = ImageOptimizer::store($request->file('image'), 'public/uploads/category/');
        }

        /* ========= Icon Image Upload ========= */
        $iconUrl = null;
        if ($request->hasFile('icon')) {
            $iconUrl = ImageOptimizer::store(
                $request->file('icon'),
                'public/uploads/category/',
                ImageOptimizer::basenameFromOriginal($request->file('icon'), 'icon')
            );
        }

        /* ========= Input Prepare ========= */
        $input = $request->all();

        $input['slug'] = strtolower(preg_replace('/\s+/', '-', $request->name));
        $input['slug'] = str_replace('/', '', $input['slug']);

        $input['parent_id']  = $request->parent_id ? $request->parent_id : 0;
        $input['front_view'] = $request->front_view ? 1 : 0;
        $input['image']      = $imageUrl;
        $input['icon']       = $iconUrl; // নতুন icon কলাম

        Category::create($input);

        Toastr::success('Success','Data insert successfully');
        return redirect()->route('categories.index');
    }

    public function edit($id)
    {
        $edit_data  = Category::find($id);
        $categories = Category::select('id','name')->get();
        return view('backEnd.category.edit',compact('edit_data','categories'));
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            // 'icon' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Fix: Use hidden_id instead of id (form may send hidden_id)
        $update_data = Category::find($request->hidden_id ?? $request->id);
        
        if (!$update_data) {
            Toastr::error('Error','Record not found');
            return redirect()->back();
        }
        
        $input       = $request->except('hidden_id');

        /* ========= Main Image Update ========= */
        if ($request->hasFile('image')) {
            $imageUrl = ImageOptimizer::store($request->file('image'), 'public/uploads/category/');

            if ($update_data->image) {
                File::delete($update_data->image);
            }

            $input['image'] = $imageUrl;
        } else {
            $input['image'] = $update_data->image;
        }

        /* ========= Icon Update ========= */
        if ($request->hasFile('icon')) {
            $iconUrl = ImageOptimizer::store(
                $request->file('icon'),
                'public/uploads/category/',
                ImageOptimizer::basenameFromOriginal($request->file('icon'), 'icon')
            );

            if ($update_data->icon) {
                File::delete($update_data->icon);
            }

            $input['icon'] = $iconUrl;
        } else {
            $input['icon'] = $update_data->icon;
        }

        /* ========= Others ========= */
        $input['slug'] = strtolower(preg_replace('/\s+/', '-', $request->name));
        $input['slug'] = str_replace('/', '', $input['slug']);

        $input['parent_id']  = $request->parent_id ? $request->parent_id : 0;
        $input['front_view'] = $request->front_view ? 1 : 0;
        $input['status']     = $request->status ? 1 : 0;

        $update_data->update($input);

        Toastr::success('Success','Data update successfully');
        return redirect()->route('categories.index');
    }

    public function inactive(Request $request)
    {
        $inactive = Category::find($request->hidden_id);
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
        $active = Category::find($request->hidden_id);
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
        $delete_data = Category::find($request->hidden_id);

        if (!$delete_data) {
            Toastr::error('Error', 'Category not found.');
            return redirect()->back();
        }

        // সাবক্যাটাগরি বা প্রোডাক্ট থাকলে ডিলিট না করে মেসেজ দিন
        $hasSubcategories = \App\Models\Subcategory::where('category_id', $delete_data->id)->exists();
        $hasProducts = \App\Models\Product::where('category_id', $delete_data->id)->exists();
        if ($hasSubcategories || $hasProducts) {
            Toastr::error('Cannot delete', 'Remove or reassign subcategories and products under this category first.');
            return redirect()->back();
        }

        if ($delete_data->image && File::exists($delete_data->image)) {
            File::delete($delete_data->image);
        }
        if ($delete_data->icon && File::exists($delete_data->icon)) {
            File::delete($delete_data->icon);
        }
        $delete_data->delete();

        Toastr::success('Success', 'Category deleted successfully.');
        return redirect()->back();
    }
}
