<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Silber\Bouncer\Database\Ability;
use Silber\Bouncer\Database\HasRolesAndAbilities;

class Group extends Model
{
    use HasFactory, HasRolesAndAbilities;
    protected $table = "groups";

    protected $fillable = [
        'name',
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
        return $this->belongsToMany(Role::class, 'group_role');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
    public function abilities()
    {
        return $this->hasMany(Ability::class);
    }

}
