<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\ImageOptimizer;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use App\Models\Category;
use App\Models\Subcategory;
use File;
use DB;

class SubcategoryController extends Controller
{
    public function getCategory(Request $request){
        $category = DB::table("categories")
        ->where("service_category", $request->service_category)
        ->pluck('name', 'id');
        return response()->json($category);
    }        

    function __construct()
    {
        $this->middleware('permission:subcategory-list|subcategory-create|subcategory-edit|subcategory-delete', ['only' => ['index','store']]);
        $this->middleware('permission:subcategory-create', ['only' => ['create','store']]);
        $this->middleware('permission:subcategory-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:subcategory-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $data = Subcategory::orderBy('id','DESC')->with('category')->get();
        return view('backEnd.subcategory.index',compact('data'));
    }
    public function create()
    {
        $categories = Category::get();
        return view('backEnd.subcategory.create', compact('categories'));
    }
    public function store(Request $request)
    {
        $this->validate($request, [
            'category_id' => 'required',
            'subcategoryName' => 'required',
            'status' => 'required',
        ]);
        $imageUrl = null;
        if ($request->hasFile('image')) {
            $imageUrl = ImageOptimizer::store($request->file('image'), 'public/uploads/subcategory/');
        }
        
      
        $input = $request->all();

        $input['slug'] = strtolower(preg_replace('/\s+/', '-', $request->subcategoryName));
        $input['slug'] = str_replace('/', '', $input['slug']);

        $input['image'] = $imageUrl;
        Subcategory::create($input);
        Toastr::success('Success','Data insert successfully');
        return redirect()->route('subcategories.index');
    }
    
    public function edit($id)
    {
        $edit_data = Subcategory::find($id);
        $categories = Category::select('id','name')->get();
        return view('backEnd.subcategory.edit',compact('edit_data','categories'));
    }
    
    public function update(Request $request)
    {
        $this->validate($request, [
            'category_id' => 'required',
            'subcategoryName' => 'required',
            'status' => 'required',
        ]);
        $update_data = Subcategory::find($request->id);
        $input = $request->all();
        
        if ($request->hasFile('image')) {
            $input['image'] = ImageOptimizer::store($request->file('image'), 'public/uploads/subcategory/');
            File::delete($update_data->image);
        } else {
            $input['image'] = $update_data->image;
        }


        
        $input['slug'] = strtolower(preg_replace('/\s+/', '-', $request->subcategoryName));
        $input['slug'] = str_replace('/', '', $input['slug']);
        $input['status'] = $request->status?1:0;
        
        $update_data->update($input);

        Toastr::success('Success','Data update successfully');
        return redirect()->route('subcategories.index');
    }
 
    public function inactive(Request $request)
    {
        $inactive = Subcategory::find($request->hidden_id);
        $inactive->status = 0;
        $inactive->save();
        Toastr::success('Success','Data inactive successfully');
        return redirect()->back();
    }
    public function active(Request $request)
    {
        $active = Subcategory::find($request->hidden_id);
        $active->status = 1;
        $active->save();
        Toastr::success('Success','Data active successfully');
        return redirect()->back();
    }
    public function destroy(Request $request)
{
    try {
        $id = $request->id ?? $request->hidden_id;

        if (!$id) {
            Toastr::error('Error', 'Invalid subcategory ID!');
            return redirect()->back();
        }

        $subcategory = Subcategory::find($id);

        if (!$subcategory) {
            Toastr::error('Error', 'Subcategory not found!');
            return redirect()->back();
        }

        if (File::exists($subcategory->image)) {
            File::delete($subcategory->image);
        }

        $subcategory->delete();
        Toastr::success('Success', 'Subcategory deleted successfully');
        return redirect()->back();

    } catch (\Exception $e) {
        Toastr::error('Error', $e->getMessage());
        return redirect()->back();
    }
}
} // ✅ ← এই ব্রেস দিয়ে ক্লাস শেষ করো

