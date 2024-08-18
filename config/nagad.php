<?php
return [

    /*
     * Require Location Access for Groups
     */

    'requires_location_groups' => array_map('intval', explode(',', env('REQUIRES_LOCATION_GROUPS', '3,4'))),

];
