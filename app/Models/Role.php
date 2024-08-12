<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_role');
    }

}
