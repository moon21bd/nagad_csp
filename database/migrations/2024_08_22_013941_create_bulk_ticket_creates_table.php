<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBulkTicketCreatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bulk_ticket_creates', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('service_type_id')->nullable();
            $table->unsignedInteger('service_category_id')->nullable();
            $table->unsignedInteger('service_sub_category_id')->nullable();
            $table->string('mobile_no', 64)->nullable();
            $table->unsignedInteger('group_id')->nullable();
            $table->text('ticket_comment')->nullable();
            $table->string('ticket_status', 64)->nullable();
            $table->string('ticket_channel')->nullable();
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
        Schema::dropIfExists('bulk_ticket_creates');
    }
}
