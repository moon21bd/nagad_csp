<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LaratrustSetupTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Update the groups table with new columns
        Schema::table('groups', function (Blueprint $table) {
            $table->string('display_name')->nullable()->after('name');
            $table->string('description')->nullable()->after('display_name');
        });

        // Create roles table
        Schema::create('roles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->unique();
            $table->string('display_name')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Create permissions table
        Schema::create('permissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->unique();
            $table->string('display_name')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Create role_user pivot table
        Schema::create('role_user', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('user_id');
            $table->string('user_type');
            $table->unsignedBigInteger('group_id')->nullable();

            $table->foreign('role_id')->references('id')->on('roles')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('group_id')->references('id')->on('groups')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->unique(['user_id', 'role_id', 'user_type', 'group_id']);
        });

        // Create role_group pivot table
        Schema::create('role_group', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('group_id');

            $table->foreign('role_id')->references('id')->on('roles')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('group_id')->references('id')->on('groups')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->unique(['role_id', 'group_id']);
        });

        // Create permission_user pivot table
        Schema::create('permission_user', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('user_id');
            $table->string('user_type');
            $table->unsignedBigInteger('group_id')->nullable();

            $table->foreign('permission_id')->references('id')->on('permissions')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('group_id')->references('id')->on('groups')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->unique(['user_id', 'permission_id', 'user_type', 'group_id']);
        });

        // Create permission_role pivot table
        Schema::create('permission_role', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('role_id');

            $table->foreign('permission_id')->references('id')->on('permissions')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->primary(['permission_id', 'role_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop foreign keys from role_user table
        if (Schema::hasTable('role_user')) {
            Schema::table('role_user', function (Blueprint $table) {
                $table->dropForeign(['group_id']);
                $table->dropForeign(['role_id']);
                $table->dropForeign('role_user_group_id_foreign');

                $table->dropUnique(['user_id', 'role_id', 'user_type', 'group_id']);
            });
        }

        // Drop foreign keys from role_group table
        Schema::table('role_group', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropForeign(['group_id']);
            $table->dropUnique(['role_id', 'group_id']);
        });

        // Drop foreign keys from permission_user table
        Schema::table('permission_user', function (Blueprint $table) {
            $table->dropForeign(['group_id']);
            $table->dropForeign(['permission_id']);
            $table->dropUnique(['user_id', 'permission_id', 'user_type', 'group_id']);
        });

        // Drop newly added columns from groups table
        Schema::table('groups', function (Blueprint $table) {
            $table->dropColumn(['display_name', 'description']);
        });

        // Drop pivot tables and main tables
        Schema::dropIfExists('permission_user');
        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('permissions');
        if (Schema::hasTable('role_user')) {
            Schema::dropIfExists('role_user');
        }

        Schema::dropIfExists('roles');
    }
}
