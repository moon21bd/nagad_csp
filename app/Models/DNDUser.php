<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DNDUser extends Model
{
    use HasFactory;

    protected $table = "dnd_users";

    protected $fillable = [
        'name',
        'mobile_no',
        'message',
        'status',
        'created_by',
        'updated_by',
        'last_updated_by',
    ];
}
