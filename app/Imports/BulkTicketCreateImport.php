<?php

namespace App\Imports;

use App\Models\NCTicket;
use Carbon\Carbon;
use Shuchkin\SimpleXLSX;

class BulkTicketCreateImport
{
    protected $importedData = [];

    public function import($filePath)
    {
        if ($xlsx = SimpleXLSX::parse($filePath)) {
            foreach ($xlsx->rows() as $index => $row) {
                // Skip the header row
                if ($index == 0) {
                    continue;
                }

                // Map row data to an associative array
                $data = [
                    'service_type_id' => $row[0], // Adjust index based on your column order
                    'service_category_id' => $row[1],
                    'service_sub_category_id' => $row[2],
                    'mobile_no' => $row[3],
                    'group_id' => $row[4],
                    'ticket_comment' => $row[5],
                    'ticket_status' => $row[6],
                ];

                // Capture imported data
                $this->importedData[] = $data;

                // Create the model
                $this->model($data);
            }
        } else {
            throw new \Exception(SimpleXLSX::parseError());
        }
    }

    public function model(array $data)
    {
        return NCTicket::create([
            'uuid' => generateTicketUuid(), // Ensure you have this function implemented
            'call_type_id' => $data['service_type_id'],
            'call_category_id' => $data['service_category_id'],
            'call_sub_category_id' => $data['service_sub_category_id'],
            'caller_mobile_no' => $data['mobile_no'],
            'assign_to_group_id' => $data['group_id'],
            'ticket_comment' => $data['ticket_comment'],
            'ticket_status' => $data['ticket_status'],
            'ticket_channel' => 'BULK_CREATE',
            'sla_status' => 'NORMAL',
            'ticket_created_by' => auth()->id(),
            'ticket_created_at' => Carbon::now(),
        ]);
    }

    public function getImportedData()
    {
        return $this->importedData;
    }
}
