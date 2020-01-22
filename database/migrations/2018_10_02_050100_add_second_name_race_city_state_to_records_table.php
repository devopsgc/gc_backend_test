<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSecondNameRaceCityStateToRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('records', function (Blueprint $table) {
            $table->string('second_name', 80)->nullable()->after('name');
            $table->string('race', 80)->nullable()->after('second_name');
            $table->string('state', 80)->nullable()->after('race');
            $table->string('city', 80)->nullable()->after('state');
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
            $table->dropColumn('second_name');
            $table->dropColumn('race');
            $table->dropColumn('state');
            $table->dropColumn('city');
        });
    }
}
