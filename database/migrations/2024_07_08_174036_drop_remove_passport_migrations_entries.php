<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


class DropRemovePassportMigrationsEntries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('migrations', function (Blueprint $table) {
            DB::table('migrations')->where('migration', '2016_06_01_000001_create_oauth_auth_codes_table')->delete();
            DB::table('migrations')->where('migration', '2016_06_01_000002_create_oauth_access_tokens_table')->delete();
            DB::table('migrations')->where('migration', '2016_06_01_000003_create_oauth_refresh_tokens_table')->delete();
            DB::table('migrations')->where('migration', '2016_06_01_000004_create_oauth_clients_table')->delete();
            DB::table('migrations')->where('migration', '2016_06_01_000005_create_oauth_personal_access_clients_table')->delete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
    }
}
