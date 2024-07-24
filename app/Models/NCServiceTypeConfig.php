<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NCServiceTypeConfig extends Model
{
    use HasFactory;

    protected $table = 'nc_service_type_configs';
    protected $fillable = [
        'call_type_id',
        'call_category_id',
        'call_sub_category_id',
        'responsible_groups_with_tats',
        'required_fields_group_ids',
        'is_escalation',
        'is_show_attachment',
        'is_show_popup_msg',
        'popup_msg_texts',
        'notification_channel',
        'notification_config_id',
        'sms_config_id',
        'email_config_id',
        'is_verification_check',
        'customer_behavior_check',
        'bulk_ticket_close_perms',
        'status',
        'created_by',
        'updated_by',
        'last_updated_by',
    ];
}
