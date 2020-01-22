<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSocialMediaNameToRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('records', function (Blueprint $table) {
            $table->string('facebook_name', 80)->nullable()->after('facebook_id');
            $table->string('instagram_name', 80)->nullable()->after('instagram_id');
            $table->string('youtube_name', 80)->nullable()->after('youtube_id');
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
            $table->dropColumn('facebook_name');
            $table->dropColumn('instagram_name');
            $table->dropColumn('youtube_name');
        });
    }
}
