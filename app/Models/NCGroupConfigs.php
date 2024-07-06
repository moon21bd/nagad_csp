<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NCGroupConfigs extends Model
{
    use HasFactory;

    protected $table = "nc_group_configs";
    protected $fillable = [
        'group_id', 'access_list_id', 'status', 'created_by', 'updated_by', 'last_updated_by'
    ];

    public function group()
    {
        return $this->belongsTo(NCGroup::class, 'group_id');
    }

    public function accessLists()
    {
        return $this->belongsToMany(NCAccessList::class, 'nc_group_config_access_list', 'group_config_id', 'access_list_id');
    }

}
