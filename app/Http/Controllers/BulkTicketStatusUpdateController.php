<?php

namespace App\Http\Controllers;

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
    public function store(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls',
        ]);

        $file = $request->file('excel_file');
        $path = $file->storeAs('uploads', 'status_updates.xlsx');

        // Process the file
        Excel::import(new BulkTicketStatusUpdateImport, storage_path('app/uploads/status_updates.xlsx'));

        return response()->json(['message' => 'File imported successfully'], 200);
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
    public function destroy(BulkTicketStatusUpdate $bulkTicketStatusUpdate)
    {
        //
    }
}
