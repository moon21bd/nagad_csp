<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $table = 'nc_ticket_attachments';

    protected $appends = ['path_url'];

    protected $fillable = [
        'ticket_id',
        'path',
        'created_by',
        'updated_by',
        'last_updated_by',
    ];

    public function getPathUrlAttribute()
    {
        return $this->path ? asset($this->path) : null;
    }

    public function ticket()
    {
        return $this->belongsTo(NCTicket::class, 'ticket_id');
    }
}
