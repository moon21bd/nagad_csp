<?php

namespace App\Services;

use App\Models\Group;

class GroupService
{
    public function getGroupName($ids)
    {
        $groups = Group::whereIn('id', $ids)->pluck('name');
        return $groups->implode(', ');
    }
}
