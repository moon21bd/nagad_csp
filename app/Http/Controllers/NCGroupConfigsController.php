<?php

namespace App\Http\Controllers;

use App\Models\NCGroupConfigs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NCGroupConfigsController extends Controller
{
    public function index()
    {
        return NCGroupConfigs::with('accessLists',
            'group')->get();
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'group_id' => 'required|integer',
            'status' => 'required|in:active,inactive',
            'access_list_ids' => 'required|array',
            'access_list_ids.*' => 'exists:nc_access_lists,id',
        ]);

        $groupConfig = NCGroupConfigs::create([
            'group_id' => $validatedData['group_id'],
            'status' => $validatedData['status'],
            'created_by' => Auth::id(),
        ]);

        // Attach access lists to group configuration
        $groupConfig->accessLists()->attach($validatedData['access_list_ids']);

        return response()->json($groupConfig, 201);
    }

    public function show(NCGroupConfigs $groupConfig)
    {
        $groupConfig->load('accessLists');
        return response()->json($groupConfig);
    }

    public function update(Request $request, NCGroupConfigs $groupConfig)
    {
        $validatedData = $request->validate([
            'group_id' => 'required|integer',
            'status' => 'required|in:active,inactive',
            'access_list_ids' => 'required|array',
            'access_list_ids.*' => 'exists:nc_access_lists,id',
        ]);

        $groupConfig->update([
            'group_id' => $validatedData['group_id'],
            'status' => $validatedData['status'],
            'updated_by' => Auth::id(),
            'last_updated_by' => Auth::id(),
        ]);

        // Sync access lists to group configuration
        $groupConfig->accessLists()->sync($validatedData['access_list_ids']);

        return response()->json($groupConfig, 200);
    }

    public function destroy($id)
    {
        // Find the group config by ID
        $groupConfig = NCGroupConfigs::findOrFail($id);

        // Manually detach all related access lists
        $groupConfig->accessLists()->detach();

        // Delete the group config
        $groupConfig->delete();

        return response()->json(null, 204);
    }
}
