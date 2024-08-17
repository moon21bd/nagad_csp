<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Permission;
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

    public function store(Request $request, $id = null)
    {

        $id = $request->input('id') ?? null;

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

            if ($id) {
                $permission = Permission::findOrFail($id);

                $data = [
                    'name' => $validated['name'],
                    'display_name' => userCaseWord($validated['name']),
                    'description' => userCaseWord($validated['name']),
                ];
                $permission->update($data);

                $msg = 'Permission updated successfully.';
            } else {
                $data = [
                    'name' => $validated['name'],
                    'display_name' => userCaseWord($validated['name']),
                    'description' => userCaseWord($validated['name']),
                ];

                $permission = Permission::create($data);
                $msg = 'Permission created successfully.';
            }

            return response()->json([
                'title' => $msg,
                'message' => $msg,
            ], 200);

        } catch (ValidationException $e) {
            dd('ValidationException', $e->validator->errors()->first());
            return response()->json(['error' => $e->validator->errors()->first()], 422);
        } catch (\Exception $e) {
            dd('Exception', $e->getMessage());
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
        $user = auth()->user(); // Get the currently logged-in user
        $permissions = $user->permissions; // Retrieve all permissions (direct and group-based)

        return response()->json([
            'user' => $user->name,
            'permissions' => $permissions->pluck('name'),
            'roles' => $user->roles->pluck('name'),
        ]);
    }

}
