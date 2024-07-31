<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsGroupLeadNotifiedColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('nc_service_type_configs', function (Blueprint $table) {
            $table->enum('is_group_lead_notified', ['yes', 'no'])->nullable()->default('no')->after('notification_channels');
            $table->enum('is_user_notified', ['yes', 'no'])->nullable()->default('no')->after('is_group_lead_notified');

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
            $table->dropColumn('is_group_lead_notified');
            $table->dropColumn('is_user_notified');
        });
    }
}
