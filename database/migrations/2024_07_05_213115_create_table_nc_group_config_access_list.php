<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableNcGroupConfigAccessList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nc_group_config_access_list', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('group_config_id');
            $table->unsignedBigInteger('access_list_id');
            $table->timestamps();

            $table->foreign('group_config_id')->references('id')->on('nc_group_configs')->onDelete('cascade');
            $table->foreign('access_list_id')->references('id')->on('nc_access_lists')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nc_group_config_access_list');
    }
}
