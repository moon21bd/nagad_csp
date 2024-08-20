<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketsRequiredField extends Model
{
    protected $table = 'tickets_required_fields';

    protected $fillable = [
        'ticket_id',
        'required_field_id',
        'required_field_value',
        'created_by',
        'updated_by',
        'last_updated_by',
    ];

    public function ticket()
    {
        return $this->belongsTo(NCTicket::class, 'ticket_id');
    }

    public function requiredFields()
    {
        return $this->belongsTo(NCRequiredFieldConfig::class, 'required_field_id');
    }

}
