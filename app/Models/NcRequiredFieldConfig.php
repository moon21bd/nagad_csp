<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NcRequiredFieldConfig extends Model
{
    use HasFactory;
    protected $fillable = ['call_type_id','call_category_id','call_sub_category_id','input_field_name','input_type','input_validation','input_value'];
}
