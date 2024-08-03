<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NCCallCategory extends Model
{
    use HasFactory;

    protected $table = 'nc_call_categories';

    protected $fillable = [
        'call_type_id',
        'call_category_name',
        'status',
        'created_by',
        'updated_by',
        'last_updated_by',
    ];

    protected $casts = [
        'call_type_id' => 'integer',
        'call_category_name' => 'string',
        'status' => 'string',
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

    public function callType()
    {
        return $this->belongsTo(NCCallType::class, 'call_type_id');
    }
    public function users()
    {
        return $this->hasMany(User::class, 'group_id');
    }
}
