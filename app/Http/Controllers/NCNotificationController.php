<?php

namespace App\Http\Controllers;

use App\Models\NCNotification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NCNotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $notifications = NCNotification::with('group')->where('user_id', Auth::id())->get();
        return response()->json($notifications);
    }

    /**
     * Store notifications for users in the specified groups.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'group_ids' => 'required|array',
            'is_group_lead_notified' => 'nullable|string',
            'group_ids.*' => 'exists:groups,id',
            'title' => 'required|string|max:255',
            'link' => 'required|string|max:255',
            'channel' => 'required|in:sms,email,web,others',
            'message' => 'required|string',
            'is_seen' => 'nullable|boolean',
            'is_read' => 'nullable|boolean',
        ]);

        // Retrieve users associated with the specified groups
        $users = User::whereIn('group_id', $validated['group_ids'])->get();

        // Iterate over user IDs and create notifications for each
        foreach ($users as $user) {

            // Adding condition to deny group owner notification
            if ($validated['is_group_lead_notified'] === 'no' && $user->parent_id === 0) {
                continue;
            }

            $notificationData = [
                'user_id' => $user->id,
                'group_id' => $user->group_id,
                'title' => $validated['title'],
                'link' => $validated['link'],
                'channel' => $validated['channel'],
                'message' => $validated['message'],
                'is_read' => $validated['is_read'] ?? false,
                'is_seen' => $validated['is_seen'] ?? false,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ];

            NCNotification::create($notificationData);
        }

        return response()->json(['message' => 'Notifications created successfully'], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\NCNotification  $nCNotification
     * @return \Illuminate\Http\Response
     */
    public function show(NCNotification $nCNotification)
    {
        if ($nCNotification->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($nCNotification);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\NCNotification  $nCNotification
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, NCNotification $nCNotification)
    {
        if ($nCNotification->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'is_read' => 'required|boolean',
        ]);

        $nCNotification->update($validated);

        return response()->json($nCNotification);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\NCNotification  $nCNotification
     * @return \Illuminate\Http\Response
     */
    public function destroy(NCNotification $nCNotification)
    {
        dd($nCNotification->id, Auth::id());
        if ($nCNotification->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $nCNotification->delete();

        return response()->json(['message' => 'Notification deleted']);
    }
}
