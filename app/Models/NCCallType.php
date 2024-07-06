<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NCCallType extends Model
{
    use HasFactory;

    protected $table = 'nc_call_types';

    protected $fillable = [
        'call_type_name',
        'status',
        'created_by',
        'updated_by',
        'last_updated_by'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function lastUpdater()
    {
        return $this->belongsTo(User::class, 'last_updated_by');
    }
}
