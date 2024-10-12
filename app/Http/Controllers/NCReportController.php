<?php

namespace App\Http\Controllers;

use App\Models\NCCallType;
use App\Models\NCTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class NCReportController extends Controller
{
    /* public function getTotalReportCount(Request $request, $groupId = null)
    {
    $dateFilter = $request->input('date');

    $user = auth()->user();
    $query = NCTicket::select('ticket_status', DB::raw('count(*) as total'));

    if ($user->hasRole('superadmin') || $user->hasRole('admin')) {
    if ($groupId) {
    $tickets = $query->where('assign_to_group_id', $groupId);
    } else {
    $tickets = $query;
    }
    } else {
    $tickets = $query->where('assign_to_group_id', $user->group_id);
    }

    if ($dateFilter) {
    $dates = explode(',', $dateFilter);
    $startDate = $dates[0];
    $endDate = $dates[1] ?? $dates[0];
    $tickets->whereDate('ticket_created_at', '>=', $startDate)
    ->whereDate('ticket_created_at', '<=', $endDate);
    }

    $totalCount = $tickets->groupBy('ticket_status')->get();
    $pendingTicket = $createdTicket = $resolvedTicket = $openTicket = $closedTicket = $reopenTicket = $inProgressTicket = 0;

    if ($totalCount->isNotEmpty()) {
    foreach ($totalCount as $count) {
    switch (strtoupper($count->ticket_status)) {
    case "PENDING":
    $pendingTicket = $count->total;
    break;
    case "CREATED":
    $createdTicket = $count->total;
    break;
    case "OPENED":
    $openTicket = $count->total;
    break;
    case "CLOSED":
    case "CLOSED - REACHED":
    case "CLOSED - NOT RECEIVED":
    case "CLOSED - NOT CONNECTED":
    case "CLOSED - SWITCHED OFF":
    case "CLOSED - NOT COOPERATED":
    $closedTicket += $count->total;
    break;
    case "REOPEN":
    $reopenTicket = $count->total;
    break;
    case "RESOLVED":
    $resolvedTicket = $count->total;
    break;
    case "INPROGRESS":
    $inProgressTicket = $count->total;
    break;
    }
    }

    return response()->json([
    'openTicket' => $openTicket,
    'inProgressTicket' => $inProgressTicket,
    'createdTicket' => $createdTicket,
    'resolvedTicket' => $resolvedTicket,
    'pendingTicket' => $pendingTicket,
    'closedTicket' => $closedTicket,
    'reopenTicket' => $reopenTicket,
    ], 201);
    } else {
    // If no reports are found, return a message
    return response()->json([
    'msg' => 'No Report Found',
    ], 201);
    }
    } */

    public function getTotalReportCount(Request $request, $groupId = null)
    {
        $dateFilter = $request->input('date');

        $user = auth()->user();
        $query = NCTicket::select('ticket_status', 'sla_status', DB::raw('count(*) as total'));

        // Check user roles to set the group ID
        if ($user->hasRole('superadmin') || $user->hasRole('admin')) {
            if ($groupId) {
                $tickets = $query->where('assign_to_group_id', $groupId);
            } else {
                $tickets = $query;
            }
        } else {
            $tickets = $query->where('assign_to_group_id', $user->group_id);
        }

        // Apply date filter if provided
        if ($dateFilter) {
            $dates = explode(',', $dateFilter);
            $startDate = $dates[0];
            $endDate = $dates[1] ?? $dates[0];
            $tickets->whereDate('ticket_created_at', '>=', $startDate)
                ->whereDate('ticket_created_at', '<=', $endDate);
        }

        // Group by ticket status and SLA status
        $totalCount = $tickets->groupBy('ticket_status', 'sla_status')->get();

        // Initialize ticket status counts
        $pendingTicket = $createdTicket = $resolvedTicket = $openTicket = $closedTicket = $reopenTicket = $inProgressTicket = 0;
        $slaSuccess = $slaFailed = 0;

        if ($totalCount->isNotEmpty()) {
            foreach ($totalCount as $count) {
                // Count ticket statuses
                switch (strtoupper($count->ticket_status)) {
                    case "PENDING":
                        $pendingTicket = $count->total;
                        break;
                    case "CREATED":
                        $createdTicket = $count->total;
                        break;
                    case "OPENED":
                        $openTicket = $count->total;
                        break;
                    case "CLOSED":
                    case "CLOSED - REACHED":
                    case "CLOSED - NOT RECEIVED":
                    case "CLOSED - NOT CONNECTED":
                    case "CLOSED - SWITCHED OFF":
                    case "CLOSED - NOT COOPERATED":
                        $closedTicket += $count->total;
                        break;
                    case "REOPEN":
                        $reopenTicket = $count->total;
                        break;
                    case "RESOLVED":
                        $resolvedTicket = $count->total;
                        break;
                    case "INPROGRESS":
                        $inProgressTicket = $count->total;
                        break;
                }

                // Count SLA statuses
                switch (strtolower($count->sla_status)) {
                    case "met":
                        $slaSuccess += $count->total;
                        break;
                    case "breached":
                    case "review_needed":
                    case "in_progress":
                        $slaFailed += $count->total;
                        break;
                }
            }

            return response()->json([
                'openTicket' => $openTicket,
                'inProgressTicket' => $inProgressTicket,
                'createdTicket' => $createdTicket,
                'resolvedTicket' => $resolvedTicket,
                'pendingTicket' => $pendingTicket,
                'closedTicket' => $closedTicket,
                'reopenTicket' => $reopenTicket,
                'slaSuccess' => $slaSuccess,
                'slaFailed' => $slaFailed,
            ], 201);
        } else {
            // If no reports are found, return a message
            return response()->json([
                'msg' => 'No Report Found',
            ], 201);
        }
    }

    public function getDailyReportCount(Request $request, $id = null)
    {
        $user = auth()->user();
        // $dateFilter = $request->input('date');

        $query = NCTicket::select('call_type_id', 'call_type_name', DB::raw('count(*) as total'))
            ->join('nc_call_types', 'nc_call_types.id', 'nc_tickets.call_type_id')
            ->whereDate('ticket_created_at', '=', Carbon::today()->toDateString());

        /* if ($dateFilter) {
        $dates = explode(',', $dateFilter);
        $startDate = $dates[0];
        $endDate = $dates[1] ?? $dates[0];
        $query->whereDate('ticket_created_at', '>=', $startDate)
        ->whereDate('ticket_created_at', '<=', $endDate);
        } else {
        $query->whereDate('ticket_created_at', '=', Carbon::today()->toDateString());
        } */

        $query->whereDate('ticket_created_at', '=', Carbon::today()->toDateString());

        if ($user->hasRole('superadmin') || $user->hasRole('admin')) {
            if ($id) {
                $tickets = $query->where('assign_to_group_id', $id);
            } else {
                $tickets = $query;
            }
        } else {
            $tickets = $query->where('assign_to_group_id', $user->group_id);
        }

        // Call type-wise counts
        $callTypeWiseCounts = $tickets->groupBy('nc_tickets.call_type_id')->get();

        // Initialize the call type counts
        $totalComplaint = $totalQuery = $totalServiceRequest = 0;

        // Count the tickets based on call type
        if ($callTypeWiseCounts->isNotEmpty()) {
            foreach ($callTypeWiseCounts as $callTypeWiseCount) {
                switch (strtoupper($callTypeWiseCount->call_type_name)) {
                    case "COMPLAINT":
                        $totalComplaint = $callTypeWiseCount->total;
                        break;
                    case "QUERY":
                        $totalQuery = $callTypeWiseCount->total;
                        break;
                    case "SERVICE REQUEST":
                        $totalServiceRequest = $callTypeWiseCount->total;
                        break;
                }
            }
        }

        // Total ticket count for today
        $totalCount = $tickets->count();

        // Status-wise counts
        $groupWiseCounts = NCTicket::select('ticket_status', DB::raw('count(*) as total'))
            ->whereDate('ticket_created_at', '=', Carbon::today()->toDateString())
            ->groupBy('ticket_status')
            ->get();

        // Initialize the status counts
        $closedTicket = $inProgressTicket = 0;

        // Count the tickets based on their status
        if ($groupWiseCounts->isNotEmpty()) {
            foreach ($groupWiseCounts as $count) {
                switch (strtoupper($count->ticket_status)) {
                    case "CLOSED":
                        $closedTicket = $count->total;
                        break;
                    case "INPROGRESS":
                        $inProgressTicket = $count->total;
                        break;
                }
            }
        }

        // Return the counts in a JSON response
        return response()->json([
            'totalCount' => $totalCount ?? 0,
            'totalServiceRequest' => $totalServiceRequest ?? 0,
            'totalQuery' => $totalQuery ?? 0,
            'totalComplaint' => $totalComplaint ?? 0,
            'totalInProgressTicket' => $inProgressTicket ?? 0,
            'totalClosedTicket' => $closedTicket ?? 0,
        ], 201);
    }

    public function getMonthWiseTicketStatusCount(Request $request, $id = null, $month = null)
    {

        $user = auth()->user();
        $dateFilter = $request->input('date');

        if ($user->hasRole('superadmin') || $user->hasRole('admin')) {
            $assignedId = $id;
        } else {
            $assignedId = $user->group_id;
        }

        $monthNumber = $this->getMonthNumber(strtoupper($month));

        $query = NCTicket::select('ticket_status', DB::raw('count(*) as total'))
            ->whereMonth('ticket_created_at', $monthNumber);

        if ($assignedId) {
            $query->where('assign_to_group_id', $assignedId);
        }

        // Check if a date filter is present
        if ($dateFilter) {
            $dates = explode(',', $dateFilter);
            $startDate = $dates[0];
            $endDate = $dates[1] ?? $dates[0]; // Use the same date if no end date is provided
            $query->whereDate('ticket_created_at', '>=', $startDate)
                ->whereDate('ticket_created_at', '<=', $endDate);
        }

        // Group by ticket status and execute the query
        $totalCount = $query->groupBy('ticket_status')->get();

        // Initialize ticket status counts
        $pendingTicket = $openTicket = $closedTicket = $reopenTicket = $inProgressTicket = 0;

        if ($totalCount->isNotEmpty()) {
            foreach ($totalCount as $count) {
                switch (strtoupper($count->ticket_status)) {
                    case "PENDING":
                        $pendingTicket = $count->total;
                        break;
                    case "OPENED":
                        $openTicket = $count->total;
                        break;
                    case "CLOSED":
                        $closedTicket = $count->total;
                        break;
                    case "REOPEN":
                        $reopenTicket = $count->total;
                        break;
                    case "INPROGRESS":
                        $inProgressTicket = $count->total;
                        break;
                }
            }

            return response()->json([
                'openTicket' => $openTicket ?? 0,
                'inProgressTicket' => $inProgressTicket ?? 0,
                'pendingTicket' => $pendingTicket ?? 0,
                'closedTicket' => $closedTicket ?? 0,
                'reopenTicket' => $reopenTicket ?? 0,
            ], 201);
        } else {
            return response()->json([
                'msg' => 'No Report Found',
            ], 201);
        }
    }

    public function getDateWiseColumnChartDataCount($id = null, $month = null, $dateFilter = null)
    {
        // Get the logged-in user
        $user = auth()->user();

        // Determine the assigned group id
        $assignedId = $id ?? 1;

        // Apply role-based filtering
        if ($user->hasRole('superadmin') || $user->hasRole('admin')) {
            // Admin or Super Admin can view all tickets
            $query = NCTicket::query();

            // If group ID is provided, filter by that group
            if ($id) {
                $query->where('assign_to_group_id', $assignedId);
            }
        } elseif ($user->hasRole('owner') && $user->group->hasOwner()) {
            // Group Owner can view tickets assigned to their group
            $query = NCTicket::where('assign_to_group_id', $user->group_id);
        } else {
            // If the user doesn't have the required role, return unauthorized response
            return response()->json([
                'msg' => 'Unauthorized or no data available for your role.',
            ], 403);
        }

        // Handle the month and date logic
        $monthNumber = $month ? $this->getMonthNumber(strtoupper($month)) : now()->format('m');
        $year = now()->format('Y');
        $nextMonthNumber = (int) $monthNumber + 1;
        $from = "$year-$monthNumber-01";
        $to = "$year-$nextMonthNumber-01";

        // Get the call type IDs
        $callTypes = NCCallType::select('id', 'call_type_name')
            ->where('status', 'active')
            ->get()
            ->toArray();

        // Initialize call type IDs
        $complaintId = $queryId = $serviceRequestId = null;

        foreach ($callTypes as $callType) {
            switch (strtoupper($callType['call_type_name'])) {
                case 'COMPLAINT':
                    $complaintId = $callType['id'];
                    break;
                case 'QUERY':
                    $queryId = $callType['id'];
                    break;
                case 'SERVICE REQUEST':
                    $serviceRequestId = $callType['id'];
                    break;
            }
        }

        // Initialize date range and result arrays
        $dbData = [];
        $period = new \DatePeriod(new \DateTime($from), new \DateInterval('P1D'), new \DateTime($to));
        $range = [];
        foreach ($period as $date) {
            $range[$date->format("Y-m-d")] = 0;
        }

        // Fetch and organize the complaint data
        $complaintTotalData = $query->clone()
            ->select(DB::raw('DATE(created_at) as time'), DB::raw('count(*) as count'))
            ->where('call_type_id', $complaintId)
            ->whereDate('created_at', '>=', $from . ' 00:00:00')
            ->whereDate('created_at', '<=', $to . ' 23:59:59')
            ->groupBy('time')
            ->get();

        $complaintData = [];
        foreach ($complaintTotalData as $val) {
            $complaintData[$val->time] = $val->count;
        }
        $complaintTotalDataArray = array_replace($range, $complaintData);

        // Fetch and organize the query data
        $queryTotalData = $query->clone()
            ->select(DB::raw('DATE(created_at) as time'), DB::raw('count(*) as count'))
            ->where('call_type_id', $queryId)
            ->whereDate('created_at', '>=', $from . ' 00:00:00')
            ->whereDate('created_at', '<=', $to . ' 23:59:59')
            ->groupBy('time')
            ->get();

        $queryData = [];
        foreach ($queryTotalData as $val) {
            $queryData[$val->time] = $val->count;
        }
        $queryTotalDataArray = array_replace($range, $queryData);

        // Fetch and organize the service request data
        $serviceRequestTotalData = $query->clone()
            ->select(DB::raw('DATE(created_at) as time'), DB::raw('count(*) as count'))
            ->where('call_type_id', $serviceRequestId)
            ->whereDate('created_at', '>=', $from . ' 00:00:00')
            ->whereDate('created_at', '<=', $to . ' 23:59:59')
            ->groupBy('time')
            ->get();

        $serviceRequestData = [];
        foreach ($serviceRequestTotalData as $val) {
            $serviceRequestData[$val->time] = $val->count;
        }
        $serviceRequestTotalDataArray = array_replace($range, $serviceRequestData);

        // Return the data in a JSON response
        return response()->json([
            'totalComplaintData' => $complaintTotalDataArray ?? [],
            'totalQueryData' => $queryTotalDataArray ?? [],
            'totalServiceRequestData' => $serviceRequestTotalDataArray ?? [],
        ], 201);
    }

    public function getMonthNumber($month)
    {
        $monthList = [
            'JANUARY' => '01',
            'FEBRUARY' => '02',
            'MARCH' => '03',
            'APRIL' => '04',
            'MAY' => '05',
            'JUNE' => '06',
            'JULY' => '07',
            'AUGUST' => '08',
            'SEPTEMBER' => '09',
            'OCTOBER' => '10',
            'NOVEMBER' => '11',
            'DECEMBER' => '12',
        ];
        return $monthList[$month];
    }

    public function getUserStatistics(Request $request)
    {
        $totalActiveUsers = DB::table('user_login_activities')
            ->whereNotNull('last_login')
            ->distinct('user_id')
            ->count('user_id');

        $idleUsers = DB::table('user_login_activities')
            ->whereNotNull('last_logout')
            ->distinct('user_id')
            ->count('user_id');

        $inactiveUsers = DB::table('users')
            ->whereIn('status', ['Inactive', 'Pending'])
            ->distinct('id')
            ->count('id');

        $totalUsers = DB::table('users')->distinct('id')->count('id');

        return response()->json([
            'totalActiveUser' => $totalActiveUsers,
            'totalIdleUser' => $idleUsers,
            'totalInactiveUser' => $inactiveUsers,
            'totalUsers' => $totalUsers,
        ]);
    }

}
