<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NCNotification extends Model
{
    use HasFactory;

    protected $table = "nc_notifications";
    protected $fillable = [
        'user_id',
        'group_id',
        'title',
        'link',
        'channel',
        'message',
        'is_seen',
        'is_read',
        'created_by',
        'updated_by',
        'last_updated_by',
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
