<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GroupController extends Controller
{
    public function index()
    {
        $groups = Group::all();
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
        $group = Group::with('roles')->findOrFail($id);
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

    /* public function assignRoles(Request $request, Group $group)
    {
    $request->validate(['roles' => 'required|array']);
    $roles = Role::whereIn('name', $request->roles)->get();
    $group->roles()->sync($roles->pluck('id')->toArray());

    return response()->json($group->load('roles'), 200);
    } */

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
                try {
                    $user->attachRoles($role->permissions, $group);
                } catch (\Exception $e) {
                    // Log the exception or handle the error
                    \Log::error('Error attaching roles: ' . $e->getMessage());
                    return response()->json(['message' => 'Error attaching roles'], 500);
                }
            }
        }

        // Return the group with the newly assigned roles
        return response()->json([], 200);
    }

    /* public function assignRoles(Request $request, Group $group)
    {
    // Validate the incoming request
    $request->validate(['roles' => 'required|array']);

    // Get the roles based on the provided names
    $roles = Role::whereIn('name', $request->roles)->get();

    // Sync the roles with the group (this will add/remove roles as needed)
    $group->roles()->sync($roles->pluck('id')->toArray());

    // Get all users associated with the group
    $users = $group->users ?? [];
    // Assign the roles' permissions to each user in the group
    foreach ($users as $user) {
    foreach ($roles as $role) {
    // Attached the permissions of each role to the user
    $user->attachRoles($role->permissions, $group);
    }
    }

    // Return the group with the newly assigned roles
    return response()->json([], 200);
    } */

    public function removeRole(Request $request, $groupId, $roleId)
    {
        $group = Group::findOrFail($groupId);
        $role = Role::findOrFail($roleId);
        $group->roles()->detach($role);
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
}
