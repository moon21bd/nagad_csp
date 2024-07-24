<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNcTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nc_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('uuid', 128)->unique();
            $table->integer('call_type_id')->nullable();
            $table->integer('call_category_id')->nullable();
            $table->integer('call_sub_category_id')->nullable();
            $table->string('caller_mobile_no', 64)->nullable();
            $table->json('required_fields')->nullable();
            $table->integer('group_id')->nullable()->default(0);
            $table->integer('assign_to_group_id')->nullable()->default(0);
            $table->integer('assign_by_id')->nullable()->default(0);
            $table->tinyInteger('is_ticket_reassign')->nullable()->default(0);
            $table->text('comments')->nullable();
            $table->string('sla_status', 32)->nullable();
            $table->string('attachment', 128)->unique()->nullable();
            $table->integer('ticket_notification_status')->nullable()->default(0);
            $table->tinyInteger('is_customer_notified')->nullable()->default(0);
            $table->string('ticket_status', 32)->nullable();
            $table->string('ticket_channel', 32)->nullable()->default('WEB');
            $table->integer('ticket_created_by')->nullable();
            $table->integer('ticket_updated_by')->nullable();
            $table->integer('ticket_last_updated_by')->nullable()->default(0);
            $table->timestamp('ticket_created_at')->useCurrent();
            $table->timestamp('ticket_updated_at')->nullable();
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
        Schema::dropIfExists('nc_tickets');
    }
}
