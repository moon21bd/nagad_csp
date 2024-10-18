<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSessionStatus extends Model
{
    protected $table = 'user_session_status';

    protected $fillable = [
        'user_id', 'group_id', 'status', 'created_by', 'updated_by', 'ip_address',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
