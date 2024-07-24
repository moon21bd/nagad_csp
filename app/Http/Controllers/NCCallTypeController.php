<?php

namespace App\Http\Controllers;

use App\Models\NCCallType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NCCallTypeController extends Controller
{
    /*function __construct()
    {
    $this->middleware('permission:call-types-list|call-types-create|call-types-edit|call-types-delete', ['only' => ['index', 'store']]);
    $this->middleware('permission:call-types-create', ['only' => ['create', 'store']]);
    $this->middleware('permission:call-types-edit', ['only' => ['edit', 'store']]);
    $this->middleware('permission:call-types-delete', ['only' => ['destroy']]);
    }*/

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
        ]);
        $validatedData['created_by'] = Auth::id();
        $ncCallType = NCCallType::create($validatedData);
        return response()->json($ncCallType, 201);
    }

    public function show($id)
    {
        $callTypes = NCCallType::findOrFail($id);
        return response()->json($callTypes);
    }

    public function update(Request $request, $id)
    {
        $ncCallType = NCCallType::findOrFail($id);

        $validatedData = $request->validate([
            'call_type_name' => 'required|string|max:128',
            'status' => 'required|in:active,inactive',
        ]);
        $validatedData['updated_by'] = $validatedData['last_updated_by'] = Auth::id();
        $ncCallType->update($validatedData);
        return response()->json($ncCallType);
    }

    public function destroy($id)
    {

        NCCallType::destroy($id);
        /* $callCategory = NCCallCategory::where('call_type_id', $id);
        $callCategory->delete();
        $callSubCategory = NCCallSubCategory::where('call_type_id', $id);
        $callSubCategory->delete(); */
        return response()->json(null, 204);
    }

    public function getActiveCallType()
    {
        $ncCallType = NCCallType::where('status', 'active')
        // ->orderByDesc('id')
            ->get();
        return response()->json($ncCallType);
    }
}
