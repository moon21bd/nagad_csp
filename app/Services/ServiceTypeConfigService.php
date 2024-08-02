<?php

namespace App\Services;

use App\Models\NCServiceTypeConfig;

class ServiceTypeConfigService
{
    public function getServiceTypeConfigs(int $callTypeId, int $callCategoryId, int $callSubCategoryId)
    {
        return NCServiceTypeConfig::where('call_type_id', $callTypeId)
            ->where('call_category_id', $callCategoryId)
            ->where('call_sub_category_id', $callSubCategoryId)
            ->firstOrFail();
    }
}
