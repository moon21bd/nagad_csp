<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNCServiceTypeConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nc_service_type_configs', function (Blueprint $table) {
            $table->id();
            $table->integer('call_type_id')->unsigned()->nullable();
            $table->integer('call_category_id')->unsigned()->nullable();
            $table->integer('call_sub_category_id')->unsigned()->nullable();
            $table->json('responsible_groups_with_tats')->nullable();
            $table->text('required_fields_group_ids')->nullable(); // group concate will be applied here.
            $table->enum('is_escalation', ['yes', 'no'])->nullable();
            $table->enum('is_show_attachment', ['yes', 'no'])->nullable();
            $table->enum('is_show_popup_msg', ['yes', 'no'])->nullable();
            $table->text('popup_msg_texts')->nullable(); // can be multiple
            $table->enum('notification_channel', ['SMS', 'EMAIL', 'SOCKET']);

            $table->integer('notification_config_id')->unsigned()->nullable();
            $table->integer('sms_config_id')->unsigned()->nullable();
            $table->integer('email_config_id')->unsigned()->nullable();
            $table->enum('is_verification_check', ['yes', 'no'])->nullable();
            $table->enum('customer_behavior_check', ['yes', 'no'])->nullable();
            $table->enum('bulk_ticket_close_perms', ['yes', 'no'])->nullable();
            $table->enum('status', ['active', 'inactive'])->nullable();
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
        Schema::dropIfExists('nc_service_type_configs');
    }
}
