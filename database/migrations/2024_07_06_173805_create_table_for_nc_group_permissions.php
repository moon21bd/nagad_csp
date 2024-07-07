<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableForNcGroupPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nc_group_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('nc_groups')->onDelete('cascade');
            $table->foreignId('permission_id')->constrained('nc_permissions')->onDelete('cascade');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('last_updated_by')->nullable();
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
        Schema::table('nc_group_permissions', function (Blueprint $table) {
            Schema::dropIfExists('nc_group_permissions');
        });
    }
}
