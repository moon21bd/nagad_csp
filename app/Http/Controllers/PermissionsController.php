<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsController extends Controller
{
    /* public function __construct()
    {
    $this->middleware('permission:permission-list|permission-create|permission-edit|permission-delete', ['only' => ['index', 'store']]);
    $this->middleware('permission:permission-create', ['only' => ['create', 'store']]);
    $this->middleware('permission:permission-edit', ['only' => ['edit', 'store']]);
    $this->middleware('permission:permission-delete', ['only' => ['destroy']]);
    } */

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
        try {
            $id = $request->input('id');
            $this->validate($request, [
                'name' => ($id ? 'required|unique:permissions,name,' . $id : 'required|unique:permissions,name'),
            ]);

            if ($id) {
                $permission = Permission::findOrFail($id);
                $permission->name = $request->input('name');
                $permission->save();
                $msg = 'Permission updated successfully.';
            } else {
                $permission = Permission::create([
                    'name' => $request->input('name'),
                    'guard_name' => 'api',
                ]);
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
        $permission = Permission::find($id);

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
        DB::table("permissions")->where('id', $id)->delete();

        return response()->json([
            'title' => 'Success.',
            'message' => 'Permission Deleted.',
            'data' => '',
        ], 200);
    }

    /**
     * Assign permissions to the super admin role.
     *
     * @param \App\Models\Permission $permission
     * @return void
     */
    private function assignPermissionsToSuperAdmin(Permission $permission)
    {
        // Get or create the super admin role
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);

        // Assign the newly created permission to the super admin role
        $superAdminRole->givePermissionTo($permission);
    }
}
