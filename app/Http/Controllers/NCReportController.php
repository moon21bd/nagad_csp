<?php

namespace App\Http\Controllers;

use App\Models\NCCallCategory;
use App\Models\NCCallType;
use App\Models\NCTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NCReportController extends Controller
{

    public function getTotalReportCount(Request $request, $groupId = null)
    {
        $dateFilter = $request->input('date');
        $user = auth()->user();

        $query = NCTicket::select('ticket_status', 'sla_status', DB::raw('count(*) as total'));

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

        $totalCount = $tickets->groupBy('ticket_status', 'sla_status')->get();

        $pendingTicket = $createdTicket = $resolvedTicket = $openTicket = $closedTicket = $reopenTicket = $inProgressTicket = 0;
        $slaSuccess = $slaFailed = 0;

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
        $dateFilter = $request->input('date', Carbon::today()->toDateString());

        $query = NCTicket::select('call_type_id', 'call_type_name', DB::raw('count(*) as total'))
            ->join('nc_call_types', 'nc_call_types.id', 'nc_tickets.call_type_id')
            ->whereDate('ticket_created_at', '=', $dateFilter);

        if ($user->hasRole('superadmin') || $user->hasRole('admin')) {
            if ($id) {
                $tickets = $query->where('assign_to_group_id', $id);
            } else {
                $tickets = $query;
            }
        } else {
            $tickets = $query->where('assign_to_group_id', $user->group_id);
        }

        $callTypeWiseCounts = $tickets->groupBy('nc_tickets.call_type_id')->get();
        $totalComplaint = $totalQuery = $totalServiceRequest = 0;

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

        $totalCount = $tickets->count();

        $groupWiseCounts = NCTicket::select('ticket_status', DB::raw('count(*) as total'))
            ->whereDate('ticket_created_at', '=', $dateFilter)
            ->groupBy('ticket_status')
            ->get();

        $closedTicket = $inProgressTicket = 0;

        foreach ($groupWiseCounts as $count) {
            switch (strtoupper($count->ticket_status)) {
                case "CLOSED":
                case "CLOSED - REACHED":
                case "CLOSED - NOT RECEIVED":
                case "CLOSED - NOT CONNECTED":
                case "CLOSED - SWITCHED OFF":
                case "CLOSED - NOT COOPERATED":
                    $closedTicket += $count->total;
                    break;
                case "CREATED":
                    $inProgressTicket = $count->total;
                    break;
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
    }

    public function getTopCategoriesData(Request $request, $groupId = null, $month = null)
    {
        $user = auth()->user();

        $date = $request->input('date');
        if (!$date) {
            $date = now()->format('Y-m-d');
        }

        $date = \Carbon\Carbon::parse($date);

        $monthNumber = $month ? $this->getMonthNumber(strtoupper($month)) : $date->format('m');
        $year = $date->format('Y');

        $assignedId = $groupId ?? $user->group_id;
        $query = NCTicket::query();

        if ($user->hasRole('superadmin') || $user->hasRole('admin')) {
            if ($groupId) {
                $query->where('assign_to_group_id', $assignedId);
            }
        } elseif ($user->hasRole('owner') && $user->group->hasOwner()) {
            $query->where('assign_to_group_id', $user->group_id);
        } else {
            return response()->json([
                'msg' => 'Unauthorized or no data available for your role.',
            ], 403);
        }

        $topCategoriesData = $query->select('call_category_id', DB::raw('COUNT(*) as count'))
            ->groupBy('call_category_id')
            ->orderBy('count', 'desc')
            ->limit(30)
            ->get();

        $categoryNames = NCCallCategory::whereIn('id', $topCategoriesData->pluck('call_category_id'))
            ->get()
            ->keyBy('id')
            ->map(function ($category) use ($topCategoriesData) {
                return [
                    'category' => $category->call_category_name,
                    'count' => $topCategoriesData->firstWhere('call_category_id', $category->id)->count ?? 0,
                ];
            });

        $categoryNamesArray = $categoryNames->values()->toArray();

        return response()->json($categoryNamesArray, 200);
    }

    public function getMonthWiseTicketStatusCount(Request $request, $id = null, $month = null)
    {
        Log::info('getMonthWiseTicketStatusCount|' . json_encode([$request->all(), $id, $month]));

        $user = auth()->user();
        $dateFilter = $request->input('date');
        $assignedId = $user->hasRole('superadmin') || $user->hasRole('admin') ? $id : $user->group_id;

        if ($dateFilter) {
            $dates = explode(',', $dateFilter);
            $fromDate = $dates[0];
            $toDate = isset($dates[1]) ? $dates[1] : $fromDate;

            if (!Carbon::createFromFormat('Y-m-d', $fromDate) || !Carbon::createFromFormat('Y-m-d', $toDate)) {
                return response()->json(['error' => 'Invalid date format.'], 400);
            }
        } else {
            $monthNumber = $month ? $this->getMonthNumber(strtoupper($month)) : now()->format('m');
            $year = now()->format('Y');
            $fromDate = "$year-$monthNumber-01";
            $toDate = date('Y-m-t', strtotime($fromDate));
        }

        $query = NCTicket::select('ticket_status', DB::raw('count(*) as total'))
            ->whereBetween('ticket_created_at', [$fromDate, $toDate]);

        if ($assignedId) {
            $query->where('assign_to_group_id', $assignedId);
        }

        $totalCount = $query->groupBy('ticket_status')->get();

        $statusCounts = [
            'totalCreated' => 0,
            'totalOpened' => 0,
            'totalResolved' => 0,
            'totalAssigned' => 0,
            'totalPending' => 0,
            'totalClosed' => 0,
            'totalReopen' => 0,
        ];

        foreach ($totalCount as $count) {
            $status = strtoupper($count->ticket_status);

            if (str_starts_with($status, 'CLOSED')) {
                $statusCounts['totalClosed'] += $count->total;
            } else {
                switch ($status) {
                    case "CREATED":
                        $statusCounts['totalCreated'] = $count->total;
                        break;
                    case "OPENED":
                        $statusCounts['totalOpened'] = $count->total;
                        break;
                    case "RESOLVED":
                        $statusCounts['totalResolved'] = $count->total;
                        break;
                    case "ASSIGNED":
                        $statusCounts['totalAssigned'] = $count->total;
                        break;
                    case "PENDING":
                        $statusCounts['totalPending'] = $count->total;
                        break;
                    case "REOPEN":
                        $statusCounts['totalReopen'] = $count->total;
                        break;
                }
            }
        }

        return response()->json($statusCounts, 201);
    }

    public function getDateWiseColumnChartDataCount(Request $request, $id = null, $month = null)
    {
        Log::info('getDateWiseColumnChartDataCount|' . json_encode([
            $request->all(), $id, $month,
        ]));

        $user = auth()->user();
        $assignedId = $id ?? 1;

        if ($user->hasRole('superadmin') || $user->hasRole('admin')) {
            $query = NCTicket::query();
            if ($id) {
                $query->where('assign_to_group_id', $assignedId);
            }
        } elseif ($user->hasRole('owner') && $user->group->hasOwner()) {
            $query = NCTicket::where('assign_to_group_id', $user->group_id);
        } else {
            return response()->json(['msg' => 'Unauthorized or no data available for your role.'], 403);
        }

        if (!$month) {
            return response()->json(['msg' => 'Month is required.'], 400);
        }

        $year = now()->format('Y');
        $monthNumber = $this->getMonthNumber(strtoupper($month));
        $from = "$year-$monthNumber-01";
        $to = date('Y-m-t', strtotime($from)); // Last day of the month

        $callTypes = NCCallType::select('id', 'call_type_name')
            ->where('status', 'active')
            ->get()
            ->pluck('id', 'call_type_name')->mapWithKeys(function ($id, $name) {
            return [strtoupper($name) => $id];
        });

        $period = new \DatePeriod(new \DateTime($from), new \DateInterval('P1D'), (new \DateTime($to))->modify('+1 day'));
        $range = array_fill_keys(array_map(fn($date) => $date->format('Y-m-d'), iterator_to_array($period)), 0);

        $fetchData = function ($callTypeId) use ($query, $from, $to) {
            return $query->clone()
                ->select(DB::raw('DATE(created_at) as time'), DB::raw('count(*) as count'))
                ->where('call_type_id', $callTypeId)
                ->whereBetween('created_at', ["$from 00:00:00", "$to 23:59:59"])
                ->groupBy('time')
                ->get()
                ->pluck('count', 'time')
                ->toArray();
        };

        $complaintData = array_replace($range, $fetchData($callTypes['COMPLAINT'] ?? null));
        $serviceRequestData = array_replace($range, $fetchData($callTypes['SERVICE REQUEST'] ?? null));

        return response()->json([
            'totalComplaintData' => $complaintData,
            'totalServiceRequestData' => $serviceRequestData,
        ], 201);
    }

    public function getMonthNumber($month)
    {
        $monthList = [
            'JANUARY' => '01', 'FEBRUARY' => '02', 'MARCH' => '03', 'APRIL' => '04',
            'MAY' => '05', 'JUNE' => '06', 'JULY' => '07', 'AUGUST' => '08',
            'SEPTEMBER' => '09', 'OCTOBER' => '10', 'NOVEMBER' => '11', 'DECEMBER' => '12',
        ];
        return $monthList[$month];
    }

    public function getUserStatistics(Request $request)
    {
        $nagadSebaGroupId = 3;

        $activeUsersCount = DB::table('user_session_status')
            ->where('status', 'Active')
            ->whereIn('id', function ($query) {
                $query->select(DB::raw('MAX(id)'))
                    ->from('user_session_status')
                    ->groupBy('user_id');
            })
            ->count('user_id');

        $breakUsersCount = DB::table('user_session_status')
            ->where('status', 'Break')
            ->whereIn('id', function ($query) {
                $query->select(DB::raw('MAX(id)'))
                    ->from('user_session_status')
                    ->groupBy('user_id');
            })
            ->count('user_id');

        $totalUsers = DB::table('users')->where('group_id', $nagadSebaGroupId)->distinct('id')->count('id');

        return response()->json([
            'totalActiveUser' => $activeUsersCount,
            'totalBreakUser' => $breakUsersCount,
            'totalNagadSebaUsers' => $totalUsers,
        ]);
    }

}
