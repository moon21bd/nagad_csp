<?php

namespace App\Http\Controllers;

use App\Imports\BulkTicketCreateImport;
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
        $bulkTicket = BulkTicketCreate::all();
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
        $fileName = 'bulk_ticket_creates_' . time() . '.' . $file->getClientOriginalExtension();

        $file->storeAs('', $fileName, 'public_uploads');
        $fullFilePath = public_path('uploads/bulk-tickets/' . $fileName);

        // Import data
        $importer = new BulkTicketCreateImport();
        $importer->import($fullFilePath);

        $importedData = $importer->getImportedData();

        // Save the file path and response
        $bulkTicketCreate = new BulkTicketCreate();
        $bulkTicketCreate->excel_file = 'uploads/bulk-tickets/' . $fileName;
        $bulkTicketCreate->response = base64_encode(json_encode($importedData));
        $bulkTicketCreate->created_by = auth()->id();
        $bulkTicketCreate->lot = date('YmdHis');
        $bulkTicketCreate->save();

        return response()->json(['message' => 'File imported and data saved successfully', 'file_path' => $bulkTicketCreate->excel_file], 200);
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
    public function destroy($id)
    {
        BulkTicketCreate::destroy($id);
        return response()->json(null, 204);
    }
}
