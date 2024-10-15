<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\NCRequiredFieldConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NCRequiredConfigController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = NCRequiredFieldConfig::with('callType', 'callCategory', 'callSubCategory')->get();
        return response()->json($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'formFields.*.callTypeId' => 'required|exists:nc_call_types,id',
            'formFields.*.callCategoryId' => 'required|exists:nc_call_categories,id',
            'formFields.*.callSubCategoryId' => 'required|exists:nc_call_sub_categories,id',
            'formFields.*.inputFiledName' => 'required|string|max:128',
            'formFields.*.inputType' => 'required|string|max:128',
            'formFields.*.inputValue' => 'nullable|string',
            'formFields.*.inputValidation' => 'required|string',
            'formFields.*.statusValue' => 'required|in:active,inactive',
        ]);

        // Loop through each form field set and create records
        foreach ($validatedData['formFields'] as $field) {
            NCRequiredFieldConfig::create([
                'call_type_id' => $field['callTypeId'],
                'call_category_id' => $field['callCategoryId'],
                'call_sub_category_id' => $field['callSubCategoryId'],
                'input_field_name' => $field['inputFiledName'],
                'input_type' => $field['inputType'],
                'input_value' => $field['inputValue'] ?? '',
                'input_validation' => $field['inputValidation'],
                'status' => $field['statusValue'],
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
                'last_updated_by' => Auth::id(),
            ]);
        }

        return response()->json(['message' => 'Form fields created successfully!'], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\NCRequiredFieldConfig $ncRequiredFieldConfig
     * @return \Illuminate\Http\Response
     */
    public function show(NCRequiredFieldConfig $ncRequiredFieldConfig, $id)
    {
        $data = NCRequiredFieldConfig::findOrFail($id);
        if ($data) {
            return json_encode($data);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\NCRequiredFieldConfig $ncRequiredFieldConfig
     * @return \Illuminate\Http\Response
     */
    public function edit(NCRequiredFieldConfig $ncRequiredFieldConfig)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\NCRequiredFieldConfig $ncRequiredFieldConfig
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'call_type_id' => 'required|exists:nc_call_types,id',
            'call_category_id' => 'required|exists:nc_call_categories,id',
            'call_sub_category_id' => 'required|exists:nc_call_sub_categories,id',
            'input_field_name' => 'required|string|max:128',
            'input_type' => 'required|string|max:128',
            'input_value' => 'nullable',
            'input_validation' => 'required|string',
            'status' => 'required|in:active,inactive',
        ]);
        $validatedData['updated_by'] = $validatedData['last_updated_by'] = Auth::id();

        $requiredConfig = NCRequiredFieldConfig::findOrFail($id);
        $requiredConfig->update($validatedData);
        return response()->json($requiredConfig);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\NCRequiredFieldConfig $ncRequiredFieldConfig
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $fieldConfig = NCRequiredFieldConfig::findOrFail($id);
        $fieldConfig->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }

    public function getRequiredFieldConfigsData($callTypeId, $callCategoryId, $callSubCategoryId)
    {
        $requiredFieldConfigs = NCRequiredFieldConfig::where(['call_type_id' => $callTypeId, 'call_category_id' => $callCategoryId, 'call_sub_category_id' => $callSubCategoryId])
            ->get();

        return response()->json($requiredFieldConfigs);

    }

    public function getRequiredFieldConfigBySubCatId($id)
    {
        $requiredFieldConfigs = NCRequiredFieldConfig::where('call_sub_category_id', $id)
            ->get();

        return response()->json($requiredFieldConfigs);
    }
}
