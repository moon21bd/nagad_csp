<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueConstraintToNCServiceTypeConfigs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('nc_service_type_configs', function (Blueprint $table) {
            // create unique constraint
            $table->unique(['call_type_id', 'call_category_id', 'call_sub_category_id'], 'unique_wrapup_combination');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('nc_service_type_configs', function (Blueprint $table) {
            // Drop unique constraint
            $table->dropUnique('unique_wrapup_combination');
        });

    }
}
