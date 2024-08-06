<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToNcTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('nc_tickets', function (Blueprint $table) {
            $table->timestamp('sla_updated_at')->nullable()->after('sla_status');
            $table->integer('initial_assign_id')->nullable()->after('responsible_group_ids')->default(0);
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
            $table->dropColumn('sla_updated_at');
            $table->dropColumn('initial_assign_id');
        });
    }
}
