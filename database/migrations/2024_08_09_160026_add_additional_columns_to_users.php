<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

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
            $table->uuid('uuid')->nullable()->after('id');
            $table->unsignedInteger('level')->default(1)->after('group_id');
            $table->softDeletes()->after('updated_at');
        });

        \App\Models\User::whereNull('uuid')->each(function ($user) {
            $user->uuid = Str::uuid();
            $user->save();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->uuid('uuid')->nullable(false)->change();
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
            // Drop the unique index if it exists
            if (Schema::hasColumn('users', 'uuid')) {
                $indexes = Schema::getConnection()->getDoctrineSchemaManager()->listTableIndexes('users');
                foreach ($indexes as $index) {
                    if (in_array('uuid', $index->getColumns())) {
                        $table->dropUnique(['uuid']);
                        break;
                    }
                }
            }

            // Drop columns if they exist
            if (Schema::hasColumn('users', 'uuid')) {
                $table->dropColumn('uuid');
            }

            if (Schema::hasColumn('users', 'level')) {
                $table->dropColumn('level');
            }

            if (Schema::hasColumn('users', 'deleted_at')) {
                $table->dropColumn('deleted_at');
            }
        });
    }
}
