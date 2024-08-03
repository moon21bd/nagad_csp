<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNcTicketTimelines extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nc_ticket_timelines', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('ticket_id');
            $table->text('responsible_group_ids')->nullable();
            $table->string('ticket_status');
            $table->text('ticket_comments')->nullable();
            $table->string('ticket_attachments', 128)->nullable();
            $table->unsignedInteger('ticket_opened_by')->nullable();
            $table->unsignedInteger('ticket_status_updated_by')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('last_time_opened_at')->nullable();
            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->integer('last_updated_by')->unsigned()->nullable();

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
        Schema::dropIfExists('nc_ticket_timelines');
    }
}
