<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLoginFailedActivityIntoUserActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_activities', function (Blueprint $table) {
            $table->integer('failed_logins')->default(0)->after('last_logout');
            $table->timestamp('last_failed_login')->nullable()->after('failed_logins');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_activities', function (Blueprint $table) {
            $table->dropColumn('failed_logins');
            $table->dropColumn('last_failed_login');

        });

    }
}
