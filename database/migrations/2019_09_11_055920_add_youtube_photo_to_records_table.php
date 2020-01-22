<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddYoutubePhotoToRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('records', function (Blueprint $table) {
            $table->string('youtube_photo', 160)->nullable()->after('youtube_name');
            DB::statement("ALTER TABLE records MODIFY COLUMN photo_default ENUM('facebook', 'instagram', 'twitter', 'youtube', 'weibo', 'xiaohongshu')");
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
            $table->dropColumn('youtube_photo');
            DB::statement("ALTER TABLE records MODIFY COLUMN photo_default ENUM('facebook','instagram','weibo')");
        });
    }
}
