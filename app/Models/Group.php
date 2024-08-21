<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laratrust\Models\LaratrustRole;
use Laratrust\Models\LaratrustTeam;
use Laratrust\Traits\LaratrustTeamTrait;

// use Illuminate\Database\Eloquent\Model;

class Group extends LaratrustTeam
{
    use HasFactory, LaratrustTeamTrait;

    public $guarded = [];

    protected $table = "groups";

    protected $fillable = [
        'owner_id',
        'name',
        'display_name',
        'description',
        'status',
        'created_by',
        'updated_by',
        'last_updated_by',
    ];

    protected $casts = [
        'name' => 'string',
        'status' => 'string',
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

    public function roles()
    {
        return $this->belongsToMany(LaratrustRole::class, 'role_group', 'group_id', 'role_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'group_id');
    }

}
