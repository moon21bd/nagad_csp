<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NCGroupConfigAccessList extends Model
{
    use HasFactory;

    protected $table = "nc_group_config_access_list";
    protected $fillable = [
        'group_config_id', 'access_list_id'
    ];

}
