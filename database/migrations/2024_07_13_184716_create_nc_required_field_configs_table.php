<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNcRequiredFieldConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nc_required_field_configs', function (Blueprint $table) {
            $table->id();
            $table->integer('call_type_id')->nullable();
            $table->integer('call_category_id')->nullable();
            $table->integer('call_sub_category_id');
            $table->string('input_field_name');
            $table->string('input_type');
            $table->text('input_value')->nullable();
            $table->text('input_validation');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->string('created_by',128)->nullable();
            $table->string('updated_by',128)->nullable();
            $table->string('last_updated_by',128)->nullable();
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
        Schema::dropIfExists('nc_required_field_configs');
    }
}
