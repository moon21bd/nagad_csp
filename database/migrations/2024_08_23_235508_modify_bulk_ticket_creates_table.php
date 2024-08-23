<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyBulkTicketCreatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bulk_ticket_creates', function (Blueprint $table) {
            // Drop the specified columns
            $table->dropColumn([
                'service_type_id',
                'service_category_id',
                'service_sub_category_id',
                'mobile_no',
                'group_id',
                'ticket_comment',
                'ticket_status',
                'ticket_channel',
            ]);

            // Add the new 'response' column
            $table->text('response')->after('excel_file')->nullable();
            $table->text('lot')->after('response')->nullable();

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
            // Add the dropped columns back
            $table->unsignedInteger('service_type_id')->nullable();
            $table->unsignedInteger('service_category_id')->nullable();
            $table->unsignedInteger('service_sub_category_id')->nullable();
            $table->string('mobile_no', 64)->nullable();
            $table->unsignedInteger('group_id')->nullable();
            $table->text('ticket_comment')->nullable();
            $table->string('ticket_status', 64)->nullable();
            $table->string('ticket_channel')->nullable();

            // Drop the 'response', 'lot' columns
            $table->dropColumn(['response', 'lot']);
        });

    }
}
