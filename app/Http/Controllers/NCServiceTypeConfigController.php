<?php

namespace App\Http\Controllers;

use App\Models\NCServiceTypeConfig;
use Illuminate\Database\QueryException;
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
        $data = NCServiceTypeConfig::with('callType', 'callCategory', 'callSubCategory')
            ->orderByDesc('id')
            ->get();
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'callTypeId' => 'required|exists:nc_call_types,id',
            'callCategoryId' => 'required|exists:nc_call_categories,id',
            'callSubCategoryId' => 'required|exists:nc_call_sub_categories,id',
            'selectedGroups' => 'required',
            'sms_config_id' => 'nullable|integer',
            'email_config_id' => 'nullable|integer',
            'selectedNotificationChannels' => 'nullable|array',
            'is_show_popup_msg' => 'nullable|string',
            'popupMessages' => 'nullable|array',
            'is_escalation' => 'nullable|string',
            'is_show_attachment' => 'nullable|string',
            'is_verification_check' => 'nullable|string',
            'customer_behavior_check' => 'nullable|string',
            'bulk_ticket_close_perms' => 'nullable|string',
            'requiredFieldIds' => 'nullable|string',
        ]);

        $configArr = [
            'call_type_id' => $validated['callTypeId'],
            'call_category_id' => $validated['callCategoryId'],
            'call_sub_category_id' => $validated['callSubCategoryId'],
            'responsible_groups_with_tats' => $validated['selectedGroups'],
            'required_fields_ids' => $validated['requiredFieldIds'],
            'is_escalation' => $validated['is_escalation'],
            'is_show_attachment' => $validated['is_show_attachment'],
            'is_show_popup_msg' => $validated['is_show_popup_msg'],
            'popup_msg_texts' => json_encode($validated['popupMessages']),
            'notification_channels' => $validated['selectedNotificationChannels'],
            'sms_config_id' => $validated['sms_config_id'],
            'email_config_id' => $validated['email_config_id'],
            'is_verification_check' => $validated['is_verification_check'],
            'customer_behavior_check' => $validated['customer_behavior_check'],
            'bulk_ticket_close_perms' => $validated['bulk_ticket_close_perms'],
            'status' => 'active',
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
            'last_updated_by' => Auth::id(),
        ];

        try {
            $serviceType = NCServiceTypeConfig::updateOrCreate(
                [
                    'call_type_id' => $validated['callTypeId'],
                    'call_category_id' => $validated['callCategoryId'],
                    'call_sub_category_id' => $validated['callSubCategoryId'],
                ],
                $configArr
            );
            return response()->json($serviceType, 201);
        } catch (QueryException $e) {
            if ($e->getCode() == 23000) { // SQLSTATE[23000] for integrity constraint violation
                return response()->json([
                    'message' => 'Duplicate entry detected for the given combination of service type, service category, and service sub-category.',
                ], 400);
            }
            // Handle other types of query exceptions
            return response()->json([
                'message' => 'An error occurred while processing your request.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\NCServiceTypeConfig  $NCServiceTypeConfig
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $seviceTypeConfig = NCServiceTypeConfig::with('callType', 'callCategory', 'callSubCategory')->findOrFail($id);
        return response()->json($seviceTypeConfig);

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

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'callTypeId' => 'required|exists:nc_call_types,id',
            'callCategoryId' => 'required|exists:nc_call_categories,id',
            'callSubCategoryId' => 'required|exists:nc_call_sub_categories,id',
            'selectedGroups' => 'required|array',
            'sms_config_id' => 'nullable|integer',
            'email_config_id' => 'nullable|integer',
            'selectedNotificationChannels' => 'nullable|array',
            'is_show_popup_msg' => 'nullable|string',
            'popupMessages' => 'nullable|array',
            'is_escalation' => 'nullable|string',
            'is_show_attachment' => 'nullable|string',
            'is_verification_check' => 'nullable|string',
            'customer_behavior_check' => 'nullable|string',
            'bulk_ticket_close_perms' => 'nullable|string',
            'requiredFieldIds' => 'nullable|string',
        ]);

        $configArr = [
            'call_type_id' => $validated['callTypeId'],
            'call_category_id' => $validated['callCategoryId'],
            'call_sub_category_id' => $validated['callSubCategoryId'],
            'responsible_groups_with_tats' => $validated['selectedGroups'],
            'required_fields_ids' => $validated['requiredFieldIds'],
            'is_escalation' => $validated['is_escalation'],
            'is_show_attachment' => $validated['is_show_attachment'],
            'is_show_popup_msg' => $validated['is_show_popup_msg'],
            'popup_msg_texts' => json_encode($validated['popupMessages']),
            'notification_channels' => $validated['selectedNotificationChannels'],
            'sms_config_id' => $validated['sms_config_id'],
            'email_config_id' => $validated['email_config_id'],
            'is_verification_check' => $validated['is_verification_check'],
            'customer_behavior_check' => $validated['customer_behavior_check'],
            'bulk_ticket_close_perms' => $validated['bulk_ticket_close_perms'],
            'status' => 'active',
            'updated_by' => Auth::id(),
            'last_updated_by' => Auth::id(),
        ];

        try {
            // Update the existing NCServiceTypeConfig record
            $serviceConfig = NCServiceTypeConfig::findOrFail($id);
            $serviceConfig->update($configArr);

            return response()->json($serviceConfig, 200);
        } catch (QueryException $e) {
            if ($e->getCode() == 23000) { // SQLSTATE[23000] for integrity constraint violation
                return response()->json([
                    'message' => 'Duplicate entry detected for the given combination of call type, call category, and call sub-category.',
                ], 400);
            }
            // Handle other types of query exceptions
            return response()->json([
                'message' => 'An error occurred while processing your request.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\NCServiceTypeConfig  $nCServiceTypeConfig
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $fieldConfig = NCServiceTypeConfig::findOrFail($id);
        $fieldConfig->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}
