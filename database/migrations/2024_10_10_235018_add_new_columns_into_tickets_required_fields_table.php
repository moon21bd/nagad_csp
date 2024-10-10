<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnsIntoTicketsRequiredFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tickets_required_fields', function (Blueprint $table) {
            $table->integer('call_type_id')->after('ticket_id')->nullable();
            $table->integer('call_category_id')->after('call_type_id')->nullable();
            $table->integer('call_sub_category_id')->after('call_category_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tickets_required_fields', function (Blueprint $table) {
            $table->dropColumn(['call_type_id', 'call_category_id', 'call_sub_category_id']);
        });
    }
}
