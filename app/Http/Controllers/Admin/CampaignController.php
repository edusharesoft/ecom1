<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\ImageOptimizer;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\CampaignReview;
use App\Models\Campaign;
use Toastr;
use Str;
use File;
use Illuminate\Support\Facades\DB;

class CampaignController extends Controller
{
    public function index(Request $request)
    {
        $show_data = Campaign::orderBy('id','DESC')->get();
        return view('backEnd.campaign.index',compact('show_data'));
    }
    public function create()
    {
        $products = Product::where(['status'=>1])->select('id','name','status')->get();
        return view('backEnd.campaign.create',compact('products'));
    }
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'short_description' => 'nullable',
            'description' => 'nullable',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'image_one' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'image_two' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'image_three' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'product_id' => 'required|array|min:1|exists:products,id',
            'image.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'review' => 'required',
            'deadline' => 'nullable|date|after:now', // Ensure deadline is a future date
            'top_title_1' => 'nullable|string|max:255',
            'top_title_2' => 'nullable|string|max:255',
            'heading_1' => 'nullable|string|max:255',
            'feature_1' => 'nullable|string|max:255',
            'feature_2' => 'nullable|string|max:255',
            'heading_2' => 'nullable|string|max:255',
            'heading_3' => 'nullable|string|max:255',
            'heading_4' => 'nullable|string|max:255',
            'note' => 'nullable|string|max:255',
            'billing_details' => 'nullable|string|max:255',
        
        ]);
    
        // Prepare the input data
        $input = $request->except('image', 'product_id');
        $input['status'] = true; // Set status to true if not checked
    
        // Handle the first selected product ID
        $firstProductId = $request->product_id[0];
        $input['product_id'] = $firstProductId;
    
        // Handle Banner Image
        if ($request->hasFile('banner')) {
            $input['banner'] = ImageOptimizer::store($request->file('banner'), 'public/uploads/campaign/');
        }
    
        // Handle Image One
        if ($request->hasFile('image_one')) {
            $input['image_one'] = ImageOptimizer::store($request->file('image_one'), 'public/uploads/campaign/');
        }
    
        // Handle Image Two
        if ($request->hasFile('image_two')) {
            $input['image_two'] = ImageOptimizer::store($request->file('image_two'), 'public/uploads/campaign/');
        }
    
        // Handle Image Three
        if ($request->hasFile('image_three')) {
            $input['image_three'] = ImageOptimizer::store($request->file('image_three'), 'public/uploads/campaign/');
        }
    
        // Create slug
        $input['slug'] = strtolower(Str::slug($request->name));
        $input['video'] = $this->getYouTubeVideoId($request->video);
    
        // Create a new campaign
        $campaign = Campaign::create($input);
        // Attach remaining selected products to the pivot table
        $remainingProductIds = array_slice($request->product_id, 1);
        if (!empty($remainingProductIds)) {
            $campaign->products()->attach($remainingProductIds);
        }
    
        // Handle additional images (review images)
        if ($request->hasFile('image')) {
            foreach ($request->file('image') as $image) {
                $imageUrl = ImageOptimizer::store($image, 'public/uploads/campaign/');

                $pimage = new CampaignReview();
                $pimage->campaign_id = $campaign->id;
                $pimage->image = $imageUrl;
                $pimage->save();
            }
        }
    
        Toastr::success('Success', 'Campaign created successfully');
        return redirect()->route('campaign.index');
    }

    
    
    public function edit($id)
    {
        // Fetch the campaign with its related images and products
        $edit_data = Campaign::with('images')->findOrFail($id);
    
     
        $select_products = DB::select('
            SELECT products.id, products.name, products.status 
            FROM products
            INNER JOIN campaign_product ON products.id = campaign_product.product_id
            WHERE campaign_product.campaign_id = ?
        ', [$id]);

    
        // Fetch all available products
        $products = Product::where('status', 1)->select('id', 'name', 'status')->get();
    
        return view('backEnd.campaign.edit', compact('edit_data', 'products','select_products'));
    }

    
    public function update(Request $request)
    { 
         $this->validate($request, [
            'name' => 'required',
            'short_description' => 'nullable',
            'description' => 'nullable',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'image_one' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'image_two' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'image_three' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'product_id' => 'required|array|min:1|exists:products,id',
            'image.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'review' => 'required',
            'deadline' => 'nullable|date',
            'top_title_1' => 'nullable|string|max:255',
            'top_title_2' => 'nullable|string|max:255',
            'heading_1' => 'nullable|string|max:255',
            'feature_1' => 'nullable|string|max:255',
            'feature_2' => 'nullable|string|max:255',
            'heading_2' => 'nullable|string|max:255',
            'heading_3' => 'nullable|string|max:255',
            'heading_4' => 'nullable|string|max:255',
            'note' => 'nullable|string|max:255',
            'billing_details' => 'nullable|string|max:255',
        ]);
        // image one
        $update_data = Campaign::find($request->hidden_id);
        $input = $request->except('hidden_id','product_ids','files','image');
        $input['status'] = $request->has('status') ? 1 : 0;
        $input['video'] = $this->getYouTubeVideoId($request->video);
        $input['product_id'] = $request->product_id[0];
        
          // Handle Banner Image
        if ($request->hasFile('banner')) {
            $input['banner'] = ImageOptimizer::store($request->file('banner'), 'public/uploads/campaign/');
            File::delete($update_data->banner);
        } else {
            $input['banner'] = $update_data->banner;
        }
        if ($request->hasFile('image_one')) {
            $input['image_one'] = ImageOptimizer::store($request->file('image_one'), 'public/uploads/campaign/');
            File::delete($update_data->image_one);
        } else {
            $input['image_one'] = $update_data->image_one;
        }
        if ($request->hasFile('image_two')) {
            $input['image_two'] = ImageOptimizer::store($request->file('image_two'), 'public/uploads/campaign/');
            File::delete($update_data->image_two);
        } else {
            $input['image_two'] = $update_data->image_two;
        }
        if ($request->hasFile('image_three')) {
            $input['image_three'] = ImageOptimizer::store($request->file('image_three'), 'public/uploads/campaign/');
            File::delete($update_data->image_three);
        } else {
            $input['image_three'] = $update_data->image_three;
        }
        // image four
        $input['slug'] = strtolower(Str::slug($request->name));
        $input['video'] = $this->getYouTubeVideoId($request->video);
        $update_data = Campaign::find($request->hidden_id);
        $update_data->update($input);
        
        // Sync remaining selected products to the pivot table
        $remainingProductIds = array_slice($request->product_id, 1);
        $update_data->products()->sync($remainingProductIds);

        $images = $request->file('image');  
        if($images){
            foreach ($images as $key => $image) {
                $imageUrl = ImageOptimizer::store($image, 'public/uploads/campaign/');

                $pimage             = new CampaignReview();
                $pimage->campaign_id = $update_data->id;
                $pimage->image      = $imageUrl;
                $pimage->save();
            }
        }

        Toastr::success('Success','Data update successfully');
        return redirect()->route('campaign.index');
    }
 
    public function inactive(Request $request)
    {
        $inactive = Campaign::find($request->hidden_id);
        $inactive->status = 0;
        $inactive->save();
        Toastr::success('Success','Data inactive successfully');
        return redirect()->back();
    }
    public function active(Request $request)
    {
        $active = Campaign::find($request->hidden_id);
        $active->status = 1;
        $active->save();
        Toastr::success('Success','Data active successfully');
        return redirect()->back();
    }
    public function destroy(Request $request)
    {
       
        $delete_data = Campaign::find($request->hidden_id);
        $delete_data->delete();
        
        $campaign = Product::whereNotNull('campaign_id')->get();
        foreach($campaign as $key=>$value){
            $product = Product::find($value->id);
            $product->campaign_id = null;
            $product->save();
        }
        Toastr::success('Success','Data delete successfully');
        return redirect()->back();
    }
    public function imgdestroy(Request $request)
    { 
        $delete_data = CampaignReview::find($request->id);
        File::delete($delete_data->image);
        $delete_data->delete();
        Toastr::success('Success','Data delete successfully');
        return redirect()->back();
    } 
    public function getYouTubeVideoId($input)
    {
        // Check if the input is a valid YouTube video ID (11 characters long)
        if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $input)) {
            return $input; // Return the ID directly if it's valid
        }
    
        // Regular expression to match YouTube video URLs
        $pattern = '/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/';
        
        // Execute the regex pattern
        preg_match($pattern, $input, $matches);
        
        // Check if a match was found and return the video ID or null
        return isset($matches[1]) ? $matches[1] : null;
    }

}
