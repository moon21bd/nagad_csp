<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\RBACService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Silber\Bouncer\Database\Ability;

class PermissionsController extends Controller
{

    protected $RBACService;

    public function __construct(RBACService $RBACService)
    {
        $this->RBACService = $RBACService;
    }

    /**
     * permissions List with pagination.
     *
     * @param array $request
     * @return json array response
     */
    public function permissions(Request $request)
    {
        $permissions = $this->RBACService->getAllAbility();
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
                // 'name' => ($id ? 'required|unique:abilities,name,' . $id : 'required|unique:abilities,name'),
                'name' => 'required|string',
            ]);
            $ability = $request->input('name');

            if ($id) {
                $this->RBACService->updateAbilityById($id, $ability);
                $msg = 'Permission updated successfully.';
            } else {
                $this->RBACService->createAbility($ability);
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
        $permission = Ability::find($id);

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
        $this->RBACService->deleteAbilityById($id);
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
