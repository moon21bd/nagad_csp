<?php

namespace App\Imports;

use App\Models\BulkTicketCreate;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;

class BulkTicketCreateImport implements ToModel
{
    use Importable;

    public function model(array $row)
    {
        return new BulkTicketCreate([
            'service_type_id' => $row[0],
            'service_category_id' => $row[1],
            'service_sub_category_id' => $row[2],
            'mobile_no' => $row[3],
            'group_id' => $row[4],
            'ticket_comment' => $row[5],
            'ticket_status' => $row[6],
            'ticket_channel' => $row[7],
            'excel_file' => 'ticket_creates.xlsx',
            'created_by' => auth()->id(),
        ]);
    }
}
