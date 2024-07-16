<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NCRequiredFieldConfig extends Model
{
    use HasFactory;

    protected $table = "nc_required_field_configs";
    protected $fillable = ['call_type_id', 'call_category_id', 'call_sub_category_id', 'input_field_name', 'input_type', 'input_validation', 'input_value'];

    public function callType()
    {
        return $this->belongsTo(NCCallType::class, 'call_type_id');
    }

    public function callCategory()
    {
        return $this->belongsTo(NCCallCategory::class, 'call_category_id');
    }

    public function callSubCategory()
    {
        return $this->belongsTo(NCCallSubCategory::class, 'call_sub_category_id');
    }
}
