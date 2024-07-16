<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use DB;

class RolesController extends Controller
{
    function __construct()
    {
//        $this->middleware('permission:role-list|role-create|role-edit|role-delete', ['only' => ['index', 'store']]);
//        $this->middleware('permission:role-create', ['only' => ['create', 'store']]);
//        $this->middleware('permission:role-edit', ['only' => ['edit', 'store']]);
//        $this->middleware('permission:role-delete', ['only' => ['destroy']]);
    }

    /**
     * Roles List with pagination.
     *
     * @param array $request
     * @return json array response
     */
    public function roles(Request $request)
    {
        $roles = Role::all();
        return response()->json([
            'title' => 'Success.',
            'message' => 'Roles List.',
            'roles' => $roles
        ], 200);

    }

    /**
     * Save or update Role details Permission
     *
     * @param array $request
     * @return json array response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:roles,name'
        ]);
        $msg = 'Role created successfully.';
        $role = Role::create(['name' => $request->input('name'), 'guard_name' => 'api']);

        // $role->syncPermissions($request->input('rolePermissions'));

        return response()->json([
            'title' => $msg,
            'message' => $msg
        ], 200);

    }

    /**
     * Roles details with Permissions by ID
     *
     * @param Int $id
     * @return json array response
     */
    public function getRoleById($id)
    {
        $role = Role::find($id);

        $role['rolePermissions'] = Permission::join("role_has_permissions", "role_has_permissions.permission_id", "=", "permissions.id")
            ->where("role_has_permissions.role_id", $id)
            ->pluck('id')->toArray();

        return response()->json([
            'title' => 'Success.',
            'message' => 'Role Details.',
            'role' => $role
        ], 200);
    }

    public function update(Request $request, $roleId)
    {
        dd($request->all(),$roleId );
        $role = Role::findById($roleId);
        $roleName = $request->input('name');
        // dd($roleName);
        $role->name = $roleName;
        $role->save();

        return response()->json(['message' => 'Role updated successfully']);
    }

    /**
     * Delete role
     *
     * @param Int $id
     * @return json array response
     */
    public function destroy($id)
    {
        DB::table("roles")->where('id', $id)->delete();

        return response()->json([
            'title' => 'Success.',
            'message' => 'Role Deleted.',
            'data' => ''
        ], 200);
    }
}
