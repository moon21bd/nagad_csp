<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('login_device_name', 128)->nullable();
            $table->string('browser', 128)->nullable();
            $table->timestamp('last_online')->nullable();
            $table->timestamp('last_login')->nullable();
            $table->timestamp('last_logout')->nullable();
            $table->ipAddress('creator_ip')->nullable();
            $table->ipAddress('updator_ip')->nullable();
            $table->string('creator_device', 128)->nullable();
            $table->string('updator_device', 128)->nullable();
            $table->timestamp('last_password_change_time')->nullable();
            $table->timestamp('last_update_date')->nullable();

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
        Schema::dropIfExists('user_activities');
    }
}
