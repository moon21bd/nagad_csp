<?php
return [

    /*
     * Require Location Access for Groups
     */

    'requires_location_groups' => array_map('intval', explode(',', env('REQUIRES_LOCATION_GROUPS', ','))),
    /*
     * User Roles Level
     */
    'SUPER_ADMIN' => 1,
    'ADMIN' => 2,
    'GROUP_OWNER' => 3,
    'USER' => 4,

];
