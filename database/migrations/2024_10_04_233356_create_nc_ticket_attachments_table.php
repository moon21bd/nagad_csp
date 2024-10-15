<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNcTicketAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nc_ticket_attachments', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->unsignedBigInteger('ticket_id'); // Foreign key reference to tickets table, using the correct type
            $table->string('path', 128)->nullable(); // File path for the attachment
            $table->unsignedInteger('created_by')->nullable(); // User who created the attachment
            $table->unsignedInteger('updated_by')->nullable(); // User who updated the attachment
            $table->unsignedInteger('last_updated_by')->nullable(); // Last user who updated the attachment
            $table->timestamps();

            // Foreign key constraint with cascading delete
            $table->foreign('ticket_id')->references('id')->on('nc_tickets')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nc_ticket_attachments');
    }
}
