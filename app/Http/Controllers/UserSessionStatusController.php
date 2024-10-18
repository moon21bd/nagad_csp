<?php

namespace App\Http\Controllers;

use App\Models\UserSessionStatus;
use Illuminate\Http\Request;

class UserSessionStatusController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'status' => 'required|in:Active,Break',
        ]);

        $userId = auth()->user()->id;
        $ipAddress = getIPAddress();

        UserSessionStatus::create([
            'user_id' => $userId,
            'group_id' => auth()->user()->group_id,
            'status' => $request->status,
            'created_by' => $userId,
            'ip_address' => $ipAddress,
        ]);

        return response()->json(['message' => 'Status updated successfully.']);
    }

    public function currentStatus()
    {
        $userId = auth()->id();
        $status = UserSessionStatus::where('user_id', $userId)
            ->latest()
            ->first();

        return response()->json(['status' => $status ? $status->status : 'Active']);
    }

    // Optionally, you can add methods for reporting based on active/break users
    public function getStatusReport()
    {
        $activeUsers = UserSessionStatus::where('status', 'Active')->get();
        $breakUsers = UserSessionStatus::where('status', 'Break')->get();

        return view('reports.session_status_report', compact('activeUsers', 'breakUsers'));
    }
}
