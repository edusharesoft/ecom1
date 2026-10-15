<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use App\Models\Product;
use App\Models\Review;
use App\Models\Customer;
class ReviewController extends Controller
{
    // function __construct()
    // {
    //      $this->middleware('permission:review-list|review-create|review-edit|review-delete', ['only' => ['index','store']]);
    //      $this->middleware('permission:review-create', ['only' => ['create','store']]);
    //      $this->middleware('permission:review-edit', ['only' => ['edit','update']]);
    //      $this->middleware('permission:review-delete', ['only' => ['destroy']]);
    // }

    public function index(Request $request)
    {
        $show_data = Review::orderBy('id','DESC')->get();
        return view('backEnd.review.index',compact('show_data'));
    }
    public function create()
    {
        $products = Product::where(['status'=>1])->select('id','name')->get();
        $customers = Customer::where('status', 'active')->get();
        return view('backEnd.review.create',compact('products', 'customers'));
    }
    public function store(Request $request)
    {
        $this->validate($request, [
            'customer_id' => 'required',
            'ratting' => 'required',
            'review' => 'required',
            'product_id' => 'required',
            'status' => 'required',
        ]);
        $customer = Customer::where('id', $request->customer_id)->first();
        $input = $request->all();
        $input['name'] = $customer->name ? $customer->name : 'N / A';
        $input['email'] = $customer->email ? $customer->email : 'N / A';
        $input['status'] = $request->status==1?'active':'pending';
        Review::create($input);
        Toastr::success('Success','Data insert successfully');
        return redirect()->route('reviews.index');
    }
    
    public function edit($id)
    {
        $edit_data = Review::find($id);
        $products = Product::where(['status'=>1])->select('id','name')->get();
        return view('backEnd.review.edit',compact('edit_data','products'));
    }
    
    public function update(Request $request)
    {
        
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required',
            'ratting' => 'required',
            'review' => 'required',
            'product_id' => 'required',
        ]);
        $input = $request->except('hidden_id');
        $input['status'] = $request->status==1?'active':'pending';
        $update_data = Review::find($request->hidden_id);
        if (!$update_data) {
            Toastr::error('Error','Record not found');
            return redirect()->back();
        }
        $update_data->update($input);

        Toastr::success('Success','Data update successfully');
        return redirect()->route('reviews.index');
    }
 
    public function pending(){
        $data = Review::where('status','pending')->get();
        return view('backEnd.review.pending',compact('data'));
    }
    public function inactive(Request $request){
        $inactive = Review::find($request->hidden_id);
        $inactive->status = 'pending';
        $inactive->save();
        Toastr::success('Success','Data inactive successfully');
        return redirect()->back();
    }
    public function active(Request $request){
        $active = Review::find($request->hidden_id);
        $active->status = 'active';
        $active->save();
        
        $product = Product::select('id','ratting')->find($active->product_id);
        $product->ratting += 1;
        $product->save();
        Toastr::success('Success','Data active successfully');
        return redirect()->back();
    }
    public function destroy(Request $request)
    {
        $delete_data = Review::find($request->hidden_id);
        $delete_data->delete();
        Toastr::success('Success','Data delete successfully');
        return redirect()->back();
    }
}
