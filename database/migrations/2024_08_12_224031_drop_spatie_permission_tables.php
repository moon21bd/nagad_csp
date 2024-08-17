<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropSpatiePermissionTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('group_role')) {
            // Drop foreign keys referencing the 'roles' table
            Schema::table('group_role', function (Blueprint $table) {
                if (Schema::hasColumn('group_role', 'role_id')) {
                    $table->dropForeign(['role_id']); // Adjust 'role_id' if it's named differently
                }
            });
        }

        // Drop the permission tables
        Schema::dropIfExists('role_has_permissions');
        Schema::dropIfExists('model_has_permissions');
        Schema::dropIfExists('model_has_roles');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
