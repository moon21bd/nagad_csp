<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CallCategory extends Model
{
    use HasFactory;
    protected $fillable = ['call_type_id','call_category_name','status ','created_by','updated_by','last_updated_by'];

}
