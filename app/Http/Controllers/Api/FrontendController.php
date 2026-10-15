<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\GeneralSetting;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use App\Models\CreatePage;
use App\Models\SocialMedia;
use App\Models\Contact;
use Carbon\Carbon;
use Response;
use Hash;
use Auth;
use Mail;
use Str;
use DB;
class FrontendController extends Controller
{
   public function appconfig()
    {
        $data = GeneralSetting::where('status',1)->select('id','name','white_logo','dark_logo','favicon')->first();
        return response()->json(['status' => 'success','message'=>'Data fatch successfully','data'=>$data]);
    }
    
    public function slider()
    {
        $data = Banner::where(['status'=>1,'category_id'=>1])->select('id','image','status','category_id','link')->get();
        return response()->json(['status' => 'success','message'=>'Data fatch successfully','data'=>$data]);
    }
    
    public function categorymenu(){
        $data = Category::where(['status'=>1])->select('id','slug','name','image')->with('menusubcategories','menusubcategories.menuchildcategories')->get();
        return response()->json(['status' => 'success','message'=>'Data fatch successfully','data'=>$data]);
   }
   
    public function hotdealproduct(){
        $data = Product::where(['status'=>1])->select('id','slug','name','topsale','old_price','new_price')->with('image')->get();
        return response()->json(['status' => 'success','message'=>'Data fatch successfully','data'=>$data]);
   }
   
   public function homepageproduct(){
        $data = Category::where(['status'=>1])->select('id','slug','name')->with('products','products.image')->get();
        return response()->json(['status' => 'success','message'=>'Data fatch successfully','data'=>$data]);
   }
   
   public function footermenuleft(){
        $data = CreatePage::where(['status'=>1])->select('id','slug','name')->limit(3)->get();
        return response()->json(['status' => 'success','message'=>'Data fatch successfully','data'=>$data]);
   }
   public function footermenuright(){
        $data = CreatePage::where(['status'=>1])->select('id','slug','name')->skip(3)->limit(10)->get();
        return response()->json(['status' => 'success','message'=>'Data fatch successfully','data'=>$data]);
   }
   public function socialmedia(){
        $data = SocialMedia::where(['status'=>1])->get();
        return response()->json(['status' => 'success','message'=>'Data fatch successfully','data'=>$data]);
   }
   public function contactinfo(){
        $data = Contact::where(['status'=>1])->first();
        return response()->json(['status' => 'success','message'=>'Data fatch successfully','data'=>$data]);
   }
   
//   Home Page Function End ====================

    public function catproduct($id){
        $category = Category::where(['status'=>1, 'id'=>$id])->select('id','name','slug')->first();
        $data = Product::where(['status'=>1, 'category_id'=>$category->id])->select('id','slug','name','old_price','new_price', 'category_id')->with('image')->orderBy('id','DESC')->get();
        return response()->json(['status' => 'success','message'=>'Data fatch successfully','data'=>$data, 'category'=>$category]);
    }
   
   
   
    
}













