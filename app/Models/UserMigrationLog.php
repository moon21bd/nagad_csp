<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserMigrationLog extends Model
{
    protected $table = 'user_migration_logs';

    protected $fillable = [
        'user_id',
        'previous_group_id',
        'current_group_id',
        'previous_level',
        'previous_parent_id',
        'current_level',
        'current_parent_id',
        'total_ticket_created_till',
        'previous_roles_permissions',
        'updator_group_id',
        'updated_by',
        'updator_ip',
        'updator_device_name',
    ];

}
