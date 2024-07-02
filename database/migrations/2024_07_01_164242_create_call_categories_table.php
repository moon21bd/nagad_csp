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
        Schema::create('call_categories', function (Blueprint $table) {
            $table->engine    = 'InnoDB';
            $table->charset   = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->id();
            $table->integer('call_type_id')->nullable();
            $table->string('call_category_name')->nullable();
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
        Schema::dropIfExists('call_categories');
    }
};
