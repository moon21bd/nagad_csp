<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdditionalColumnsToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('uuid')->nullable(false)->after('id');
            $table->unsignedInteger('level')->default(1)->after('group_id');
            $table->softDeletes()->after('updated_at');

            $table->unique('uuid');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'uuid')) {
                $table->dropUnique(['uuid']);
                $table->dropColumn('uuid');
            }

            if (Schema::hasColumn('users', 'level')) {
                $table->dropColumn('level');
            }

            if (Schema::hasColumn('deleted_at')) {
                $table->dropColumn('deleted_at');
            }
        });
    }
}
