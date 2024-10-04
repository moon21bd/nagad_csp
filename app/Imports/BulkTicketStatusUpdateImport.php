<?php

namespace App\Imports;

use App\Models\NCTicket; // Make sure to include the NCTicket model
use App\Models\NCTicketTimeline;
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
        $ticket = NCTicket::find($data['ticket_id']);
        $now = Carbon::now();
        $authUserId = auth()->id();
        $ticketStatus = $data['ticket_status'];
        if ($ticket) {

            // Update the ticket
            $ticket->update([
                'ticket_status' => $ticketStatus,
                // 'ticket_comment' => $data['comment'],
                'ticket_updated_by' => $authUserId,
                'updated_at' => $now,
            ]);

            // Add ticket timeline data
            NCTicketTimeline::create([
                'ticket_id' => $ticket->id,
                'responsible_group_ids' => $ticket->responsible_group_ids,
                'ticket_status' => $ticketStatus,
                // 'ticket_comments' => $ticket->comments,
                // 'ticket_attachments' => $ticket->ticket_attachments,
                'ticket_opened_by' => $authUserId,
                'ticket_status_updated_by' => $authUserId,
                'ticket_updated_channel' => 'BULK_UPDATE',
                'opened_at' => $now,
                'last_time_opened_at' => $now,
            ]);

            // Store the updated ticket data
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
