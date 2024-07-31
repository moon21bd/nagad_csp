<?php

namespace App\Http\Controllers;

use App\Models\NCServiceResponsibleGroup;
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
        $data = NCServiceTypeConfig::with('callType', 'callCategory', 'callSubCategory', 'responsibleGroup')
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
        // No implementation needed
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $this->validateRequest($request);

        $configArr = $this->prepareConfigArray($validated);

        try {
            $serviceType = NCServiceTypeConfig::updateOrCreate(
                [
                    'call_type_id' => $validated['callTypeId'],
                    'call_category_id' => $validated['callCategoryId'],
                    'call_sub_category_id' => $validated['callSubCategoryId'],
                ],
                $configArr
            );

            $this->insertOrUpdateResponsibleGroups(
                $validated['selectedGroups'],
                $validated['callTypeId'],
                $validated['callCategoryId'],
                $validated['callSubCategoryId'],
                $serviceType->id
            );

            return response()->json($serviceType, 201);
        } catch (QueryException $e) {
            return $this->handleQueryException($e);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $serviceTypeConfig = NCServiceTypeConfig::with('callType', 'callCategory', 'callSubCategory', 'responsibleGroup')->findOrFail($id);
        return response()->json($serviceTypeConfig);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\NCServiceTypeConfig  $nCServiceTypeConfig
     * @return \Illuminate\Http\Response
     */
    public function edit(NCServiceTypeConfig $nCServiceTypeConfig)
    {
        // No implementation needed
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validated = $this->validateRequest($request);

        $configArr = $this->prepareConfigArray($validated, false);

        try {
            $serviceConfig = NCServiceTypeConfig::findOrFail($id);
            $serviceConfig->update($configArr);

            $this->insertOrUpdateResponsibleGroups(
                $validated['selectedGroups'],
                $validated['callTypeId'],
                $validated['callCategoryId'],
                $validated['callSubCategoryId'],
                $id
            );

            return response()->json($serviceConfig, 200);
        } catch (QueryException $e) {
            return $this->handleQueryException($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $fieldConfig = NCServiceTypeConfig::findOrFail($id);
        $fieldConfig->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }

    /**
     * Validate the request data.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    protected function validateRequest(Request $request)
    {
        return $request->validate([
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
            'is_user_notified' => 'nullable|string',
            'is_group_lead_notified' => 'nullable|string',
            'requiredFieldIds' => 'nullable|string',
        ]);
    }

    /**
     * Prepare the configuration array for storing or updating.
     *
     * @param array $validated
     * @param bool $isCreate
     * @return array
     */
    protected function prepareConfigArray(array $validated, $isCreate = true)
    {
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
            'is_user_notified' => $validated['is_user_notified'],
            'is_group_lead_notified' => $validated['is_group_lead_notified'],
            'sms_config_id' => $validated['sms_config_id'],
            'email_config_id' => $validated['email_config_id'],
            'is_verification_check' => $validated['is_verification_check'],
            'customer_behavior_check' => $validated['customer_behavior_check'],
            'bulk_ticket_close_perms' => $validated['bulk_ticket_close_perms'],
            'status' => 'active',
            'updated_by' => Auth::id(),
            'last_updated_by' => Auth::id(),
        ];

        if ($isCreate) {
            $configArr['created_by'] = Auth::id();
        }

        return $configArr;
    }

    /**
     * Handle query exceptions.
     *
     * @param \Illuminate\Database\QueryException $e
     * @return \Illuminate\Http\Response
     */
    protected function handleQueryException(QueryException $e)
    {
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

    /**
     * Insert or update responsible groups.
     *
     * @param array $dataArray
     * @param int $callTypeId
     * @param int $callCategoryId
     * @param int $callSubCategoryId
     * @param int $serviceTypeConfigId
     * @return void
     */
    protected function insertOrUpdateResponsibleGroups(array $dataArray, $callTypeId, $callCategoryId, $callSubCategoryId, $serviceTypeConfigId)
    {
        // Delete all records with the given serviceTypeConfigId
        NCServiceResponsibleGroup::where('service_type_config_id', $serviceTypeConfigId)->delete();

        // Insert new records
        foreach ($dataArray as $data) {
            NCServiceResponsibleGroup::create([
                'call_type_id' => $callTypeId,
                'call_category_id' => $callCategoryId,
                'call_sub_category_id' => $callSubCategoryId,
                'group_id' => $data['id'],
                'service_type_config_id' => $serviceTypeConfigId,
                'group_name' => $data['name'],
                'tat_hours' => $data['tatHours'],
                'status' => 'active',
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
                'last_updated_by' => Auth::id(),
            ]);
        }
    }
}
