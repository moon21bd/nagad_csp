<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CrudController extends ResponseController
{
    public function addCallType(Request $request)
    {
        $data = [
            'call_type_name' => $request->name ?? '',
            'status'         => $request->status ?? 'active'
        ];
        $this->create("CallType", $data);
        return $this->sendResponse('Data Saved', 200);
    }

    public function getCallType()
    {

        $data = $this->getData('CallType');
        return $this->sendResponse($data, 200);

    }

    public function editCallType($id)
    {
        $data = $this->editData($id, "CallType");
        return $this->sendResponse($data, 200);
    }

    public function updateCallType(Request $request, $id)
    {
         // testing
        $data = [
            'call_type_name' => $request->name ?? '',
            'status'         => $request->status ?? 'active'
        ];
        $this->updateData($id, $data, "CallType");
        return $this->sendResponse('Data Updated', 200);
    }

    public function deleteCallType($id)
    {
        $this->deleteData($id, "CallType");
        return $this->sendResponse('Data Deleted', 200);
    }

    public function addCallCategory(Request $request)
    {
        $data = [
            'call_type_id'       => $request->call_type_id ?? null,
            'call_category_name' => $request->name ?? '',
            'status'             => $request->status ?? 'active'
        ];
        $this->create("CallCategory", $data);
        return $this->sendResponse('Data Saved', 200);
    }

    public function getCallCategory()
    {
        $data = $this->getData('CallCategory');
        return $this->sendResponse($data, 200);

    }

    public function editCallCategory($id)
    {
        $data = $this->editData($id, "CallCategory");
        return $this->sendResponse($data, 200);
    }

    public function updateCallCategory(Request $request, $id)
    {
        $data = [
            'call_type_id'       => $request->call_type_id ?? null,
            'call_category_name' => $request->name ?? '',
            'status'             => $request->status ?? 'active'
        ];
        $this->updateData($id, $data, "CallCategory");
        return $this->sendResponse('Data Updated', 200);
    }

    public function deleteCallCategory($id)
    {
        $this->deleteData($id, "CallCategory");
        return $this->sendResponse('Data Deleted', 200);
    }

    public function addCallSubCategory(Request $request)
    {
        $data = [
            'call_type_id'           => $request->call_type_id ?? null,
            'call_category_id'       => $request->call_category_id ?? null,
            'call_sub_category_name' => $request->name ?? '',
            'status'                 => $request->status ?? 'active'
        ];
        $this->create("CallSubCategory", $data);
        return $this->sendResponse('Data Saved', 200);
    }

    public function getCallSubCategory()
    {
        $data = $this->getData('CallSubCategory');
        return $this->sendResponse($data, 200);

    }

    public function editCallSubCategory($id)
    {
        $data = $this->editData($id, "CallSubCategory");
        return $this->sendResponse($data, 200);
    }

    public function updateCallSubCategory(Request $request, $id)
    {
        $data = [
            'call_type_id'           => $request->call_type_id ?? null,
            'call_category_id'       => $request->call_category_id ?? null,
            'call_sub_category_name' => $request->name ?? '',
            'status'                 => $request->status ?? 'active'
        ];
        $this->updateData($id, $data, "CallSubCategory");
        return $this->sendResponse('Data Updated', 200);
    }

    public function deleteCallSubCategory($id)
    {
        $this->deleteData($id, "CallSubCategory");
        return $this->sendResponse('Data Deleted', 200);
    }

    public function addCallSubSubCategory(Request $request)
    {
        $data = [
            'call_type_id'               => $request->call_type_id ?? null,
            'call_category_id'           => $request->call_category_id ?? null,
            'call_sub_category_id'       => $request->call_sub_category_id ?? null,
            'call_sub_sub_category_name' => $request->name ?? '',
            'status'                     => $request->status ?? 'active'
        ];
        $this->create("CallSubSubCategory", $data);
        return $this->sendResponse('Data Saved', 200);
    }

    public function getCallSubSubCategory()
    {
        $data = $this->getData('CallSubSubCategory');
        return $this->sendResponse($data, 200);

    }

    public function editCallSubSubCategory($id)
    {
        $data = $this->editData($id, "CallSubSubCategory");
        return $this->sendResponse($data, 200);
    }

    public function updateCallSubSubCategory(Request $request, $id)
    {
        $data = [
            'call_type_id'               => $request->call_type_id ?? null,
            'call_category_id'           => $request->call_category_id ?? null,
            'call_sub_category_id'       => $request->call_sub_category_id ?? null,
            'call_sub_sub_category_name' => $request->name ?? '',
            'status'                     => $request->status ?? 'active'
        ];
        $this->updateData($id, $data, "CallSubSubCategory");
        return $this->sendResponse('Data Updated', 200);
    }

    public function deleteCallSubSubCategory($id)
    {
        $this->deleteData($id, "CallSubSubCategory");
        return $this->sendResponse('Data Deleted', 200);
    }


    public function create($modelName, $data = [])
    {

        $model = "App\Models\\" . $modelName;
        $model::create($data);

    }

    public function getData($modelName)
    {
        $model = "App\Models\\" . $modelName;
        return $data = $model::where('status', 'active')
                             ->get();
    }

    public function editData($id, $modelName)
    {
        $model = "App\Models\\" . $modelName;
        return $model::where('id', $id)
                     ->first();
    }

    public function updateData($id, $data, $modelName)
    {
        $model = "App\Models\\" . $modelName;
        return $data = $model::where('id', $id)
                             ->update($data);
    }

    public function deleteData($id, $modelName)
    {
        $model = "App\Models\\" . $modelName;
        $model::where('id', $id)
              ->delete();
    }
}
