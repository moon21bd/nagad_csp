<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordHistory extends Model
{

    protected $fillable = ['user_id', 'password'];

    // Define relationship with the User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
