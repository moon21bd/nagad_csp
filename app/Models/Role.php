<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_role');
    }

}
