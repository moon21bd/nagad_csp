<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PermissionsController extends Controller
{

    public function __construct()
    {
        //
    }

    /**
     * permissions List with pagination.
     *
     * @param array $request
     * @return json array response
     */
    public function permissions(Request $request)
    {
        $permissions = Permission::all();

        return response()->json([
            'title' => 'Success.',
            'message' => 'Permissions List.',
            'data' => $permissions,
        ], 200);

    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                Rule::unique('permissions', 'name'),
            ],
            'display_name' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        try {

            $data = [
                'name' => $validated['name'],
                'display_name' => userCaseWord($validated['name']),
                'description' => userCaseWord($validated['name']),
            ];

            $permission = Permission::create($data);
            $msg = 'Permission created successfully.';

            return response()->json([
                'title' => $msg,
                'message' => $msg,
            ], 200);

        } catch (ValidationException $e) {
            // dd('ValidationException', $e->validator->errors()->first());
            return response()->json(['error' => $e->validator->errors()->first()], 422);
        } catch (\Exception $e) {
            // dd('Exception', $e->getMessage());
            return response()->json(['error' => 'Failed to process request.', 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        // dd($request->all(), $id);
        $id = $request->input('id');

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                Rule::unique('permissions')->ignore($id),
            ],
            'display_name' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        try {

            $permission = Permission::findOrFail($id);

            $data = [
                'name' => $validated['name'],
                'display_name' => userCaseWord($validated['name']),
                'description' => userCaseWord($validated['name']),
            ];
            $permission->update($data);

            $msg = 'Permission updated successfully.';

            return response()->json([
                'title' => $msg,
                'message' => $msg,
            ], 200);

        } catch (ValidationException $e) {
            return response()->json(['error' => $e->validator->errors()->first()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to process request.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Permissions details by ID
     *
     * @param Int $id
     * @return json array response
     */
    public function getPermissionById($id)
    {
        $permission = Permission::findOrFail($id);

        return response()->json([
            'title' => 'Success.',
            'message' => 'Permission Details.',
            'data' => $permission,
        ], 200);
    }

    /**
     * Delete Permission
     *
     * @param Int $id
     * @return json array response
     */
    public function destroy($id)
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();

        return response()->json([
            'title' => 'Success.',
            'message' => 'Permission Deleted.',
            'data' => '',
        ], 200);
    }

    public function getCurrentUserPermissions()
    {

        $user = auth()->user();
        $user->load('permissions');

        // Get user-specific permissions
        $userPermissions = $user->permissions->pluck('name')->toArray();

        // Get role-based permissions
        $rolePermissions = $user->roles->flatMap(function ($role) {
            return $role->permissions->pluck('name');
        })->toArray();

        return response()->json([
            'user' => $user->name,
            'roles' => $user->roles->pluck('name'),
            'userPermissions' => $userPermissions,
            'rolePermissions' => $rolePermissions,
        ]);

    }

    public function getUserPermissionsById($id)
    {
        $user = User::find($id);
        $permissions = $user->permissions;

        return response()->json([
            'user' => $user->name,
            'permissions' => $permissions->pluck('name'),
            'roles' => $user->roles->pluck('name'),
        ]);
    }

    /*public function checkPermission(Request $request)
    {
    $user = auth()->user();
    $permission = $request->input('permission');

    if ($user->hasPermission($permission) || $user->hasRolePermission($permission)) {
    return response()->json(['allowed' => true]);
    }

    return response()->json(['allowed' => false], 403);
    }*/

    public function checkPermission(Request $request)
    {
        $user = auth()->user();
        $permission = $request->input('permission');

        // Eager load permissions and roles to avoid multiple queries
        $user->load('permissions', 'roles.permissions');

        // Check both user-specific and role-based permissions using hasPermission()
        if ($user->hasPermission($permission)) {
            return response()->json(['allowed' => true]);
        }

        return response()->json(['allowed' => false], 403);
    }

}
