<?php

namespace App\Http\Controllers;

use App\Models\NCAccessList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NCAccessListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Retrieve all access lists
        $accessLists = NCAccessList::all();
        return response()->json($accessLists);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'access_name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        // Create a new access list item
        $accessList = NCAccessList::create([
            'access_name' => $request->access_name,
            'status' => $request->status,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
            'last_updated_by' => Auth::id(),
        ]);

        return response()->json($accessList, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\NCAccessList $accessList
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(NCAccessList $accessList)
    {
        return response()->json($accessList);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\AccessList $accessList
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, NCAccessList $accessList)
    {
        // Validate the request
        $request->validate([
            'access_name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        // Update the access list item
        $accessList->update([
            'access_name' => $request->access_name,
            'status' => $request->status,
            'updated_by' => Auth::id(),
            'last_updated_by' => Auth::id(),
        ]);

        return response()->json($accessList);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\NCAccessList $accessList
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(NCAccessList $accessList)
    {
        $accessList->delete();
        return response()->json(null, 204);
    }
}
