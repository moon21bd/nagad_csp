<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropAccessListIdColumnFromNcGroupConfigs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('nc_group_configs', function (Blueprint $table) {
            $table->dropColumn('access_list_id');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('nc_group_configs', function (Blueprint $table) {
            $table->unsignedBigInteger('access_list_id')->nullable();

        });
    }
}
