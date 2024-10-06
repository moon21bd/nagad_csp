<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NCTicket extends Model
{
    use HasFactory;

    protected $table = 'nc_tickets';

    // public $timestamps = false;
    protected $fillable = [
        'uuid',
        'call_type_id',
        'call_category_id',
        'call_sub_category_id',
        'caller_mobile_no',
        'required_fields',
        'group_id',
        'assign_to_group_id',
        'assign_by_id',
        'is_ticket_reassign',
        'comments',
        'sla_status',
        'attachment',
        'ticket_notification_status',
        'is_customer_notified',
        'responsible_group_ids',
        'initial_assign_id',
        'assign_to_user_id',
        'ticket_attachment',
        'ticket_status',
        'ticket_source',
        'ticket_channel',
        'ticket_created_by',
        'sla_updated_at',
        'ticket_updated_by',
        'ticket_last_updated_by',
        'ticket_created_at',
        'ticket_updated_at',
    ];

    protected $appends = ['attachment_url', 'responsible_group_names'];

    // Accessor for the attachment URL
    public function getAttachmentUrlAttribute()
    {
        return $this->attachment ? asset('uploads/' . $this->attachment) : null;
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'responsible_group_ids', 'id', 'id');
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

    public function attachments()
    {
        return $this->hasMany(Attachment::class, 'ticket_id');
    }

    public function comments()
    {
        return $this->hasMany(TicketComment::class, 'ticket_id', 'id');
    }

    public function assignToGroup()
    {
        return $this->belongsTo(Group::class, 'assign_to_group_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'ticket_created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function lastUpdater()
    {
        return $this->belongsTo(User::class, 'last_updated_by');
    }

    public function callType()
    {
        return $this->belongsTo(NCCallType::class, 'call_type_id');
    }

    public function callCategory()
    {
        return $this->belongsTo(NCCallCategory::class, 'call_category_id');
    }

    public function callSubCategory()
    {
        return $this->belongsTo(NCCallSubCategory::class, 'call_sub_category_id');
    }

    public function ticketsRequiredFields()
    {
        return $this->hasMany(TicketsRequiredField::class, 'ticket_id');
    }

    public function NCTicketTimelines()
    {
        return $this->hasMany(NCTicketTimeline::class, 'ticket_id');
    }

}
