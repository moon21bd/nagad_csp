<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NCTicketTimeline extends Model
{
    use HasFactory;

    protected $table = 'nc_ticket_timelines';

    // public $timestamps = false;
    protected $fillable = [
        'ticket_id',
        'responsible_group_ids',
        'ticket_status',
        'ticket_comments',
        'ticket_attachments',
        'ticket_opened_by',
        'ticket_status_updated_by',
        'opened_at',
        'last_time_opened_at',
        'last_time_opened_at',
        'updated_by',
        'last_updated_by',
    ];

    public function ticket()
    {
        return $this->belongsTo(NCTicket::class, 'ticket_id');
    }

    // Method to get group names from comma-separated IDs
    public function getResponsibleGroupNamesAttribute()
    {
        // Split the comma-separated IDs into an array
        $groupIds = explode(',', $this->responsible_group_ids);

        // Retrieve the group names from the Group model
        $groupNames = Group::whereIn('id', $groupIds)->pluck('name')->toArray();

        // Return the comma-separated group names
        return implode(', ', $groupNames);
    }

}
