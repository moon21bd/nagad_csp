<?php

namespace App\Http\Controllers;

use App\Models\NCCallSubSubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class NCCallSubSubCategoryController extends Controller
{
    public function index()
    {
        $subSubCategories = NCCallSubSubCategory::with('callType', 'callCategory', 'callSubCategory')->get();
        return response()->json($subSubCategories);
    }

    public function show($id)
    {
        $subSubCategory = NCCallSubSubCategory::with('callType', 'callCategory', 'callSubCategory')->findOrFail($id);
        return response()->json($subSubCategory);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'call_type_id' => 'required|exists:nc_call_types,id',
            'call_category_id' => 'required|exists:nc_call_categories,id',
            'call_sub_category_id' => 'required|exists:nc_call_sub_categories,id',
            'call_sub_sub_category_name' => 'required|string|max:128',
            'status' => 'required|in:active,inactive',
        ]);

        $validated['created_by'] = Auth::id();
        $subSubCategory = NCCallSubSubCategory::create($validated);
        return response()->json($subSubCategory, 201);
    }

    public function update(Request $request, $id)
    {
        $subSubCategory = NCCallSubSubCategory::findOrFail($id);

        $validated = $request->validate([
            'call_type_id' => 'required|exists:nc_call_types,id',
            'call_category_id' => 'required|exists:nc_call_categories,id',
            'call_sub_category_id' => 'required|exists:nc_call_sub_categories,id',
            'call_sub_sub_category_name' => 'required|string|max:128',
            'status' => 'required|in:active,inactive',
        ]);

        $validated['updated_by'] = Auth::id();
        $validated['last_updated_by'] = Auth::id();
        $subSubCategory->update($validated);
        return response()->json($subSubCategory);

    }

    public function destroy($id)
    {
        $subSubCategory = NCCallSubSubCategory::findOrFail($id);
        $subSubCategory->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}
