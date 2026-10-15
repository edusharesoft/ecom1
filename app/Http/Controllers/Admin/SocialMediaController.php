<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SocialMedia;
use Toastr;
use Illuminate\Support\Facades\Auth;
class SocialMediaController extends Controller
{
    function __construct()
    {
         // Super Admin (id=1) bypass permission check via AppServiceProvider Gate::before
         $this->middleware('permission:social-list|social-create|social-edit|social-delete', ['only' => ['index','store']]);
         $this->middleware('permission:social-create', ['only' => ['create','store']]);
         $this->middleware('permission:social-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:social-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $show_data = SocialMedia::orderBy('id','DESC')->get();
        return view('backEnd.socialmedia.index',compact('show_data'));
    }
    public function create()
    {
        return view('backEnd.socialmedia.create');
    }
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'icon' => 'required',
            'status' => 'required',
        ]);
        $input = $request->all();
        SocialMedia::create($input);
        Toastr::success('Success','Data insert successfully');
        return redirect()->route('socialmedias.index');
    }
    
    public function edit($id)
    {
        $edit_data = SocialMedia::find($id);
        return view('backEnd.socialmedia.edit',compact('edit_data'));
    }
    
    public function update(Request $request)
    {
        try {
            $this->validate($request, [
                'title' => 'required',
                'icon' => 'required',
                'status' => 'sometimes|in:0,1', // Allow 0 or 1
            ]);
            
            $update_data = SocialMedia::findOrFail($request->id);
            
            // Ensure status is set (0 or 1)
            $input = $request->all();
            $input['status'] = $request->has('status') && $request->status == '1' ? 1 : 0;
            
            $update_data->update($input);

            Toastr::success('Success','Data update successfully');
        } catch (\Exception $e) {
            Toastr::error('Error','Failed to update social media. Please try again.');
        }
        
        return redirect()->route('socialmedias.index');
    }
 
    public function inactive(Request $request)
    {
        try {
            $request->validate([
                'hidden_id' => 'required|exists:social_media,id'
            ]);

            $inactive = SocialMedia::findOrFail($request->hidden_id);
            $inactive->status = 0;
            $inactive->save();
            
            Toastr::success('Success','Data inactive successfully');
        } catch (\Exception $e) {
            Toastr::error('Error','Failed to deactivate social media. Please try again.');
        }
        
        return redirect()->back();
    }
    
    public function active(Request $request)
    {
        try {
            $request->validate([
                'hidden_id' => 'required|exists:social_media,id'
            ]);

            $active = SocialMedia::findOrFail($request->hidden_id);
            $active->status = 1;
            $active->save();
            
            Toastr::success('Success','Data active successfully');
        } catch (\Exception $e) {
            Toastr::error('Error','Failed to activate social media. Please try again.');
        }
        
        return redirect()->back();
    }
    public function destroy(Request $request)
    {
        try {
            $request->validate([
                'hidden_id' => 'required|exists:social_media,id'
            ]);

            $delete_data = SocialMedia::findOrFail($request->hidden_id);
            $delete_data->delete();
            
            Toastr::success('Success','Data delete successfully');
        } catch (\Exception $e) {
            Toastr::error('Error','Failed to delete social media. Please try again.');
        }
        
        return redirect()->back();
    }
}
