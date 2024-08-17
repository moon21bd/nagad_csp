<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailConfig extends Model
{
    protected $table = "email_configs";

    protected $fillable = [
        'mailer',
        'host',
        'port',
        'username',
        'password',
        'encryption',
        'from_address',
        'from_name',
        'status',
        'created_by',
        'updated_by',
        'last_updated_by',
    ];
}
