<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;

// use Spatie\Permission\Models\Role;

class RolesController extends Controller
{
    public function __construct()
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
        return response()->json($roles);

    }

    /**
     * Save or update Role details Permission
     *
     * @param array $request
     * @return json array response
     */
    public function store(Request $request)
    {
        // Validate the incoming request
        $this->validate($request, [
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'required|array|min:1',
        ]);

        // Create the new role
        $role = Role::create([
            'name' => $request->input('name'),
            'guard_name' => 'api',
        ]);

        // Assign permissions to the new role
        $role->syncPermissions($request->input('permissions'));

        return response()->json([
            'title' => 'Success',
            'message' => 'Role created and permissions assigned successfully.',
        ], 201);
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

        if (!$role) {
            return response()->json([
                'title' => 'Error',
                'message' => 'Role not found.',
            ], 404);
        }

        $rolePermissions = Permission::join('role_has_permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
            ->where('role_has_permissions.role_id', $id)
            ->pluck('permissions.name')
            ->toArray();

        $role->rolePermissions = $rolePermissions;

        return response()->json([
            'title' => 'Success',
            'message' => 'Role Details.',
            'role' => $role,
        ], 200);
    }

    public function update(Request $request, $roleId)
    {
        $this->validate($request, [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')->ignore($roleId),
            ],
        ]);

        try {
            $role = Role::findOrFail($roleId);
            $roleName = $request->input('name');
            $role->name = $roleName;
            $role->save();

            return response()->json(['message' => 'Role updated successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Role not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred'], 500);
        }
    }
    /**
     * Delete role
     *
     * @param Int $id
     * @return json array response
     */
    public function destroy($id)
    {
        try {
            $role = Role::findOrFail($id);

            $role->delete();

            return response()->json([
                'title' => 'Success.',
                'message' => 'Role Deleted.',
                'data' => null,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'title' => 'Error.',
                'message' => 'Unable to delete role.',
                'data' => null,
            ], 500);
        }
    }
}
