<?php

namespace App\Imports;

use App\Models\NCTicket; // Make sure to include the NCTicket model
use App\Models\NCTicketTimeline;
use App\Models\TicketComment;
use Carbon\Carbon;
use Shuchkin\SimpleXLSX;

class BulkTicketStatusUpdateImport
{
    protected $importedData = [];
    protected $updatedTickets = [];
    protected $nonMatchingTickets = [];

    public function import($filePath)
    {
        if ($xlsx = SimpleXLSX::parse($filePath)) {
            foreach ($xlsx->rows() as $index => $row) {
                if ($index == 0) {
                    continue;
                }

                // Map row data to an associative array
                $data = [
                    'ticket_id' => $row[0],
                    'ticket_status' => $row[1],
                    'comment' => $row[2],
                ];

                $this->importedData[] = $data;
                $this->processData($data);
            }
        } else {
            throw new \Exception(SimpleXLSX::parseError());
        }
    }

    protected function processData(array $data)
    {
        $ticket = NCTicket::where('uuid', $data['ticket_id'])->first();
        $now = Carbon::now();
        $authUserId = auth()->id();
        $ticketStatus = $data['ticket_status'];
        if ($ticket) {
            $ticketId = $ticket->id;

            $ticket->update([
                'ticket_status' => $ticketStatus,
                'ticket_updated_by' => $authUserId,
                'updated_at' => $now,
            ]);

            TicketComment::create([
                'ticket_id' => $ticketId,
                'comment' => $data['comments'],
                'created_by' => $authUserId,
            ]);

            // Add ticket timeline data
            NCTicketTimeline::create([
                'ticket_id' => $ticketId,
                'responsible_group_ids' => $ticket->responsible_group_ids,
                'ticket_status' => $ticketStatus,
                'ticket_opened_by' => $authUserId,
                'ticket_status_updated_by' => $authUserId,
                'ticket_updated_channel' => 'BULK_UPDATE',
                'opened_at' => $now,
                'last_time_opened_at' => $now,
            ]);

            $this->updatedTickets[] = $data;
        } else {
            // Store the non-matching ticket data
            $this->nonMatchingTickets[] = $data;
        }
    }

    public function getImportedData()
    {
        return $this->importedData;
    }

    public function getUpdatedTickets()
    {
        return $this->updatedTickets;
    }

    public function getNonMatchingTickets()
    {
        return $this->nonMatchingTickets;
    }
}
