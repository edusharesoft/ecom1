<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Toastr;
use DB;
class RoleController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:role-list|role-create|role-edit|role-delete', ['only' => ['index','store']]);
         $this->middleware('permission:role-create', ['only' => ['create','store']]);
         $this->middleware('permission:role-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:role-delete', ['only' => ['destroy']]);
    }
    
    public function index(Request $request)
    {
        $show_data = Role::orderBy('id','DESC')->get();
        return view('backEnd.roles.index',compact('show_data'));
    }
    
    public function create()
    {
        // ✅ Get all permissions for admin guard
        // Clear cache first to ensure fresh data
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        // Try multiple queries to debug
        $permissionQuery = Permission::where('guard_name', 'admin');
        $permissionCount = $permissionQuery->count();
        
        $permission = $permissionQuery->orderBy('name', 'ASC')->get();
        
        // Debug: Log permission count and first few permissions
        \Log::info('Role Create - Query Count: ' . $permissionCount);
        \Log::info('Role Create - Collection Count: ' . $permission->count());
        \Log::info('Role Create - First 5 Permissions: ' . $permission->take(5)->pluck('name')->implode(', '));
        
        // If no permissions found, try without guard filter
        if ($permission->isEmpty()) {
            \Log::warning('Role Create - No admin guard permissions found, trying all permissions');
            $permission = Permission::orderBy('name', 'ASC')->get();
            \Log::info('Role Create - All Permissions Count: ' . $permission->count());
        }
        
        // Ensure we have a collection (not null)
        if (!$permission) {
            $permission = collect([]);
        }
        
        // Debug: Add permission count to view
        return view('backEnd.roles.create',compact('permission'))->with('permission_count', $permission->count());
    }
    
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:roles,name,NULL,id,guard_name,admin',
            'permission' => 'required',
        ]);
    
        // ✅ Create role with admin guard_name (default for admin panel)
        $role = Role::create([
            'name' => $request->input('name'),
            'guard_name' => 'admin'
        ]);
        
        // ✅ Process permissions (handles both IDs and names)
        $permissionInput = $request->input('permission', []);
        $permissions = $this->processPermissions($permissionInput);
        
        if (empty($permissions)) {
            Toastr::error('Error', 'No valid permissions found. Please check your permission selection.');
            return redirect()->back()->withInput();
        }
        
        // ✅ Sync permissions with error handling
        try {
            $role->syncPermissions($permissions);
        } catch (\Exception $e) {
            \Log::error('Permission sync error', [
                'role_id' => $role->id,
                'permissions' => $permissions,
                'error' => $e->getMessage()
            ]);
            
            // Try to sync again after clearing cache
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
            $role->syncPermissions($permissions);
        }
        
        // ✅ Clear permission cache after create
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        Toastr::success('Success','Data store successfully');
        return redirect()->route('roles.index');
    }
    
    public function show($id)
    {
        $role = Role::find($id);
        $rolePermissions = Permission::join("role_has_permissions","role_has_permissions.permission_id","=","permissions.id")
            ->where("role_has_permissions.role_id",$id)
            ->get();
    
        return view('backEnd.roles.show',compact('role','rolePermissions'));
    }
    public function edit($id)
    {
        // ✅ Load role with permissions (eager load)
        $edit_data = Role::with('permissions')->findOrFail($id);
        
        // Clear cache first to ensure fresh data
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        // ✅ Get all permissions matching the role's guard_name
        // If role doesn't have guard_name, default to 'admin'
        $guardName = $edit_data->guard_name ?? 'admin';
        $permission = Permission::where('guard_name', $guardName)
            ->orderBy('name', 'ASC')
            ->get();
        
        // Debug: Log permission count
        \Log::info('Role Edit - Role ID: ' . $id . ', Guard: ' . $guardName . ', Permissions Count: ' . $permission->count());
        
        return view('backEnd.roles.edit',compact('edit_data','permission'));
    }
    
    public function update(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'permission' => 'required',
        ]);
        $input = $request->except('hidden_id', 'permission');
        $update_data = Role::find($request->hidden_id);
        if (!$update_data) {
            Toastr::error('Error','Record not found');
            return redirect()->back();
        }
        $update_data->update($input);
    
        // ✅ Process permissions (handles both IDs and names)
        $permissionInput = $request->input('permission', []);
        $permissions = $this->processPermissions($permissionInput);
        
        if (empty($permissions)) {
            Toastr::error('Error', 'No valid permissions found. Please check your permission selection.');
            return redirect()->back()->withInput();
        }
        
        // ✅ Clear permission cache before sync to avoid conflicts
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        // ✅ Sync permissions with error handling
        try {
            $update_data->syncPermissions($permissions);
        } catch (\Exception $e) {
            \Log::error('Permission sync error', [
                'role_id' => $update_data->id,
                'permissions' => $permissions,
                'error' => $e->getMessage()
            ]);
            
            // If duplicate error, manually sync using DB
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                DB::table('role_has_permissions')
                    ->where('role_id', $update_data->id)
                    ->delete();
                $update_data->syncPermissions($permissions);
            } else {
                throw $e;
            }
        }
        
        // ✅ Clear permission cache after update
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        Toastr::success('Success','Data update successfully');
        return redirect()->route('roles.index');
    }
    public function destroy(Request $request)
    {
        $delete_data = Role::find($request->hidden_id);
        if (!$delete_data) {
            Toastr::error('Error','Record not found');
            return redirect()->back();
        }
        $delete_data->delete();
        Toastr::success('Success','Data delete successfully');
        return redirect()->back();
    }
    
    /**
     * ✅ Helper method to process permission input (handles both IDs and names)
     */
    private function processPermissions($permissionInput)
    {
        $permissionNames = [];
        
        foreach ($permissionInput as $value) {
            if (is_numeric($value)) {
                // If numeric, treat as ID - check admin guard first, then any guard
                $permission = Permission::where('id', (int)$value)
                    ->where('guard_name', 'admin')
                    ->first();
                
                // If not found with admin guard, try without guard check (for compatibility)
                if (!$permission) {
                    $permission = Permission::where('id', (int)$value)->first();
                }
                
                if ($permission) {
                    $permissionNames[] = $permission->name;
                }
            } elseif (is_string($value) && !empty(trim($value))) {
                // If string, check if it's a valid permission name
                $permission = Permission::where('name', trim($value))
                    ->where('guard_name', 'admin')
                    ->first();
                
                // If not found with admin guard, try without guard check
                if (!$permission) {
                    $permission = Permission::where('name', trim($value))->first();
                }
                
                if ($permission) {
                    $permissionNames[] = $permission->name;
                }
            }
        }
        
        // Remove duplicates and return
        return array_values(array_unique($permissionNames));
    }
}
