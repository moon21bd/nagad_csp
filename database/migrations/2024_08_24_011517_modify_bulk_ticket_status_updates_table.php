<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyBulkTicketStatusUpdatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bulk_ticket_status_updates', function (Blueprint $table) {
            // Drop the specified columns
            $table->dropColumn([
                'ticket_id',
                'transaction_id',
                'ticket_status',
                'comment',
            ]);

            $table->text('lot')->after('excel_file')->nullable();
            $table->text('response')->after('lot')->nullable();

        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bulk_ticket_creates', function (Blueprint $table) {
            $table->unsignedInteger('ticket_id')->nullable();
            $table->string('transaction_id', 128)->nullable();
            $table->string('ticket_status', 128)->nullable();
            $table->text('comment')->nullable();

            $table->dropColumn(['response', 'lot']);
        });

    }
}
