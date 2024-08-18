<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    use HasFactory;

    protected $table = 'user_details';

    protected $fillable = [
        'user_id', 'employee_id', 'employee_name', 'employee_user_id', 'nid_card_no', 'registered_channel', 'gender', 'birth_date', 'address', 'last_update_date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
