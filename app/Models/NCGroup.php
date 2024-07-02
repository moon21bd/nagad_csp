<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NCGroup extends Model
{
    use HasFactory;
    protected $table = "nc_groups";

    protected $fillable = [
        'name',
        'role_id',
        'status',
        'created_by',
        'updated_by',
        'last_updated_by'
    ];

    protected $casts = [
        'name' => 'string',
        'status' => 'string'
    ];
}
