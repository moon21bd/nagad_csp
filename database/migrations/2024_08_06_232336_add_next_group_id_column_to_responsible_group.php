<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNextGroupIdColumnToResponsibleGroup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('nc_service_responsible_groups', function (Blueprint $table) {
            $table->integer('next_group_id')->unsigned()->nullable()->after('group_id')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('nc_service_responsible_groups', function (Blueprint $table) {
            $table->dropColumn('next_group_id');
        });
    }
}
