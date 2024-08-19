<?php

namespace App\Http\Controllers;

use App\Models\EmailConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class EmailConfigController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $emails = EmailConfig::all();
        return response()->json($emails);

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

        $validatedData = $request->validate([
            'mailer' => 'required|string|max:20',
            'host' => 'required|string|max:64',
            'port' => 'required|integer',
            'username' => 'required|email|max:128|unique:email_configs,username',
            'password' => 'required|string|max:128',
            'encryption' => 'nullable|string|max:128',
            'from_address' => 'required|string|max:128',
            'from_name' => 'required|string|max:128',
            'status' => 'required|in:active,inactive',
        ]);

        $validatedData['created_by'] = $validatedData['updated_by'] = $validatedData['last_updated_by'] = Auth::id();

        $emailConfig = EmailConfig::create($validatedData);
        return response()->json($emailConfig, 201);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\EmailConfig  $emailConfig
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $emailConfig = EmailConfig::findOrFail($id);
        return response()->json($emailConfig);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\EmailConfig  $emailConfig
     * @return \Illuminate\Http\Response
     */
    public function edit(EmailConfig $emailConfig)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\EmailConfig  $emailConfig
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'mailer' => 'sometimes|required|string|max:20',
            'host' => 'sometimes|required|string|max:64',
            'port' => 'sometimes|required|integer',
            'username' => [
                'sometimes',
                'required',
                'email',
                'max:64',
                Rule::unique('email_configs')->ignore($id),
            ],
            'password' => 'sometimes|required|string|max:128',
            'encryption' => 'nullable|string|max:128',
            'from_address' => 'sometimes|required|string|max:128',
            'from_name' => 'sometimes|required|string|max:128',
            'status' => 'sometimes|required|in:active,inactive',
        ]);

        $emailConfig = EmailConfig::findOrFail($id);
        $validatedData['updated_by'] = $validatedData['last_updated_by'] = Auth::id();

        $emailConfig->update($validatedData);
        return response()->json($emailConfig);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\EmailConfig  $emailConfig
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        EmailConfig::destroy($id);
        return response()->json(null, 204);

    }

    public function allActiveEmailConfigs()
    {
        $configs = EmailConfig::where('status', 'active')
            ->get()
            ->makeHidden(['mailer', 'port', 'encryption', 'password', 'created_by', 'updated_by', 'last_updated_by', 'created_at', 'updated_at']);

        return response()->json($configs);
    }
}
