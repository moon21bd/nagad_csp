<?php

namespace App\Http\Controllers;

use App\Models\NCServiceTypeConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NCServiceTypeConfigController extends Controller
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
        dd($request->all());
        $validated = $request->validate([
            'callTypeId' => 'required|exists:nc_call_types,id',
            'callCategoryId' => 'required|exists:nc_call_categories,id',
            'callSubCategoryId' => 'required|exists:nc_call_sub_categories,id',
            'callerMobileNo' => 'required', // this will be generate dynamically from frontend after found the solution for params value get
            'requiredField' => 'nullable|array',
            'comments' => 'nullable|string',
            'attachment' => 'nullable|string',
        ]);

        $requiredFields = [];
        foreach ($validated['requiredField'] ?? [] as $item => $value) {
            list($id, $field) = explode('|', $item);
            $requiredFields[] = [
                'id' => (int) $id,
                'field' => $field,
                'value' => $value,
            ];
        }

        $configArr = [
            'uuid' => uniqid(), // after internet restortion ramsey/uuid will be applied
            'call_type_id' => $validated['callTypeId'],
            'call_category_id' => $validated['callCategoryId'],
            'call_sub_category_id' => $validated['callSubCategoryId'],
            'caller_mobile_no' => $validated['callerMobileNo'], // $validated['caller_mobile_no'],
            'required_fields' => json_encode($requiredFields), //$validated['requiredField'],
            'group_id' => 1, // this will assign from responsible team mapping tables later
            'assign_to_group_id' => 1, // this will also assign from responsible team mapping tables later
            'assign_by_id' => 1, // this will also assign from responsible team mapping tables later
            'is_ticket_reassign' => 0, // if assigned then this will populate
            'comments' => $validated['comments'], // $validated['comments'],
            'sla_status' => 'NORMAL',
            'attachment' => null,
            'ticket_notification_status' => 1,
            'is_customer_notified' => 0,
            'ticket_status' => (empty($requiredFields)) ? 'CLOSED' : 'PENDING', // this will populate from various team
            'ticket_channel' => 'PANEL',
            'ticket_created_by' => Auth::id(),
            'ticket_updated_by' => Auth::id(),
            'ticket_last_updated_by' => null,
            'ticket_created_at' => Carbon::now(),
            'ticket_updated_at' => Carbon::now(),
        ];

        $ticket = NCServiceTypeConfig::create($configArr);
        return response()->json($ticket, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\NCServiceTypeConfig  $nCServiceTypeConfig
     * @return \Illuminate\Http\Response
     */
    public function show(NCServiceTypeConfig $nCServiceTypeConfig)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\NCServiceTypeConfig  $nCServiceTypeConfig
     * @return \Illuminate\Http\Response
     */
    public function edit(NCServiceTypeConfig $nCServiceTypeConfig)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\NCServiceTypeConfig  $nCServiceTypeConfig
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, NCServiceTypeConfig $nCServiceTypeConfig)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\NCServiceTypeConfig  $nCServiceTypeConfig
     * @return \Illuminate\Http\Response
     */
    public function destroy(NCServiceTypeConfig $nCServiceTypeConfig)
    {
        //
    }
}
