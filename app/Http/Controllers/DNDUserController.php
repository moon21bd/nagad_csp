<?php

namespace App\Http\Controllers;

use App\Models\DNDUser;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class DNDUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $dndUsers = DNDUser::all();
        return response()->json($dndUsers);

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
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'message' => 'required|string|max:60',
            'mobile_no' => $this->phoneValidationRules() . "|unique:dnd_users",
        ], $this->phoneValidationErrorMessages());

        if ($validator->fails()) {
            return sendValidationErrorResponse($validator);
        }

        try {
            $validatedData = $request->all();

            $authUserId = Auth::id();
            $dndUser = DNDUser::create([
                'name' => $validatedData['name'],
                'mobile_no' => $validatedData['mobile_no'],
                'message' => $validatedData['message'],
                'status' => 'active',
                'created_by' => $authUserId,
                'last_updated_by' => $authUserId,
            ]);

            return response()->json([
                'title' => 'Success.',
                'message' => 'Customer Profile created',
                'data' => $dndUser,
            ], 200);

        } catch (\Exception $e) {
            Log::error('CUSTOMER-PROFILE-REGISTRATION-ERROR: ' . $e->getMessage());

            return response()->json([
                'title' => 'Failed to register.',
                'message' => 'User registration failed.',
            ], Response::HTTP_PRECONDITION_FAILED);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DNDUser  $dNDUser
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $dndUser = DNDUser::findOrFail($id);
        return response()->json($dndUser);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\DNDUser  $dNDUser
     * @return \Illuminate\Http\Response
     */
    public function edit(DNDUser $dNDUser)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DNDUser  $dNDUser
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Define validation rules
        $phoneRules = explode('|', $this->phoneValidationRules());
        $rules = [
            'name' => 'sometimes|nullable|string|max:128',
            'message' => 'sometimes|nullable|string|max:60',
            'mobile_no' => array_merge($phoneRules, [
                Rule::unique('dnd_users')->ignore($id),
            ]),
        ];

        // Create a validator instance with the rules
        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {
            return sendValidationErrorResponse($validator);
        }

        try {
            // Fetch the user and update
            $dndUser = DNDUser::findOrFail($id);

            // Update user data
            $validatedData = $request->all();
            $validatedData['updated_by'] = $validatedData['last_updated_by'] = Auth::id();

            $dndUser->update($validatedData);

            // Return success response
            return response()->json([
                'title' => 'Success.',
                'message' => 'Customer Profile updated',
                'data' => $dndUser,
            ], 200);

        } catch (\Exception $e) {
            // Log the error and return failure response
            Log::error('CUSTOMER-PROFILE-UPDATE-ERROR: ' . $e->getMessage());

            return response()->json([
                'title' => 'Failed to update.',
                'message' => 'User update failed.',
            ], Response::HTTP_PRECONDITION_FAILED);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DNDUser  $dNDUser
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DNDUser::destroy($id);
        return response()->json(null, 204);

    }

    public function getDNDNumberVerification(Request $request, $mobileNo)
    {
        $preparePhone = Str::substr($mobileNo, -11);

        $dndUser = DNDUser::where('mobile_no', $preparePhone)
            ->where('status', 'active')
            ->first(); // Get the first matching record or null if none exists

        $dndExists = !is_null($dndUser); // Check if a record was found
        $message = $dndExists ? $dndUser->message : null; // Access the message if the record exists

        return response()->json([
            'in_dnd' => $dndExists,
            'specialMessage' => $message,
        ], 200);
    }

}
