<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('employee_id', 128)->nullable();
            $table->string('employee_name', 128)->nullable();
            $table->string('employee_user_id', 128)->nullable();
            $table->string('nid_card_no', 128)->nullable();
            $table->string('registered_channel', 64)->nullable();
            $table->enum('gender', ['Male', 'Female', 'Other']);
            $table->date('birth_date')->nullable();
            $table->text('address')->nullable();
            $table->string('lat', 128)->nullable();
            $table->string('lon', 128)->nullable();
            // $table->integer('created_by')->nullable();
            // $table->integer('updated_by')->nullable();
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
        Schema::dropIfExists('user_details');
    }
}
