<?php

namespace App\Http\Controllers;

use App\Models\NCGroup;
use Illuminate\Http\Request;

class NCGroupController extends Controller
{
    public function index()
    {
        $groups = NCGroup::all();
        return response()->json($groups);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
            'role_id' => 'required',
            'created_by' => 'required|integer',
        ]);

        $group = NCGroup::create($request->all());
        return response()->json($group, 201);
    }

    public function show($id)
    {
        $group = NCGroup::findOrFail($id);
        return response()->json($group);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'status' => 'sometimes|required|in:active,inactive',
            'role_id' => 'sometimes|required',
            // 'updated_by' => 'required|integer',
            // 'last_updated_by' => 'required|integer',
        ]);

        $group = NCGroup::findOrFail($id);
        $group->update($request->all());
        return response()->json($group);
    }

    public function destroy($id)
    {
        NCGroup::destroy($id);
        return response()->json(null, 204);
    }
}
