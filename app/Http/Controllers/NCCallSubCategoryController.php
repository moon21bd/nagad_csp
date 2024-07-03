<?php

namespace App\Http\Controllers;

use App\Models\NCCallSubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class NCCallSubCategoryController extends Controller
{
    public function index()
    {
        $subCategories = NCCallSubCategory::with('callType', 'callCategory')->get();
        return response()->json($subCategories);
    }

    public function show($id)
    {
        $subCategory = NCCallSubCategory::with('callType', 'callCategory')->findOrFail($id);
        return response()->json($subCategory);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'call_type_id' => 'required|exists:nc_call_types,id',
            'call_category_id' => 'required|exists:nc_call_categories,id',
            'call_sub_category_name' => 'required|string|max:128',
            'status' => 'required|in:active,inactive',
        ]);

        $validated['created_by'] = Auth::id();
        $subCategory = NCCallSubCategory::create($validated);
        return response()->json($subCategory, 201);
    }

    public function update(Request $request, $id)
    {
        $subCategory = NCCallSubCategory::findOrFail($id);

        $validated = $request->validate([
            'call_type_id' => 'required|exists:n_c_call_types,id',
            'call_category_id' => 'required|exists:nc_call_categories,id',
            'call_sub_category_name' => 'required|string|max:128',
            'status' => 'required|in:active,inactive',
        ]);

        $validated['updated_by'] = Auth::id();
        $subCategory->update($validated);
        return response()->json($subCategory);

    }

    public function destroy($id)
    {
        $subCategory = NCCallSubCategory::findOrFail($id);
        $subCategory->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}
