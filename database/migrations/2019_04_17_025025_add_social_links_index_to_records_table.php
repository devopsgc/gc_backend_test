<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSocialLinksIndexToRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('records', function (Blueprint $table) {
            $table->index('email');
            $table->index('instagram_id');
            $table->index('facebook_id');
            $table->index('blog_url');
            $table->index('youtube_id');
            $table->index('twitter_id');
            $table->index('weibo_id');
            $table->index('miaopai_id');
            $table->index('yizhibo_id');
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
            $table->dropIndex('records_email_index');
            $table->dropIndex('records_instagram_id_index');
            $table->dropIndex('records_facebook_id_index');
            $table->dropIndex('records_blog_url_index');
            $table->dropIndex('records_youtube_id_index');
            $table->dropIndex('records_twitter_id_index');
            $table->dropIndex('records_weibo_id_index');
            $table->dropIndex('records_miaopai_id_index');
            $table->dropIndex('records_yizhibo_id_index');
        });
    }
}
