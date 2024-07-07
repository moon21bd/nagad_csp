<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NCGroup extends Model
{
    use HasFactory;
    protected $table = "nc_groups";

    protected $fillable = [
        'name',
        'status',
        'created_by',
        'updated_by',
        'last_updated_by'
    ];

    protected $casts = [
        'name' => 'string',
        'status' => 'string'
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

    public function users()
    {
        return $this->belongsToMany(User::class, 'nc_user_groups', 'group_id', 'user_id');
    }

    public function permissions()
    {
        return $this->belongsToMany(NCPermission::class, 'nc_group_permissions', 'group_id', 'permission_id');
    }
}
