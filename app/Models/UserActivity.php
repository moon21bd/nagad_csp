<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserActivity extends Model
{
    use HasFactory;
    protected $table = 'user_activities';

    protected $fillable = [
        'user_id', 'creator_ip', 'updator_ip', 'creator_device', 'updator_device', 'last_password_change_time',
        'last_update_date', 'failed_logins', 'last_failed_login',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
