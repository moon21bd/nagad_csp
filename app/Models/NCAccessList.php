<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NCAccessList extends Model
{
    use HasFactory;

    protected $table = 'nc_access_lists';

    protected $fillable = [
        'access_name',
        'status',
        'created_by',
        'updated_by',
        'last_updated_by'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function lastUpdater()
    {
        return $this->belongsTo(User::class, 'last_updated_by');
    }

    public function groupConfigs()
    {
        return $this->belongsToMany(NCGroupConfigs::class, 'group_config_access_list', 'access_list_id', 'group_config_id');
    }
}
