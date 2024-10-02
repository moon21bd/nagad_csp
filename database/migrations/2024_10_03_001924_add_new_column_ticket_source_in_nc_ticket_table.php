<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnTicketSourceInNcTicketTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('nc_tickets', function (Blueprint $table) {
            $table->string('ticket_source', 128)->after('ticket_status')->nullable();
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
            $table->dropColumn('ticket_source');
        });
    }
}
