<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function sendResponse($content = [], $status = 200)
    {
        return response()->json($content, $status);
    }

    public function sendError($error, $code = 404)
    {
        $response = [
            'errors' => $error,
        ];
        return response()->json($response, $code);
    }

    /**
     * function name: makeResponse
     * return array or object response
     * Created by Raqibul Hasan Moon
     *
     * @param status,code,message,data
     */
    protected function makeResponse($status, $code, $message = '', $data = '')
    {
        $obj = [
            'code' => $code,
            'status' => $status,
            'message' => $message,
        ];

        if (!empty($data)) {
            $obj['data'] = $data;
        }

        return $obj;
    }

    protected function phoneValidationRules()
    {
        return 'required|numeric|digits:11|regex:/(01)[3456789]{1}\d{8}/';
    }

    /**
     * Return a validation error message
     * Created by Raqibul Hasan Moon
     *
     * @return string[] error
     */
    protected function phoneValidationErrorMessages($mobileNo = 'mobile_no')
    {
        return [
            $mobileNo . '.required' => 'Phone number is required.',
            $mobileNo . '.numeric' => 'Please provide only numeric value.',
            $mobileNo . '.digits' => 'Phone number field must be 11 digits.',
            $mobileNo . '.regex' => 'Your phone number does not match with Bangladeshi operators.',
        ];
    }

}
