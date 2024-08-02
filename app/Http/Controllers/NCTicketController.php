<?php

namespace App\Http\Controllers;

use App\Models\NCTicket;
use App\Services\TicketService;
use Illuminate\Http\Request;

class NCTicketController extends Controller
{
    protected $ticketService;

    public function __construct(TicketService $ticketService)
    {
        $this->ticketService = $ticketService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tickets = $this->ticketService->getAllTickets();
        return response()->json($tickets);
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

        $validated = $request->validate([
            'callTypeId' => 'required|exists:nc_call_types,id',
            'callCategoryId' => 'required|exists:nc_call_categories,id',
            'callSubCategoryId' => 'required|exists:nc_call_sub_categories,id',
            'callerMobileNo' => 'required|string',
            'requiredField' => 'nullable|array',
            'comments' => 'nullable|string',
            'attachment' => 'nullable|string',
            // |mimes:jpeg,png,jpg,gif,pdf,doc,docx,xls,xlsx',
        ]);
        // dd($validated);

        $ticket = $this->ticketService->createTicket($validated);

        return response()->json($ticket, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\NCTicket  $nCTicket
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $ticket = NCTicket::where('id', $id)->first();
        return response()->json($ticket, 200);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\NCTicket  $nCTicket
     * @return \Illuminate\Http\Response
     */
    public function edit(NCTicket $nCTicket)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\NCTicket  $nCTicket
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, NCTicket $nCTicket)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\NCTicket  $nCTicket
     * @return \Illuminate\Http\Response
     */
    public function destroy(NCTicket $nCTicket)
    {
        //
    }
}
