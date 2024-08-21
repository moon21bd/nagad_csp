<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BulkTicketStatusUpdate extends Model
{
    protected $table = "bulk_ticket_status_updates";

    protected $fillable = [
        'ticket_id',
        'transaction_id',
        'ticket_status',
        'comment',
        'excel_file',
        'created_by',
        'updated_by',
    ];

}
