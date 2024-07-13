<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NcRequiredFieldConfig;
use Illuminate\Http\Request;

class NcRecuiredConfigController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
        NcRequiredFieldConfig::create([
            'call_type_id'=>$input['callTypeId']??'',
            'call_category_id'=>$input['callCategoryId']??'',
            'call_sub_category_id'=>$input['callSubCategoryId'],
            'input_field_name'=>$input['inputFiledName'],
            'input_type'=>$input['inputType'],
            'input_value'=>$input['inputValue']??'',
            'input_validation'=>$input['inputValidation'],
            'status'=>$input['statusValue'],
            'created_by'=>'admin',
            'updated_by'=>'admin',
            'last_updated_by'=>''


        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\NcRequiredFieldConfig  $ncRequiredFieldConfig
     * @return \Illuminate\Http\Response
     */
    public function show(NcRequiredFieldConfig $ncRequiredFieldConfig,$id)
    {
        $data = NcRequiredFieldConfig::where('call_sub_category_id',$id)->get();
        if ($data)
        {
            return json_encode($data);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\NcRequiredFieldConfig  $ncRequiredFieldConfig
     * @return \Illuminate\Http\Response
     */
    public function edit(NcRequiredFieldConfig $ncRequiredFieldConfig)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\NcRequiredFieldConfig  $ncRequiredFieldConfig
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, NcRequiredFieldConfig $ncRequiredFieldConfig)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\NcRequiredFieldConfig  $ncRequiredFieldConfig
     * @return \Illuminate\Http\Response
     */
    public function destroy(NcRequiredFieldConfig $ncRequiredFieldConfig)
    {
        //
    }
}
