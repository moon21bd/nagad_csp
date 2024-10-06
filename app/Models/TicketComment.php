<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketComment extends Model
{
    use HasFactory;

    protected $table = 'nc_ticket_comments';

    protected $fillable = [
        'ticket_id',
        'created_by',
        'updated_by',
        'last_updated_by',
        'comment',
    ];

    // Define relationship to Ticket
    public function ticket()
    {
        return $this->belongsTo(NCTicket::class);
    }

    // Define relationship to User (assuming you have a User model)
    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function createdByUserGroup()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedByUser()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
