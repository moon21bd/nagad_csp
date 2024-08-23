<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTicketUpdatedChannelToNcTicketTimelinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('nc_ticket_timelines', function (Blueprint $table) {
            $table->string('ticket_updated_channel')->after('ticket_status')->nullable()->default('SYSTEM');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('nc_ticket_timelines', function (Blueprint $table) {
            $table->dropColumn('ticket_updated_channel');
        });
    }
}
