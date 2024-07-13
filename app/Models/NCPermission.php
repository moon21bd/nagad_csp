<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NCPermission extends Model
{
    use HasFactory;

    protected $table = "nc_permissions";
    protected $fillable = ['name', 'path'];


    public function group()
    {
        return $this->belongsTo(NCGroup::class, 'group_id');
    }

}
