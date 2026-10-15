<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\GeneralSetting;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Toastr;
use Image;
use File;
use DB;
class GeneralSettingController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:setting-list|setting-create|setting-edit|setting-delete', ['only' => ['index','store']]);
        $this->middleware('permission:setting-create', ['only' => ['create','store']]);
        $this->middleware('permission:setting-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:setting-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        // ✅ Single record pattern: redirect directly to edit/create without listing/loop
        $setting = GeneralSetting::orderBy('id', 'desc')->first();

        if ($setting) {
            return redirect()->route('settings.edit', $setting->id);
        }

        return redirect()->route('settings.create');
    }
    public function create()
    {
        return view('backEnd.settings.create');
    }
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',

			'fraud_api_key' => 'required',
			'copyright_color' => 'required',
			'primary_color' => 'required',
			'secodery_color' => 'required',
			'footer_color' => 'required',
			'facebook_page_username' => 'required',
			
            'white_logo' => 'required',
			'og_baner' => 'required',
            'favicon' => 'required',
            'status' => 'required',
        ]);

        // image with intervention 
        $image = $request->file('white_logo');
        $name =  time().'-'.$image->getClientOriginalName();
        $name = preg_replace('"\.(jpg|jpeg|png|webp)$"', '.webp',$name);
        $name = strtolower(preg_replace('/\s+/', '-', $name));
        $uploadpath = 'public/uploads/settings/';
        $imageUrl = $uploadpath.$name; 
        $img=Image::make($image->getRealPath());
        $img->encode('webp', 90);
        $width = '';
        $height = '';
        $img->height() > $img->width() ? $width=null : $height=null;
        $img->resize($width, $height);
        $img->save($imageUrl);

        // dark logo
        $image2 = $request->file('dark_logo');
        $name2 =  time().'-'.$image2->getClientOriginalName();
        $name2 = preg_replace('"\.(jpg|jpeg|png|webp)$"', '.webp',$name2);
        $name2 = strtolower(preg_replace('/\s+/', '-', $name2));
        $uploadpath2 = 'public/uploads/settings/';
        $image2Url = $uploadpath2.$name2; 
        $img2=Image::make($image2->getRealPath());
        $img2->encode('webp', 90);
        $width2 = '';
        $height2 = '';
        $img2->height() > $img2->width() ? $width2=null : $height2=null;
        $img2->resize($width2, $height2);
        $img2->save($image2Url);

        // OG Baner
        $image4 = $request->file('og_baner');
        $name4 =  time().'-'.$image4->getClientOriginalName();
        $name4 = preg_replace('"\.(jpg|jpeg|png|webp)$"', '.webp',$name4);
        $name4 = strtolower(preg_replace('/\s+/', '-', $name4));
        $uploadpath4 = 'public/uploads/settings/';
        $image4Url = $uploadpath4.$name4; 
        $img4=Image::make($image4->getRealPath());
        $img4->encode('webp', 90);
        $width4 = '';
        $height4 = '';
        $img4->height() > $img4->width() ? $width4=null : $height4=null;
        $img4->resize($width4, $height4);
        $img4->save($image4Url);


        // image with intervention 
        $image3 = $request->file('favicon');
        $name3 =  time().'-'.$image3->getClientOriginalName();
        $name3 = preg_replace('"\.(jpg|jpeg|png|webp)$"', '.png',$name3);
        $name3 = strtolower(preg_replace('/\s+/', '-', $name3));
        $uploadpath3 = 'public/uploads/settings/';
        $image3Url = $uploadpath3.$name3; 
        $img3=Image::make($image3->getRealPath());
        //$img3->encode('webp', 90);
        $width3 = 256;
        $height3 = 256;
        //$img3->height() > $img3->width() ? $width3=null : $height3=null;
        //$img3->resize($width3, $height3);
        $img3->save($image3Url);

        $input = $request->all();
        $input['white_logo'] = $imageUrl;
        $input['dark_logo'] = $image2Url;
        $input['favicon'] = $image3Url;
		 $input['og_baner'] = $image4Url;
        
        $input['vendor_enabled'] = $request->has('vendor_enabled') ? 1 : 0;
        $input['reseller_enabled'] = $request->has('reseller_enabled') ? 1 : 0;
        $input['checkout_otp_enabled'] = $request->has('checkout_otp_enabled') ? 1 : 0;
        $input['news_ticker_enabled'] = $request->has('news_ticker_enabled') ? 1 : 0;
        $input['homepage_brands_enabled'] = $request->has('homepage_brands_enabled') ? 1 : 0;
        $input['homepage_vendors_enabled'] = $request->has('homepage_vendors_enabled') ? 1 : 0;
        $input['homepage_blogs_enabled'] = $request->has('homepage_blogs_enabled') ? 1 : 0;

        GeneralSetting::create($input);

        // APP_NAME sync
        if (!empty($input['name'])) {
            $this->updateEnvAppName($input['name']);
        }

        Toastr::success('Success','Data insert successfully');
        return redirect()->route('settings.index');
    }
    
    public function edit($id)
    {
        $edit_data = GeneralSetting::find($id);
        return view('backEnd.settings.edit',compact('edit_data'));
    }
    
    public function update(Request $request)
    {
        $this->validate($request, [
            'name' => 'required'
        ]);
        $update_data = GeneralSetting::find($request->id);
        $input = $request->all();
        // new white logo
        $image = $request->file('white_logo');
        if($image){
            // image with intervention 
            $image = $request->file('white_logo');
            $name =  time().'-'.$image->getClientOriginalName();
            $name = preg_replace('"\.(jpg|jpeg|png|webp)$"', '.webp',$name);
            $name = strtolower(preg_replace('/\s+/', '-', $name));
            $uploadpath = 'public/uploads/settings/';
            $imageUrl = $uploadpath.$name; 
            $img=Image::make($image->getRealPath());
            $img->encode('webp', 90);
            $width = '';
            $height = '';
            $img->height() > $img->width() ? $width=null : $height=null;
            $img->resize($width, $height);
            $img->save($imageUrl);
            $input['white_logo'] = $imageUrl;
        }else{
            $input['white_logo'] = $update_data->white_logo;
        }
        // new dark logo
        $image2 = $request->file('dark_logo');
        if($image2){
            // image with intervention 
            $image2 = $request->file('dark_logo');
            $name2 =  time().'-'.$image2->getClientOriginalName();
            $name2 = preg_replace('"\.(jpg|jpeg|png|webp)$"', '.webp',$name2);
            $name2 = strtolower(preg_replace('/\s+/', '-', $name2));
            $uploadpath2 = 'public/uploads/settings/';
            $image2Url = $uploadpath2.$name2; 
            $img2=Image::make($image2->getRealPath());
            $img2->encode('webp', 90);
            $width2 = '';
            $height2 = '';
            $img2->height() > $img2->width() ? $width2=null : $height2=null;
            $img2->resize($width2, $height2);
            $img2->save($image2Url);
            $input['dark_logo'] = $image2Url;
        }else{
            $input['dark_logo'] = $update_data->dark_logo;
        }

			// new OG image
        $image4 = $request->file('og_baner');
        if($image4){
            $image4 = $request->file('og_baner');
            $name4 =  time().'-'.$image4->getClientOriginalName();
            $name4 = preg_replace('"\.(jpg|jpeg|png|webp)$"', '.webp',$name4);
            $name4 = strtolower(preg_replace('/\s+/', '-', $name4));
            $uploadpath4 = 'public/uploads/settings/';
            $image4Url = $uploadpath4.$name4; 
            $img4=Image::make($image4->getRealPath());
            $img4->encode('webp', 90);
            $width4 = 1440;
            $height4 = 793;
            $img4->height() > $img4->width() ? $width4=null : $height4=null;
            $img4->resize($width4, $height4);
            $img4->save($image4Url);
            $input['og_baner'] = $image4Url;
        }else{
            $input['og_baner'] = $update_data->og_baner;
        }




        // new favicon image
        $image3 = $request->file('favicon');
        if($image3){
            $image3 = $request->file('favicon');
            $name3 =  time().'-'.$image3->getClientOriginalName();
            $name3 = preg_replace('"\.(jpg|jpeg|png|webp)$"', '.webp',$name3);
            $name3 = strtolower(preg_replace('/\s+/', '-', $name3));
            $uploadpath3 = 'public/uploads/settings/';
            $image3Url = $uploadpath3.$name3; 
            $img3=Image::make($image3->getRealPath());
            $img3->encode('webp', 90);
            $width3 = 32;
            $height3 = 32;
            //$img3->height() > $img3->width() ? $width3=null : $height3=null;
            $img3->resize($width3, $height3);
            $img3->save($image3Url);
            $input['favicon'] = $image3Url;
        }else{
            $input['favicon'] = $update_data->favicon;
        }
        $input['status'] = 1;
        
        // Handle vendor_enabled and reseller_enabled (checkbox returns '1' if checked, null if unchecked)
        $input['vendor_enabled'] = $request->has('vendor_enabled') ? 1 : 0;
        $input['reseller_enabled'] = $request->has('reseller_enabled') ? 1 : 0;
        $input['checkout_otp_enabled'] = $request->has('checkout_otp_enabled') ? 1 : 0;
        $input['news_ticker_enabled'] = $request->has('news_ticker_enabled') ? 1 : 0;
        $input['homepage_brands_enabled'] = $request->has('homepage_brands_enabled') ? 1 : 0;
        $input['homepage_vendors_enabled'] = $request->has('homepage_vendors_enabled') ? 1 : 0;
        $input['homepage_blogs_enabled'] = $request->has('homepage_blogs_enabled') ? 1 : 0;

        $update_data->update($input);

        // APP_NAME sync: site title পরিবর্তন হলে .env আপডেট করো
        if (!empty($input['name'])) {
            $this->updateEnvAppName($input['name']);
        }

        Cache::forget('general_setting');
        Cache::forget('seo_settings');
        Cache::forget('frontend_homepage_v1');
        Cache::forget('frontend_homepage_v2');
        Cache::forget('frontend_homepage_v3');
        Cache::forget('side_categories');
        Cache::forget('menu_categories');
        Cache::forget('menu_categories_v4');
        Cache::forget('brands_list');
        Cache::forget('pages_top');
        Cache::forget('pages_right');
        Cache::forget('common_menu');

        Toastr::success('Settings updated successfully!', 'Success');
        return redirect()->route('settings.edit', $update_data->id);
    }
 
    public function inactive(Request $request)
    {
        $inactive = GeneralSetting::find($request->hidden_id);
        $inactive->status = 0;
        $inactive->save();
        Toastr::success('Success','Data inactive successfully');
        return redirect()->back();
    }
    public function active(Request $request)
    {
        $active = GeneralSetting::find($request->hidden_id);
        $active->status = 1;
        $active->save();
        Toastr::success('Success','Data active successfully');
        return redirect()->back();
    }
    public function destroy(Request $request)
    {
        $delete_data = GeneralSetting::find($request->hidden_id);
        File::delete($delete_data->image);
        $delete_data->delete();
        Toastr::success('Success','Data delete successfully');
        return redirect()->back();
    }

    private function updateEnvAppName(string $name): void
    {
        $envPath = base_path('.env');
        if (!is_file($envPath) || !is_writable($envPath)) {
            return;
        }

        $content    = file_get_contents($envPath);
        $escapedName = str_contains($name, ' ') ? '"' . addslashes($name) . '"' : $name;

        if (preg_match('/^APP_NAME=.*/m', $content)) {
            $content = preg_replace('/^APP_NAME=.*/m', 'APP_NAME=' . $escapedName, $content);
        } else {
            $content .= "\nAPP_NAME=" . $escapedName;
        }

        file_put_contents($envPath, $content);

        // OPcache & config cache clear
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }
        try {
            \Artisan::call('config:clear');
        } catch (\Throwable $e) {
            // silent
        }
    }
}
