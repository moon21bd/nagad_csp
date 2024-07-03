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
        'status' => 'string'
    ];

    public function callType()
    {
        return $this->belongsTo(NCCallType::class, 'call_type_id');
    }
}
