<?php

namespace App\Http\Controllers;

use App\Http\Resources\NCCallTypeResource;
use App\Models\NCCallType;
use Illuminate\Http\Request;

class NCCallTypeController extends Controller
{
    public function index()
    {
        $ncCallType = NCCallType::all();
        return response()->json($ncCallType);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'call_type_name' => 'required|string|max:128',
            'status' => 'required|in:active,inactive',
            'created_by' => 'nullable|string|max:128',
            'updated_by' => 'nullable|string|max:128',
            'last_updated_by' => 'nullable|string|max:128'
        ]);

        $ncCallType = NCCallType::create($validatedData);
        return response()->json($ncCallType, 201);
    }

    public function show($id)
    {
        $group = NCCallType::findOrFail($id);
        return response()->json($group);
    }

    public function update(Request $request, $id)
    {
        $ncCallType = NCCallType::findOrFail($id);

        $validatedData = $request->validate([
            'call_type_name' => 'required|string|max:128',
            'status' => 'required|in:active,inactive',
            'created_by' => 'nullable|string|max:128',
            'updated_by' => 'nullable|string|max:128',
            'last_updated_by' => 'nullable|string|max:128'
        ]);

        $ncCallType->update($validatedData);
        return response()->json($ncCallType);
    }

    public function destroy($id)
    {
        NCCallType::destroy($id);
        return response()->json(null, 204);
    }
}
