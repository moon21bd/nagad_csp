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
}
