<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('records', function (Blueprint $table) {
            $table->increments('id');

            $table->char('country_code', 2)->index();
            $table->string('name', 80)->nullable();
            $table->char('gender', 1)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->integer('age')->nullable();
            $table->text('description')->nullable();
            $table->text('description_ppt')->nullable();
            $table->string('email', 80)->nullable();
            $table->string('calling_code', 40)->nullable();
            $table->string('phone', 240)->nullable();
            $table->text('phone_remarks')->nullable();
            $table->string('line', 240)->nullable();
            $table->string('wechat', 240)->nullable();
            $table->text('verticals')->nullable();
            $table->text('campaigns')->nullable();
            $table->enum('photo_default', ['facebook', 'instagram', 'weibo'])->nullable();
            $table->string('photo', 160)->nullable();
            $table->text('affiliations')->nullable();

            $table->string('facebook_id', 160)->nullable();
            $table->string('facebook_photo', 160)->nullable();
            $table->integer('facebook_followers')->nullable()->unsigned();
            $table->double('facebook_engagement_rate_post')->nullable()->unsigned();
            $table->double('facebook_engagement_rate_video')->nullable()->unsigned();
            $table->double('facebook_external_rate_post')->nullable()->unsigned();
            $table->double('facebook_external_rate_video')->nullable()->unsigned();
            $table->datetime('facebook_user_page')->nullable();
            $table->datetime('facebook_updated_at')->nullable();
            $table->datetime('facebook_update_succeeded_at')->nullable();
            $table->datetime('facebook_update_disabled_at')->nullable();

            $table->string('instagram_id', 160)->nullable();
            $table->string('instagram_photo', 160)->nullable();
            $table->integer('instagram_followers')->nullable()->unsigned();
            $table->double('instagram_engagement_rate_post')->nullable()->unsigned();
            $table->double('instagram_engagement_rate_video')->nullable()->unsigned();
            $table->double('instagram_external_rate_post')->nullable()->unsigned();
            $table->double('instagram_external_rate_video')->nullable()->unsigned();
            $table->datetime('instagram_updated_at')->nullable();
            $table->datetime('instagram_update_succeeded_at')->nullable();
            $table->datetime('instagram_update_disabled_at')->nullable();

            $table->string('blog_url', 160)->nullable();
            $table->integer('blog_followers')->nullable()->unsigned();
            $table->double('blog_engagement_rate')->nullable()->unsigned();
            $table->double('blog_external_rate_post')->nullable()->unsigned();
            $table->datetime('blog_updated_at')->nullable();
            $table->datetime('blog_update_succeeded_at')->nullable();
            $table->datetime('blog_update_disabled_at')->nullable();

            $table->string('youtube_id', 160)->nullable();
            $table->integer('youtube_subscribers')->nullable()->unsigned();
            $table->bigInteger('youtube_views')->nullable()->unsigned();
            $table->double('youtube_view_rate')->nullable()->unsigned();
            $table->double('youtube_external_rate_video')->nullable()->unsigned();
            $table->datetime('youtube_updated_at')->nullable();
            $table->datetime('youtube_update_succeeded_at')->nullable();
            $table->datetime('youtube_update_disabled_at')->nullable();

            $table->string('weibo_id', 160)->nullable();
            $table->string('weibo_photo', 160)->nullable();
            $table->integer('weibo_followers')->nullable()->unsigned();
            $table->double('weibo_engagement_rate_post')->nullable()->unsigned();
            $table->double('weibo_engagement_rate_livestream')->nullable()->unsigned();
            $table->double('weibo_external_rate_post')->nullable()->unsigned();
            $table->double('weibo_external_rate_livestream')->nullable()->unsigned();
            $table->datetime('weibo_updated_at')->nullable();
            $table->datetime('weibo_update_succeeded_at')->nullable();
            $table->datetime('weibo_update_disabled_at')->nullable();

            $table->string('miaopai_id', 160)->nullable();
            $table->integer('miaopai_followers')->nullable()->unsigned();
            $table->double('miaopai_engagement_rate')->nullable()->unsigned();
            $table->double('miaopai_external_rate_livestream')->nullable()->unsigned();
            $table->datetime('miaopai_updated_at')->nullable();
            $table->datetime('miaopai_update_succeeded_at')->nullable();
            $table->datetime('miaopai_update_disabled_at')->nullable();

            $table->string('yizhibo_id', 160)->nullable();
            $table->integer('yizhibo_followers')->nullable()->unsigned();
            $table->double('yizhibo_engagement_rate')->nullable()->unsigned();
            $table->double('yizhibo_external_rate_livestream')->nullable()->unsigned();
            $table->datetime('yizhibo_updated_at')->nullable();
            $table->datetime('yizhibo_update_succeeded_at')->nullable();
            $table->datetime('yizhibo_update_disabled_at')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('records');
    }
}
