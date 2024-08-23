<?php

namespace App\Http\Controllers;

use App\Models\NCCallType;
use App\Models\NCTicket;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class NCReportController extends Controller
{
    public function getTotalReportCount($id = null)
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
    }

    public function getDailyReportCount($id = null)
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
    }

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
    }

    public function getDateWiseColumnChartDataCount($id = null, $month = null)
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
}
