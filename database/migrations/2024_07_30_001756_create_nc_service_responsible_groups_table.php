<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNCServiceResponsibleGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nc_service_responsible_groups', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('service_type_config_id');
            $table->unsignedInteger('call_type_id');
            $table->unsignedInteger('call_category_id');
            $table->unsignedInteger('call_sub_category_id');
            $table->unsignedInteger('group_id');
            $table->string('group_name')->nullable();
            $table->unsignedInteger('tat_hours');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('last_updated_by')->nullable();
            $table->timestamps();

            $table->unique(['call_type_id', 'call_category_id', 'call_sub_category_id', 'group_id'], 'unique_service_responsible_groups');

        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nc_service_responsible_groups');
    }
}
