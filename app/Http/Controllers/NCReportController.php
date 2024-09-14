<?php

namespace App\Http\Controllers;

use App\Models\NCCallType;
use App\Models\NCTicket;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class NCReportController extends Controller
{
    /* public function getTotalReportCount($id = null)
    {

    $assignedId = $id ?? '1';
    $totalCount = NCTicket::select('ticket_status', DB::raw('count(*) as total'))
    ->where('assign_to_group_id', $assignedId)
    ->groupBy('ticket_status')
    ->get();
    if ($totalCount) {
    foreach ($totalCount as $count) {
    if (strtoupper($count->ticket_status) == "PENDING") {
    $pendingTicket = $count->total;
    } elseif (strtoupper($count->ticket_status) == "OPEN") {
    $openTicket = $count->total;
    } elseif (strtoupper($count->ticket_status) == "CLOSED") {
    $closedTicket = $count->total;
    } elseif (strtoupper($count->ticket_status) == "REOPEN") {
    $reopenTicket = $count->total;
    } elseif (strtoupper($count->ticket_status) == "INPROGRESS") {
    $inProgressTicket = $count->total;
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
    } */

    public function getTotalReportCount($id = null)
    {
        // Get the logged-in user
        $user = auth()->user();

        // Initialize the base query
        $query = NCTicket::select('ticket_status', DB::raw('count(*) as total'));

        // Apply role-based filtering
        if ($user->hasRole('superadmin') || $user->hasRole('admin')) {
            // Admin or Super Admin can view all tickets, no filtering by assign_to_group_id
            $tickets = $query; // No assign_to_group_id filtering needed here
        } elseif ($user->hasRole('owner') && $user->group->hasOwner()) {
            // Group Owner can view tickets assigned to their group
            $tickets = $query->where('assign_to_group_id', $user->group_id);
        } else {
            // Handle unauthorized users
            return response()->json([
                'msg' => 'Unauthorized or no tickets available for your role.',
            ], 403);
        }

        // Continue with counting ticket statuses
        $totalCount = $tickets->groupBy('ticket_status')->get();

        // Initialize the ticket status counts
        $pendingTicket = $openTicket = $closedTicket = $reopenTicket = $inProgressTicket = 0;

        // Count the tickets based on their status
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
                    case "RESOLVED":
                        $reopenTicket = $count->total;
                        break;
                    case "INPROGRESS":
                        $inProgressTicket = $count->total;
                        break;
                }
            }

            // Return the ticket status counts in a JSON response
            return response()->json([
                'openTicket' => $openTicket ?? 0,
                'inProgressTicket' => $inProgressTicket ?? 0,
                'pendingTicket' => $pendingTicket ?? 0,
                'closedTicket' => $closedTicket ?? 0,
                'reopenTicket' => $reopenTicket ?? 0,
            ], 201);
        } else {
            // If no reports are found, return a message
            return response()->json([
                'msg' => 'No Report Found',
            ], 201);
        }
    }

    public function getDailyReportCount($id = null)
    {
        // Get the logged-in user
        $user = auth()->user();

        // Initialize the base query
        $query = NCTicket::select('call_type_id', 'call_type_name', DB::raw('count(*) as total'))
            ->join('nc_call_types', 'nc_call_types.id', 'nc_tickets.call_type_id')
            ->whereDate('ticket_created_at', '=', Carbon::today()->toDateString());

        // Apply role-based filtering
        if ($user->hasRole('superadmin') || $user->hasRole('admin')) {
            // Admin or Super Admin can view all tickets, no filtering by assign_to_group_id
            $tickets = $query; // No assign_to_group_id filtering needed
        } elseif ($user->hasRole('owner') && $user->group->hasOwner()) {
            // Group Owner can view tickets assigned to their group
            $tickets = $query->where('assign_to_group_id', $user->group_id);
        } else {
            // Handle unauthorized users
            return response()->json([
                'msg' => 'Unauthorized or no tickets available for your role.',
            ], 403);
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
    /* public function getDailyReportCount($id = null)
    {
    $assignedId = $id ?? '1';

    $callTypeWiseCounts = NCTicket::select('call_type_id', 'call_type_name', DB::raw('count(*) as total'))
    ->where('assign_to_group_id', $assignedId)
    ->whereDate('ticket_created_at', '=', Carbon::today()
    ->toDateString())
    ->join('nc_call_types', 'nc_call_types.id', 'nc_tickets.call_type_id')
    ->groupBy('nc_tickets.call_type_id')
    ->get();

    if ($callTypeWiseCounts) {
    foreach ($callTypeWiseCounts as $callTypeWiseCount) {
    if (strtoupper($callTypeWiseCount->call_type_name) == "COMPLAINT") {
    $totalComplaint = $callTypeWiseCount->total;
    } elseif (strtoupper($callTypeWiseCount->call_type_name) == "QUERY") {
    $totalQuery = $callTypeWiseCount->total;
    } elseif (strtoupper($callTypeWiseCount->call_type_name) == "SERVICE REQUEST") {
    $totalServiceRequest = $callTypeWiseCount->total;
    }
    }
    }

    $totalCount = NCTicket::where('assign_to_group_id', $assignedId)
    ->whereDate('ticket_created_at', '=', Carbon::today()
    ->toDateString())
    ->count();
    $groupWiseCounts = NCTicket::select('ticket_status', DB::raw('count(*) as total'))
    ->where('assign_to_group_id', $assignedId)
    ->whereDate('ticket_created_at', '=', Carbon::today()
    ->toDateString())
    ->groupBy('ticket_status')
    ->get();
    if ($groupWiseCounts) {
    foreach ($groupWiseCounts as $count) {
    if (strtoupper($count->ticket_status) == "CLOSED") {
    $closedTicket = $count->total;
    } elseif (strtoupper($count->ticket_status) == "INPROGRESS") {
    $inProgressTicket = $count->total;
    }
    }
    }
    return response()->json([
    'totalCount' => $totalCount ?? 0,
    'totalServiceRequest' => $totalServiceRequest ?? 0,
    'totalQuery' => $totalQuery ?? 0,
    'totalComplaint' => $totalComplaint ?? 0,
    'totalInProgressTicket' => $inProgressTicket ?? 0,
    'totalClosedTicket' => $closedTicket ?? 0,
    ], 201);
    } */

    public function getMonthWiseTicketStatusCount($id = null, $month = null)
    {

        $assignedId = $id ?? 1;
        $monthNumber = $this->getMonthNumber(strtoupper($month));
        $totalCount = NCTicket::select('ticket_status', DB::raw('count(*) as total'))
            ->where('assign_to_group_id', $assignedId)
            ->whereMonth('ticket_created_at', $monthNumber)
            ->groupBy('ticket_status')
            ->get();
        if ($totalCount) {
            foreach ($totalCount as $count) {
                if (strtoupper($count->ticket_status) == "PENDING") {
                    $pendingTicket = $count->total;
                } elseif (strtoupper($count->ticket_status) == "OPENED") {
                    $openTicket = $count->total;
                } elseif (strtoupper($count->ticket_status) == "CLOSED") {
                    $closedTicket = $count->total;
                } elseif (strtoupper($count->ticket_status) == "REOPEN") {
                    $reopenTicket = $count->total;
                } elseif (strtoupper($count->ticket_status) == "INPROGRESS") {
                    $inProgressTicket = $count->total;
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

    public function getDateWiseColumnChartDataCount($id = null, $month = null)
    {
        // Get the logged-in user
        $user = auth()->user();

        // Determine the assigned group id
        $assignedId = $id ?? 1;

        // Apply role-based filtering
        if ($user->hasRole('superadmin') || $user->hasRole('admin')) {
            // Admin or Super Admin can view all tickets, no filtering by assign_to_group_id
            $query = NCTicket::query();
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

    /* public function getDateWiseColumnChartDataCount($id = null, $month = null)
    {
    $assignedId = $id ?? 1;

    if ($month != null) {
    $monthNumber = $this->getMonthNumber(strtoupper($month));
    } else {
    $monthNumber = now()->format('m');
    }
    $year = now()->format('Y');

    $nextMonthNumber = (int) $monthNumber + 1;
    $from = "$year-$monthNumber-01";
    $to = "$year-$nextMonthNumber-01";

    $callTypes = NCCallType::select('id', 'call_type_name')
    ->where('status', 'active')
    ->get()
    ->toArray();
    if ($callTypes) {
    foreach ($callTypes as $callType) {
    if (strtoupper($callType['call_type_name']) == 'COMPLAINT') {
    $complaintId = $callType['id'];
    } elseif (strtoupper($callType['call_type_name']) == 'QUERY') {
    $queryId = $callType['id'];
    } elseif (strtoupper($callType['call_type_name']) == 'SERVICE REQUEST') {
    $serviceRequestId = $callType['id'];
    }
    }
    } else {
    return response()->json([
    'msg' => 'Call_Types_Found',
    ], 201);
    }

    $dbData = [];
    $period = new \DatePeriod(new \DateTime($from), new \DateInterval('P1D'), new \DateTime($to));
    $complaintData = [];
    $queryData = [];
    $serviceRequestData = [];

    foreach ($period as $date) {
    $range[$date->format("Y-m-d")] = 0;
    }

    $complaintTotalData = NCTicket::select(DB::raw('DATE(created_at) as time'), DB::raw('count(*) as count'))
    ->where('assign_to_group_id', $assignedId)
    ->where('call_type_id', $complaintId)
    ->whereDate('created_at', '>=', $from . ' 00:00:00')
    ->whereDate('created_at', '<=', $to . ' 23:59:59')
    ->groupBy('time')
    ->get();

    foreach ($complaintTotalData as $val) {
    $complaintData[$val->time] = $val->count;
    }

    $complaintTotalDataArray = array_replace($range, $complaintData);

    foreach ($period as $date) {
    $range[$date->format("Y-m-d")] = 0;
    }
    $queryTotalData = NCTicket::select(DB::raw('DATE(created_at) as time'), DB::raw('count(*) as count'))
    ->where('assign_to_group_id', $assignedId)
    ->where('call_type_id', $queryId)
    ->whereDate('created_at', '>=', $from . ' 00:00:00')
    ->whereDate('created_at', '<=', $to . ' 23:59:59')
    ->groupBy('time')
    ->get();

    foreach ($queryTotalData as $val) {
    $queryData[$val->time] = $val->count;
    }
    $queryTotalDataArray = array_replace($range, $queryData);

    foreach ($period as $date) {
    $range[$date->format("Y-m-d")] = 0;
    }
    $serviceRequestTotalData = NCTicket::select(DB::raw('DATE(created_at) as time'), DB::raw('count(*) as count'))
    ->where('assign_to_group_id', $assignedId)
    ->where('call_type_id', $serviceRequestId)
    ->whereDate('created_at', '>=', $from . ' 00:00:00')
    ->whereDate('created_at', '<=', $to . ' 23:59:59')
    ->groupBy('time')
    ->get();

    foreach ($serviceRequestTotalData as $val) {
    $serviceRequestData[$val->time] = $val->count;
    }
    $serviceRequestTotalDataArray = array_replace($range, $serviceRequestData);

    return response()->json([
    'totalComplaintData' => $complaintTotalDataArray ?? [],
    'totalQueryData' => $queryTotalDataArray ?? [],
    'totalServiceRequestData' => $serviceRequestTotalDataArray ?? [],
    ], 201);

    } */

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
}
