<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BulkTicketStatusUpdate extends Model
{
    protected $table = "bulk_ticket_status_updates";

    protected $fillable = [
        'excel_file',
        'response',
        'lot',
        'created_by',
        'updated_by',
    ];

    protected $appends = ['excel_file_url', 'created_by_username'];

    public function getExcelFileUrlAttribute()
    {
        return $this->excel_file ? asset($this->excel_file) : null;
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getCreatedByUsernameAttribute()
    {
        return $this->creator ? $this->creator->name : 'Unknown User';
    }

}
