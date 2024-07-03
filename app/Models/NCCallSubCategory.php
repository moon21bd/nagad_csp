<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NCCallSubCategory extends Model
{
    use HasFactory;

    protected $table = 'nc_call_sub_categories';

    protected $fillable = [
        'call_type_id',
        'call_category_id',
        'call_sub_category_name',
        'status',
        'created_by',
        'updated_by',
        'last_updated_by',
    ];

    public function callType()
    {
        return $this->belongsTo(NCCallType::class, 'call_type_id');
    }

    public function callCategory()
    {
        return $this->belongsTo(NCCallCategory::class, 'call_category_id');
    }
}
