<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Role;
use Illuminate\Http\Request;

class GroupRoleController extends Controller
{
    public function assignRoles(Request $request, Group $group)
    {
        // dd($request->all(), $group->roles, $group->id);
        $request->validate(['roles' => 'required|array', 'roles.*' => 'string']);
        $roles = Role::whereIn('name', $request->roles)->get();
        $group->roles()->syncWithoutDetaching($roles->pluck('id'));
        return response()->json($group->load('roles'), 200);
    }

    public function removeRoles(Request $request, Group $group, $roleId)
    {
        $role = Role::findOrFail($roleId);
        $group->roles()->detach($role);
        return response()->json($group->load('roles'), 200);
    }
}
