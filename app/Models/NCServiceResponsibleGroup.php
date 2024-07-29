<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class NCServiceResponsibleGroup extends Model
{
    protected $table = 'nc_service_responsible_groups';
    protected $fillable = [
        'service_type_config_id',
        'call_type_id',
        'call_category_id',
        'call_sub_category_id',
        'group_id',
        'group_name',
        'tat_hours',
        'status',
        'created_by',
        'updated_by',
        'last_updated_by',
    ];

    /**
     * Get the validation rules for the model.
     *
     * @return array
     */
    /* public static function rules()
    {
    return [
    'service_type_config_id' => 'required|integer',
    'call_type_id' => 'required|integer',
    'call_category_id' => 'required|integer',
    'call_sub_category_id' => 'required|integer',
    'group_id' => 'required|integer',
    'group_name' => 'nullable|string|max:255',
    'tat_hours' => 'required|integer',
    'status' => 'nullable|in:active,inactive',
    'created_by' => 'nullable|integer',
    'updated_by' => 'nullable|integer',
    'last_updated_by' => 'nullable|integer',
    ];
    } */

    public function rules()
    {
        return [
            'callTypeId' => ['required', 'exists:nc_call_types,id'],
            'callCategoryId' => ['required', 'exists:nc_call_categories,id'],
            'callSubCategoryId' => ['required', 'exists:nc_call_sub_categories,id'],
            'groupId' => 'required|integer',
            Rule::unique('nc_service_responsible_groups')->where(function ($query) {
                return $query->where('call_type_id', $this->callTypeId)
                    ->where('call_category_id', $this->callCategoryId)
                    ->where('call_sub_category_id', $this->callSubCategoryId)
                    ->where('group_id', $this->groupId);
            }),
        ];
    }

    /**
     * Validate the model attributes.
     *
     * @param array $attributes
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public static function validate(array $attributes)
    {
        return Validator::make($attributes, self::rules());
    }
}
