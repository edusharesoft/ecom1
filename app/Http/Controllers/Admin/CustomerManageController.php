<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Models\Customer;
use App\Models\IpBlock;
use App\Models\Payment;
use App\Models\Shipping;
use Illuminate\Support\Facades\DB;
use Toastr;
use Image;
use File;
use Auth;
use Hash;
class CustomerManageController extends Controller
{
    public function index(Request $request){
        if($request->keyword){
            $show_data = Customer::orWhere('phone',$request->keyword)->orWhere('name',$request->keyword)->paginate(20);
        }else{
             $show_data = Customer::paginate(20);
        }
       
        return view('backEnd.customer.index',compact('show_data'));
    }

    public function edit($id){
        $edit_data = Customer::find($id);
        return view('backEnd.customer.edit',compact('edit_data'));
    }
    
    public function update(Request $request){
        $this->validate($request, [
            'name' => 'required',
            'phone' => 'required',
            'email' => 'required',
        ]);

        $input = $request->except('hidden_id');
        $update_data = Customer::find($request->hidden_id);
        // new password
        
        
        if(!empty($input['password'])){ 
            $input['password'] = Hash::make($input['password']);
        }else{
            $input = Arr::except($input,array('password'));    
        }

        // new image
        $image = $request->file('image');
        if($image){
            // image with intervention 
            $name =  time().'-'.$image->getClientOriginalName();
            $name = preg_replace('"\.(jpg|jpeg|png|webp)$"', '.webp',$name);
            $name = strtolower(preg_replace('/\s+/', '-', $name));
            $uploadpath = 'public/uploads/customer/';
            $imageUrl = $uploadpath.$name; 
            $img=Image::make($image->getRealPath());
            $img->encode('webp', 90);
            $width = 100;
            $height = 100;
            $img->height() > $img->width() ? $width=null : $height=null;
            $img->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
            });
            $img->save($imageUrl);
            $input['image'] = $imageUrl;
            File::delete($update_data->image);
        }else{
            $input['image'] = $update_data->image;
        }
        $input['status'] = $request->status?1:0;
        $update_data->update($input);

        Toastr::success('Success','Data update successfully');
        return redirect()->route('customers.index');
    }
 
    public function inactive(Request $request){
        $inactive = Customer::find($request->hidden_id);
        $inactive->status = 'inactive';
        $inactive->save();
        Toastr::success('Success','Data inactive successfully');
        return redirect()->back();
    }
    public function active(Request $request){
        $active = Customer::find($request->hidden_id);
        $active->status = 'active';
        $active->save();
        Toastr::success('Success','Data active successfully');
        return redirect()->back();
    }
    public function profile(Request $request){
        $profile = Customer::with('orders')->find($request->id);
        return view('backEnd.customer.profile',compact('profile'));
    }
    public function adminlog(Request $request){
        $customer = Customer::find($request->hidden_id);
        Auth::guard('customer')->loginUsingId($customer->id);
        return redirect()->route('customer.account');
    }
    public function ip_block(Request $request){
        $data = IpBlock::get();
        // Query parameter থেকে IP এবং reason নেওয়া
        $prefillIp = $request->query('ip');
        $prefillReason = $request->query('reason', 'ফেইক অর্ডার');
        return view('backEnd.reports.ipblock',compact('data', 'prefillIp', 'prefillReason'));
    }
    public function ipblock_store(Request $request){

        $store_data = new IpBlock();
        $store_data->ip_no = $request->ip_no;
        $store_data->reason = $request->reason;
        $store_data->save();
        Toastr::success('Success','IP address add successfully');
        return redirect()->back();
    }
    public function ipblock_update(Request $request){
        $update_data = IpBlock::find($request->id);
        if (!$update_data) {
            Toastr::error('Error','Record not found');
            return redirect()->back();
        }
        $update_data->ip_no = $request->ip_no;
        $update_data->reason = $request->reason;
        $update_data->save();
        Toastr::success('Success','IP address update successfully');
        return redirect()->back();
    }
    public function ipblock_destroy(Request $request){
        $delete_data = IpBlock::find($request->id);
        if (!$delete_data) {
            Toastr::error('Error','Record not found');
            return redirect()->back();
        }
        $delete_data->delete();
        Toastr::success('Success','IP address delete successfully');
        return redirect()->back();
    }

    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $this->removeCustomer($customer);

        Toastr::success('Success', 'Customer deleted successfully');
        return redirect()->route('customers.index');
    }

    public function destroyAll(Request $request)
    {
        $request->validate([
            'confirm' => 'required|in:DELETE',
        ]);

        $deleted = 0;

        DB::transaction(function () use (&$deleted) {
            Customer::query()->orderBy('id')->chunkById(100, function ($customers) use (&$deleted) {
                foreach ($customers as $customer) {
                    $this->removeCustomer($customer);
                    $deleted++;
                }
            });
        });

        Toastr::success('Success', "Deleted {$deleted} customer(s) successfully");
        return redirect()->route('customers.index');
    }

    protected function removeCustomer(Customer $customer): void
    {
        if ($customer->image && File::exists($customer->image)) {
            File::delete($customer->image);
        }

        Payment::where('customer_id', $customer->id)->delete();
        Shipping::where('customer_id', $customer->id)->delete();

        $customer->tokens()->delete();
        $customer->roles()->detach();
        $customer->permissions()->detach();

        $customer->delete();
    }
    
    // AJAX method for quick IP block from order page
    public function ipblock_quick_store(Request $request){
        try {
            $ip = $request->ip;
            $reason = $request->reason ?? 'ফেইক অর্ডার';
            
            if(!$ip){
                return response()->json([
                    'status' => 'error',
                    'message' => 'IP address is required'
                ], 400);
            }
            
            // Check if IP already blocked
            $existing = IpBlock::where('ip_no', $ip)->first();
            if($existing){
                return response()->json([
                    'status' => 'error',
                    'message' => 'This IP is already blocked'
                ], 400);
            }
            
            $store_data = new IpBlock();
            $store_data->ip_no = $ip;
            $store_data->reason = $reason;
            $store_data->save();
            
            return response()->json([
                'status' => 'success',
                'message' => 'IP address blocked successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to block IP: ' . $e->getMessage()
            ], 500);
        }
    }
}
