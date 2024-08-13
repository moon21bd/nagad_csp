<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\Rule;
use Laratrust\Helper;

class RolesController extends Controller
{

    protected $rolesModel;
    protected $permissionModel;

    public function __construct()
    {
        $this->rolesModel = Config::get('laratrust.models.role');
        $this->permissionModel = Config::get('laratrust.models.permission');
    }

    /**
     * Roles List with pagination.
     *
     * @param array $request
     * @return json array response
     */
    public function roles(Request $request)
    {
        $roles = $this->rolesModel::withCount('permissions')->get();
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
        $validated = $this->validate($request, [
            'name' => 'required|string|max:50|unique:roles,name',
            'permissions' => 'required|array|min:1',
            'display_name' => 'nullable|string',
            'description' => 'nullable|string',
        ], [
            'name.required' => 'The role name is required.',
            'name.string' => 'The role name must be a string.',
            'name.max' => 'The role name must not exceed 50 characters.',
            'name.unique' => 'This Role name has already been taken. Please try another one.',
            'permissions.required' => 'Please select at least one permission.',
            'permissions.array' => 'The permissions must be an array.',
            'permissions.min' => 'Please select at least one permission.',
        ]);

        $data = [
            'name' => $validated['name'],
            'display_name' => userCaseWord($validated['name']),
            'description' => userCaseWord($validated['name']),
        ];
        $role = $this->rolesModel::create($data);
        $role->syncPermissions($validated['permissions'] ?? []);

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
        $role = Role::findOrFail($id);

        if (!$role) {
            return response()->json([
                'title' => 'Error',
                'message' => 'Role not found.',
            ], 404);
        }

        $role->rolePermissions = $role->permissions;

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
                'exists:permissions,name',
            ],
            'display_name' => 'nullable|string',
            'description' => 'nullable|string',
        ]);
        try {

            $role = $this->rolesModel::findOrFail($roleId);

            if (!Helper::roleIsEditable($role)) {
                return response()->json(['error' => 'The role is not editable'], 404);
            }
            $data = [
                'name' => $request->name,
                'display_name' => userCaseWord($request->name),
                'description' => userCaseWord($request->name),
            ];
            $role->update($data);
            $role->syncPermissions($request->permissions ?? []);

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
        $usersAssignedToRole = \DB::table(Config::get('laratrust.tables.role_user'))
            ->where(Config::get('laratrust.foreign_keys.role'), $id)
            ->count();
        $role = $this->rolesModel::findOrFail($id);

        $msg = "";
        if (!Helper::roleIsDeletable($role)) {
            return response()->json(['message' => 'The role is not deletable']);
        }

        if ($usersAssignedToRole > 0) {
            return response()->json(['message' => 'Role is attached to one or more users. It can not be deleted']);

        } else {
            $this->rolesModel::destroy($id);
            return response()->json(['message' => 'Role deleted successfully']);
        }
        return response()->json(['message' => 'Unhandled stage at role delete.']);
    }
}
