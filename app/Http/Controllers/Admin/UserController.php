<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Arr;
use App\Models\User;
use Toastr;
use Image;
use File;
use DB;
use Hash;
class UserController extends Controller
{
    public function index(Request $request)
    {
        // ✅ Filter out Vendor and Reseller users - only show Admin/Staff users
        $data = User::whereNull('vendor_id') // Exclude vendor users
            ->where(function($query) {
                $query->where('role', '!=', 'reseller')
                      ->orWhereNull('role');
            })
            ->whereDoesntHave('roles', function($q) {
                $q->whereIn('name', ['vendor', 'reseller']);
            })
            ->orderBy('id','DESC')
            ->get();
        
        return view('backEnd.users.index',compact('data'));
    }
    
    public function create()
    {
        $roles = Role::select('name')->get();
        return view('backEnd.users.create',compact('roles'));
    }
    
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:confirm-password',
            'roles' => 'required'
        ]);
        // image with intervention 
        $image = $request->file('image');
        $name =  time().'-'.$image->getClientOriginalName();
        $name = preg_replace('"\.(jpg|jpeg|png|webp)$"', '.webp',$name);
        $name = strtolower(preg_replace('/\s+/', '-', $name));
        $uploadpath = 'public/uploads/users/';
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

        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $input['image'] = $imageUrl;
        
        $user = User::create($input);
        $user->assignRole($request->input('roles'));
        Toastr::success('Success','Data insert successfully');
        return redirect()->route('users.index');
    }
    
    public function edit($id)
    {
        $edit_data = User::find($id);
        $roles = Role::get();
        return view('backEnd.users.edit',compact('edit_data','roles'));
    }
    
    public function update(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$request->hidden_id,
            'password' => 'same:confirm-password',
            'roles' => 'required'
        ]);
        
        $update_data = User::find($request->hidden_id);
        if (!$update_data) {
            Toastr::error('Error','Record not found');
            return redirect()->back();
        }

        // new password
        $input = $request->except('hidden_id');
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
            $uploadpath = 'public/uploads/users/';
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

        // role asign
        DB::table('model_has_roles')->where('model_id',$request->hidden_id)->delete();
        $update_data->assignRole($request->input('roles'));
        Toastr::success('Success','Data update successfully');
        return redirect()->route('users.index');
    }
 
    public function inactive(Request $request)
    {
        $inactive = User::find($request->hidden_id);
        $inactive->status = 0;
        $inactive->save();
        Toastr::success('Success','Data inactive successfully');
        return redirect()->back();
    }
    public function active(Request $request)
    {
        $active = User::find($request->hidden_id);
        $active->status = 1;
        $active->save();
        Toastr::success('Success','Data active successfully');
        return redirect()->back();
    }
    public function destroy(Request $request)
    {

        $delete_data = User::find($request->hidden_id);
        if($delete_data->id!=1){
           File::delete($delete_data->image);
            $delete_data->delete();
            Toastr::success('Success','Data delete successfully'); 
        }else{
            Toastr::success('error','Data delete unsuccessfully'); 
        }
        
        return redirect()->back();
    }
}
