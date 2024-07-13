<?php

namespace App\Http\Controllers;

use App\Models\NCGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NCGroupController extends Controller
{
    public function index()
    {
        $groups = NCGroup::all();
        return response()->json($groups);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        $validatedData['created_by'] = Auth::id();
        $group = NCGroup::create($validatedData);
        return response()->json($group, 201);
    }

    public function show($id)
    {
        $group = NCGroup::findOrFail($id);
        return response()->json($group);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'status' => 'sometimes|required|in:active,inactive',
        ]);

        $group = NCGroup::findOrFail($id);
        $validatedData['updated_by'] = $validatedData['last_updated_by'] = Auth::id();
        $group->update($validatedData);
        return response()->json($group);
    }

    public function destroy($id)
    {
        NCGroup::destroy($id);
        return response()->json(null, 204);
    }
}
