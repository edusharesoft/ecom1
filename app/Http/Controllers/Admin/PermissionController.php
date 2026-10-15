<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Toastr;
use DB;
class PermissionController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:permission-list|permission-create|permission-edit|permission-delete', ['only' => ['index','store']]);
         $this->middleware('permission:permission-create', ['only' => ['create','store']]);
         $this->middleware('permission:permission-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:permission-delete', ['only' => ['destroy']]);
    }
    
    public function index(Request $request)
    {
        // ✅ Show only admin guard permissions in admin panel
        $show_data = Permission::where('guard_name', 'admin')->orderBy('id','DESC')->get();
        return view('backEnd.permissions.index',compact('show_data'));
    }
    
    public function create()
    {
        return view('backEnd.permissions.create');
    }
    
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:permissions,name,NULL,id,guard_name,admin',
        ]);
        $input = $request->all();
        // ✅ Set guard_name to 'admin' to match RoleController
        $input['guard_name'] = 'admin';
        $insert = Permission::create($input);
        
        // ✅ Clear permission cache after create
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        Toastr::success('Success','Data store successfully');
        return redirect()->route('permissions.index');
    }
    
    public function edit($id)
    {
        $edit_data = Permission::find($id);
        return view('backEnd.permissions.edit',compact('edit_data'));
    }
    
    public function update(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:permissions,name,'.$request->hidden_id.',id,guard_name,admin',
        ]);
        $input = $request->except('hidden_id');
        // ✅ Set guard_name to 'admin' to match RoleController
        $input['guard_name'] = 'admin';
        $update_data = Permission::find($request->hidden_id);
        if (!$update_data) {
            Toastr::error('Error','Record not found');
            return redirect()->back();
        }
        $update_data->update($input);
        
        // ✅ Clear permission cache after update
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        Toastr::success('Success','Data update successfully');
        return redirect()->route('permissions.index');
    }
    public function destroy(Request $request)
    {
        $delete_data = Permission::find($request->hidden_id);
        if (!$delete_data) {
            Toastr::error('Error','Record not found');
            return redirect()->back();
        }
        $delete_data->delete();
        Toastr::success('Success','Data delete successfully');
        return redirect()->back();
    }
}
