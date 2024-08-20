<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLoginActivity extends Model
{
    use HasFactory;
    protected $table = 'user_login_activities';

    protected $fillable = [
        'user_id',
        'group_id',
        'latitude',
        'longitude',
        'login_device_name',
        'browser',
        'ip_address',
        'last_online',
        'last_login',
        'last_logout',
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
