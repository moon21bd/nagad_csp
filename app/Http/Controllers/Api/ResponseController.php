<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller as Controller;


class ResponseController extends Controller {
    public function sendResponse($response,$code=200) {
        return response()->json($response,$code,['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'],
        JSON_UNESCAPED_UNICODE)->setStatusCode($response['code'] ?? 200);
    }

    public function sendError($error, $code = 404) {
        $response = [
            'error' => $error,
        ];
        return response()->json($response, $code);
    }
}
