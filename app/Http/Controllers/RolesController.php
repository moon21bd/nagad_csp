<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Services\RBACService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Silber\Bouncer\Database\Role as DatabaseRole;

class RolesController extends Controller
{
    protected $RBACService;

    public function __construct(RBACService $RBACService)
    {
        $this->RBACService = $RBACService;
    }

    /**
     * Roles List with pagination.
     *
     * @param array $request
     * @return json array response
     */
    public function roles(Request $request)
    {
        $roles = DatabaseRole::all();

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
        ], [
            'name.required' => 'The role name is required.',
            'name.string' => 'The role name must be a string.',
            'name.max' => 'The role name must not exceed 255 characters.',
            'name.unique' => 'This Role name has already been taken. Please try another one.',
            'permissions.required' => 'Please select at least one permission.',
            'permissions.array' => 'The permissions must be an array.',
            'permissions.min' => 'Please select at least one permission.',
        ]);

        $this->RBACService->createRoleWithAbilities($request->name, $request->permissions);

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
        $role = DatabaseRole::find($id);
        $abilities = $this->RBACService->getAbilitiesByRole($role);

        if (!$role) {
            return response()->json([
                'title' => 'Error',
                'message' => 'Role not found.',
            ], 404);
        }

        $role->rolePermissions = $abilities;

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
            'permissions' => [
                'required',
                'array',
                'exists:permissions,name', // Validate that each permission exists by name
            ],
        ]);

        try {
            $role = Role::findOrFail($roleId);
            $roleName = $request->input('name');
            $role->name = $roleName;
            $role->save();

            // Specify the guard name
            $guardName = 'api'; // Adjust if necessary

            // Debugging - Check input permissions
            $inputPermissions = $request->input('permissions');
            /* $permissions = Permission::whereIn('name', $inputPermissions)
            ->where('guard_name', $guardName)
            ->get(); */

            // Convert permission names to IDs
            $permissionIds = $permissions->pluck('id')->toArray();

            // Sync permissions by IDs
            $role->syncPermissions($permissionIds);

            return response()->json(['message' => 'Role updated successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Role not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
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
