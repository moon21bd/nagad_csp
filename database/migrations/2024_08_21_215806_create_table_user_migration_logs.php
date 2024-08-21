<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableUserMigrationLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_migration_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('previous_group_id');
            $table->unsignedInteger('current_group_id');
            $table->unsignedInteger('previous_level');
            $table->unsignedInteger('current_level');
            $table->unsignedInteger('previous_parent_id');
            $table->unsignedInteger('current_parent_id');
            $table->unsignedInteger('total_ticket_created_till');
            $table->json('previous_roles_permissions')->nullable();
            $table->unsignedInteger('updated_by');
            $table->unsignedInteger('updator_group_id')->nullable();
            $table->ipAddress('updator_ip')->nullable();
            $table->string('updator_device_name', 128)->nullable();
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
        Schema::dropIfExists('user_migration_logs');
    }
}
