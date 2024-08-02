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
        'uuid', 'call_type_id', 'call_category_id', 'call_sub_category_id', 'caller_mobile_no', 'required_fields',
        'group_id', 'assign_to_group_id', 'assign_by_id', 'is_ticket_reassign', 'comments', 'sla_status',
        'attachment', 'ticket_notification_status', 'is_customer_notified', 'responsible_group_ids', 'assign_to_user_id', 'ticket_attachment', 'ticket_status', 'ticket_channel', 'ticket_created_by',
        'ticket_updated_by', 'ticket_last_updated_by', 'ticket_created_at', 'ticket_updated_at',
    ];
}
