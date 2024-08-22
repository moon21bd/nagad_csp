<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laratrust\Models\LaratrustPermission;

class GroupController extends Controller
{
    public function index()
    {
        $groups = Group::with('owner')->get();
        return response()->json($groups);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        $validatedData['created_by'] = Auth::id();
        $validatedData['display_name'] = userCaseWord($validatedData['name']);
        $validatedData['description'] = userCaseWord($validatedData['name']);
        $group = Group::create($validatedData);
        return response()->json($group, 201);
    }

    public function show($id)
    {
        $group = Group::with('permissions')->findOrFail($id);
        return response()->json($group);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'status' => 'sometimes|required|in:active,inactive',
        ]);

        $group = Group::findOrFail($id);
        $validatedData['updated_by'] = $validatedData['last_updated_by'] = Auth::id();
        $validatedData['display_name'] = userCaseWord($validatedData['name']);
        $validatedData['description'] = userCaseWord($validatedData['name']);

        $group->update($validatedData);
        return response()->json($group);
    }

    public function destroy($id)
    {
        Group::destroy($id);
        return response()->json(null, 204);
    }

    public function assignRoles(Request $request, Group $group)
    {
        // Validate the incoming request
        $request->validate(['roles' => 'required|array']);

        // Get the roles based on the provided names
        $roles = Role::whereIn('name', $request->roles)->get();

        if ($roles->isEmpty()) {
            return response()->json(['message' => 'Roles not found'], 404);
        }

        // Sync the roles with the group
        $group->roles()->sync($roles->pluck('id')->toArray());
        // Get all users associated with the group
        $users = $group->users;

        if ($users->isEmpty()) {
            return response()->json(['message' => 'No users found in the group'], 404);
        }

        // Assign the roles' permissions to each user in the group
        foreach ($users as $user) {
            foreach ($roles as $role) {
                $user->attachPermissions($role->permissions, $group);
            }
        }

        return response()->json([], 200);
    }

    public function removeRole(Request $request, $groupId, $roleId)
    {
        $group = Group::findOrFail($groupId);
        $role = Role::findOrFail($roleId);
        $group->roles()->detach($role);
        $users = $group->users ?? [];

        foreach ($users as $user) {
            $user->detachPermissions($role->permissions, $group);
        }

        return response()->json('Delete successful.', 204);
    }

    public function getGroupWiseRoles($groupId)
    {
        return $this->getRolesAndPermissionsByGroup($groupId);
    }

    /**
     * Get all roles and permissions under a specific group_id.
     *
     * @param int $groupId
     * @return array
     */
    public function getRolesAndPermissionsByGroup($groupId)
    {
        // Get all role IDs associated with the group_id via the role_group table
        $roleIds = DB::table('role_group')->where('group_id', $groupId)->pluck('role_id');

        // Retrieve roles based on these role IDs
        $roles = Role::whereIn('id', $roleIds)->get();

        // Retrieve permissions based on these role IDs
        $permissions = Permission::whereHas('roles', function ($query) use ($roleIds) {
            $query->whereIn('roles.id', $roleIds);
        })->get();

        return [
            'roles' => $roles->toArray(),
            'permissions' => $permissions->toArray(),
        ];
    }

    public function getActiveGroups()
    {
        $groups = Group::where('status', 'Active')
            ->get();
        return response()->json($groups);
    }

    public function assignPermission(Request $request, $id)
    {
        $group = Group::find($id);

        // Get the IDs of the permissions being requested
        $permissionIds = LaratrustPermission::whereIn('name', $request->permissions)->pluck('id')->toArray();

        // Get the IDs of the existing permissions for the group
        $existingPermissionIds = $group->permissions->pluck('id')->toArray();

        // Determine which permissions to attach (new ones)
        $newPermissions = array_diff($permissionIds, $existingPermissionIds);

        // Determine which permissions to detach (removed ones)
        $permissionsToDetach = array_diff($existingPermissionIds, $permissionIds);

        // Attach new permissions
        if (!empty($newPermissions)) {
            $group->permissions()->attach($newPermissions);
        }

        // Detach permissions that were removed
        if (!empty($permissionsToDetach)) {
            $group->permissions()->detach($permissionsToDetach);
        }

        return response()->json([
            'message' => 'Permissions updated successfully',
            'type' => 'success',
        ]);
    }

    /* public function assignPermission(Request $request, Group $team, $id)
{

$group = Group::find($id);

$permissionIds = LaratrustPermission::whereIn('name', $request->permissions)->pluck('id')->toArray();

$existingPermissionIds = $group->permissions->pluck('id')->toArray();
$newPermissions = array_diff($permissionIds, $existingPermissionIds);
dd('request->permissions', $request->permissions, 'newPermissions', $newPermissions, 'permissionIds', $permissionIds, 'existingPermissionIds', $existingPermissionIds);
if (!empty($newPermissions)) {
$group->permissions()->attach($newPermissions);
}

return response()->json([
'message' => 'Permission assigned',
'type' => 'success',
]);
} */
}
