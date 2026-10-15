<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Toastr;

class InhouseProductController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:product-list|product-create|product-edit|product-delete', ['only' => ['index','show']]);
    }

    /**
     * Display all inhouse products (products without vendor_id)
     */
    public function index(Request $request)
    {
        // Show only inhouse products (vendor_id is null)
        $query = Product::whereNull('vendor_id')
            ->orderBy('id','DESC')
            ->with('image','category');

        if ($request->keyword) {
            $query->where('name', 'LIKE', '%' . $request->keyword . "%");
        }

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->status !== null) {
            $query->where('status', $request->status);
        }

        $data = $query->paginate(20);
        $categories = Category::where('parent_id', 0)->where('status', 1)->select('id', 'name')->get();
        
        return view('backEnd.inhouse_product.index', compact('data', 'categories'));
    }

    /**
     * Show single product details
     */
    public function show($id)
    {
        $product = Product::whereNull('vendor_id')
            ->with('image','images','category','subcategory','childcategory','brand','colors','sizes')
            ->findOrFail($id);
            
        return view('backEnd.inhouse_product.show', compact('product'));
    }
}
