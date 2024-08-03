<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExtraFieldsToNcTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('nc_tickets', function (Blueprint $table) {
            $table->text('responsible_group_ids')->nullable()->after('group_id');
            $table->unsignedInteger('assign_to_user_id')->nullable()->after('assign_by_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('nc_tickets', function (Blueprint $table) {
            $table->dropColumn('responsible_group_ids');
            $table->dropColumn('assign_to_user_id');
        });
    }
}
