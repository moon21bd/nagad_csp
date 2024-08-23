<?php

namespace App\Http\Controllers;

use App\Imports\BulkTicketStatusUpdateImport;
use App\Models\BulkTicketStatusUpdate;
use Illuminate\Http\Request;

class BulkTicketStatusUpdateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $bulkTicket = BulkTicketStatusUpdate::all();
        return response()->json($bulkTicket);
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
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls',
        ]);

        $file = $request->file('excel_file');
        $fileName = 'bulk_ticket_status_updates_' . time() . '.' . $file->getClientOriginalExtension();

        $file->storeAs('', $fileName, 'bulk_tickets_status_updates');
        $fullFilePath = public_path('uploads/bulk-tickets-status/' . $fileName);

        // Import data
        $importer = new BulkTicketStatusUpdateImport();
        $importer->import($fullFilePath);

        $importedData = $importer->getImportedData();
        $getUpdatedTickets = $importer->getUpdatedTickets();
        $getNonMatchingTickets = $importer->getNonMatchingTickets();

        // Save the file path and response
        $bulkTicketsStatusUpdates = new BulkTicketStatusUpdate();
        $bulkTicketsStatusUpdates->excel_file = 'uploads/bulk-tickets-status/' . $fileName;
        $bulkTicketsStatusUpdates->response = base64_encode(json_encode([
            'imported' => $importedData,
            'updated' => $getUpdatedTickets,
            'nonMatched' => $getNonMatchingTickets,
        ]));
        $bulkTicketsStatusUpdates->created_by = auth()->id();
        $bulkTicketsStatusUpdates->lot = date('YmdHis');
        $bulkTicketsStatusUpdates->save();

        return response()->json(['message' => 'File imported and data saved successfully', 'file_path' => $bulkTicketsStatusUpdates->excel_file], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BulkTicketStatusUpdate  $bulkTicketStatusUpdate
     * @return \Illuminate\Http\Response
     */
    public function show(BulkTicketStatusUpdate $bulkTicketStatusUpdate)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BulkTicketStatusUpdate  $bulkTicketStatusUpdate
     * @return \Illuminate\Http\Response
     */
    public function edit(BulkTicketStatusUpdate $bulkTicketStatusUpdate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BulkTicketStatusUpdate  $bulkTicketStatusUpdate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BulkTicketStatusUpdate $bulkTicketStatusUpdate)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BulkTicketStatusUpdate  $bulkTicketStatusUpdate
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        BulkTicketStatusUpdate::destroy($id);
        return response()->json(null, 204);
    }
}
