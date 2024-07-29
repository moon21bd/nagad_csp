<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class NCServiceTypeConfig extends Model
{
    use HasFactory;

    protected $table = 'nc_service_type_configs';
    protected $fillable = [
        'call_type_id',
        'call_category_id',
        'call_sub_category_id',
        'responsible_groups_with_tats',
        'required_fields_ids',
        'is_escalation',
        'is_show_attachment',
        'is_show_popup_msg',
        'popup_msg_texts',
        'notification_channels',
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

    public function rules()
    {
        return [
            'callTypeId' => ['required', 'exists:nc_call_types,id'],
            'callCategoryId' => ['required', 'exists:nc_call_categories,id'],
            'callSubCategoryId' => ['required', 'exists:nc_call_sub_categories,id'],
            Rule::unique('nc_service_type_configs')->where(function ($query) {
                return $query->where('call_type_id', $this->callTypeId)
                    ->where('call_category_id', $this->callCategoryId)
                    ->where('call_sub_category_id', $this->callSubCategoryId);
            }),
        ];
    }
    protected $casts = [
        'notification_channels' => 'array', // Cast to JSON
        'responsible_groups_with_tats' => 'array', // Cast to JSON
    ];

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
    public function responsibleGroup()
    {
        return $this->hasMany(NCServiceResponsibleGroup::class, 'service_type_config_id', 'id');
    }
}
