<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTikTokToPhotoDefaultEnumColumnInRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('records', function (Blueprint $table) {
            DB::statement("ALTER TABLE records MODIFY COLUMN photo_default ENUM('instagram', 'youtube', 'facebook', 'twitter', 'tiktok', 'weibo', 'xiaohongshu')");
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
            DB::statement("ALTER TABLE records MODIFY COLUMN photo_default ENUM('facebook', 'instagram', 'twitter', 'youtube', 'weibo', 'xiaohongshu')");
        });
    }
}
