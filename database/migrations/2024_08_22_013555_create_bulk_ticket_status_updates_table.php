<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBulkTicketStatusUpdatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bulk_ticket_status_updates', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('ticket_id')->nullable();
            $table->string('transaction_id', 128)->nullable();
            $table->string('ticket_status', 128)->nullable();
            $table->text('comment')->nullable();
            $table->string('excel_file', 128);
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bulk_ticket_status_updates');
    }
}
