<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;

class ClickActivityController extends Controller
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
    // Store a new activity log
    public function store(Request $request)
    {
        $request->validate([
            'event' => 'required|string|max:255',
            'additional_data' => 'nullable|string',
            'subject_type' => 'nullable|string',
            'subject_id' => 'nullable|integer',
        ]);

        // Log the activity if the user is authenticated
        if (Auth::check()) {
            activity()
                ->causedBy(Auth::user())
                ->withProperties(['additional_data' => $request->additional_data])
                ->tap(function (Activity $activity) use ($request) {
                    $activity->subject_type = $request->subject_type;
                    $activity->subject_id = $request->subject_id;
                    $activity->event = $request->event;
                })
                ->log('User clicked: ' . $request->event);
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
