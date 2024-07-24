<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdditionalFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('parent_id')->after('group_id')->default(0);
            $table->string('mobile_no', 64)->after('parent_id')->nullable();
            $table->string('user_type', 64)->after('mobile_no')->default('Internal');
            $table->string('api_token', 128)->after('user_type')->nullable();
            $table->integer('created_by')->after('password')->default(null);
            $table->integer('updated_by')->after('created_by')->default(null);
            $table->enum('status', ['Active', 'Inactive', 'Blocked', 'Pending', 'Suspended', 'Deleted'])->default('Pending');

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
            $table->dropColumn('parent_id');
            $table->dropColumn('mobile_no');
            $table->dropColumn('user_type');
            $table->dropColumn('api_token');
            $table->dropColumn('created_by');
            $table->dropColumn('updated_by');
            $table->dropColumn('status');

        });
    }
}
