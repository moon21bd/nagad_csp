<?php

namespace App\Http\Controllers;

use App\Models\BulkTicketCreate;
use Illuminate\Http\Request;

class BulkTicketCreateController extends Controller
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
        $path = $file->storeAs('uploads', 'ticket_creates.xlsx');

        // Process the file
        Excel::import(new BulkTicketCreateImport, storage_path('app/uploads/ticket_creates.xlsx'));

        return response()->json(['message' => 'File imported successfully'], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BulkTicketCreate  $bulkTicketCreate
     * @return \Illuminate\Http\Response
     */
    public function show(BulkTicketCreate $bulkTicketCreate)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BulkTicketCreate  $bulkTicketCreate
     * @return \Illuminate\Http\Response
     */
    public function edit(BulkTicketCreate $bulkTicketCreate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BulkTicketCreate  $bulkTicketCreate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BulkTicketCreate $bulkTicketCreate)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BulkTicketCreate  $bulkTicketCreate
     * @return \Illuminate\Http\Response
     */
    public function destroy(BulkTicketCreate $bulkTicketCreate)
    {
        //
    }
}
