<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('nc_call_sub_sub_categories', function (Blueprint $table) {
            $table->engine    = 'InnoDB';
            $table->charset   = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->id();
            $table->integer('call_type_id')->nullable();
            $table->integer('call_category_id')->nullable();
            $table->integer('call_sub_category_id')->nullable();
            $table->string('call_sub_sub_category_name',128)->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->string('created_by',128)->nullable();
            $table->string('updated_by',128)->nullable();
            $table->string('last_updated_by',128)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('call_sub_sub_categories');
    }
};
