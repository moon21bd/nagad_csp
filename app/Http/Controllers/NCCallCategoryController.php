<?php

namespace App\Http\Controllers;

use App\Models\NCCallCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NCCallCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // $categories = NCCallCategory::all();
        $categories = NCCallCategory::with(['callType', 'creator', 'updater', 'lastUpdater'])
            ->orderByDesc('id')
            ->get();
        return response()->json($categories);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'call_type_id' => 'required|integer',
            'call_category_name' => 'required|string|max:128',
            'status' => 'required|in:active,inactive',
        ]);
        $validatedData['created_by'] = Auth::id();
        $callCategory = NCCallCategory::create($validatedData);
        return response()->json($callCategory, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $category = NCCallCategory::with(['callType', 'creator', 'updater', 'lastUpdater'])->findOrFail($id);
            return response()->json($category);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Category not found'], 404);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getActiveCategoryByCallTypeId($id)
    {
        $category = NCCallCategory::where(['call_type_id' => $id, 'status' => 'active'])
            ->orderByDesc('created_at')
            ->get();
        return response()->json($category);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'call_type_id' => 'required|integer',
            'call_category_name' => 'required|string|max:128',
            'status' => 'required|in:active,inactive',
        ]);
        $validatedData['updated_by'] = $validatedData['last_updated_by'] = Auth::id();

        $category = NCCallCategory::findOrFail($id);
        $category->update($validatedData);
        return response()->json($category);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $category = NCCallCategory::findOrFail($id);
        $category->delete();
        return response()->json(null, 204);
    }

    public function getActiveServiceCategory()
    {
        $categories = NCCallCategory::with(['callType', 'creator', 'updater', 'lastUpdater'])
            ->where('status', 'active')
            ->orderByDesc('created_at')
            ->get();
        return response()->json($categories);
    }

}
