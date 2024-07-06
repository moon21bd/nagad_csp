<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NCCallSubSubCategory extends Model
{
    use HasFactory;

    protected $table = 'nc_call_sub_sub_categories';

    protected $fillable = [
        'call_type_id',
        'call_category_id',
        'call_sub_category_id',
        'call_sub_sub_category_name',
        'status',
        'created_by',
        'updated_by',
        'last_updated_by',
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
        return $this->belongsTo(NCCallSubCategory::class, 'call_category_id');
    }
}
