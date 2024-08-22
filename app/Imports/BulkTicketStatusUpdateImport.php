<?php

namespace App\Imports;

use App\Models\BulkTicketStatusUpdate;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;

class BulkTicketStatusUpdateImport implements ToModel
{
    use Importable;

    public function model(array $row)
    {
        return new BulkTicketStatusUpdate([
            'ticket_id' => $row[0],
            'transaction_id' => $row[1],
            'ticket_status' => $row[2],
            'comment' => $row[3],
            'excel_file' => 'status_updates.xlsx',
            'created_by' => auth()->id(),
        ]);
    }
}
