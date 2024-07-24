<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $group->update($validatedData);
        return response()->json($group);
    }

    public function destroy($id)
    {
        Group::destroy($id);
        return response()->json(null, 204);
    }

    public function assignRole(Request $request, Group $group)
    {
        dd($request->all(), $group->id);
        $request->validate(['role' => 'required|string']);
        $role = Role::where('name', $request->role)->firstOrFail();
        $group->roles()->attach($role);
        return response()->json($group->load('roles'), 200);
    }

    public function removeRole(Request $request, Group $group, $roleId)
    {
        $role = Role::findOrFail($roleId);
        $group->roles()->detach($role);
        return response()->json($group->load('roles'), 200);
    }
}
