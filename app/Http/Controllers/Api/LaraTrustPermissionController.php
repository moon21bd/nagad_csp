<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\User;

class LaraTrustPermissionController extends Controller
{
    public function assignUserToGroup(User $user, Group $group)
    {
        // Assign the user to the group
        $user->group()->associate($group);
        $user->save();

        // Assign all group roles to the user
        foreach ($group->roles as $role) {
            $user->attachRole($role);
        }

        // Assign all group permissions to the user
        foreach ($group->permissions as $permission) {
            $user->attachPermission($permission);
        }
    }

    // Manually Reassigning Permissions

    public function manuallyReassigningPermissions(User $user, Group $group)
    {
        // $group = Group::find($groupId);

        foreach ($group->users as $user) {
            // Detach existing permissions and roles
            $user->detachPermissions($user->permissions);
            $user->detachRoles($user->roles);

            // Reassign based on group
            foreach ($group->roles as $role) {
                $user->attachRole($role);
            }
            foreach ($group->permissions as $permission) {
                $user->attachPermission($permission);
            }
        }

    }

    // Removing Permissions When a User Leaves a Group

    public function removeUserFromGroup(User $user)
    {
        $group = $user->group;

        if ($group) {
            // Detach group-specific permissions and roles
            foreach ($group->roles as $role) {
                $user->detachRole($role);
            }
            foreach ($group->permissions as $permission) {
                $user->detachPermission($permission);
            }

            // Remove the user from the group
            $user->group()->dissociate();
            $user->save();
        }
    }

    // Dynamic Permission Checking
    public function dynamicPermissionChecking(User $user)
    {
        $group = $user->group;

        if ($group && $group->permissions->contains('view-dashboard')) {
            return true;
        }

        return $user->hasPermission('view-dashboard');
    }

}
