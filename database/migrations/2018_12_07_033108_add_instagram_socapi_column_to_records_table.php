<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInstagramSocapiColumnToRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('records', function (Blueprint $table) {
            $table->longText('instagram_socapi')->nullable()->after('instagram_update_disabled_at');
            $table->datetime('instagram_socapi_updated_at')->nullable()->after('instagram_socapi');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('records', function (Blueprint $table) {
            $table->dropColumn('instagram_socapi');
            $table->dropColumn('instagram_socapi_updated_at');
        });
    }
}
