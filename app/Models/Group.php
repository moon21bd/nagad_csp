<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;
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

}
