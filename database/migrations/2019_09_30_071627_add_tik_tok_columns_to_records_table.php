<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTikTokColumnsToRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('records', function (Blueprint $table) {
            $table->string('tiktok_id', 160)->nullable()->index()
                ->after('twitter_update_disabled_at');
            $table->string('tiktok_name', 160)->nullable()
                ->after('tiktok_id');
            $table->string('tiktok_photo', 160)->nullable()
                ->after('tiktok_name');
            $table->integer('tiktok_followers')->nullable()->unsigned()
                ->after('tiktok_photo');
            $table->double('tiktok_engagements')->nullable()->unsigned()
                ->after('tiktok_followers');
            $table->double('tiktok_engagement_rate_post')->nullable()->unsigned()
                ->after('tiktok_engagements');
            $table->double('tiktok_external_rate_post')->nullable()->unsigned()
                ->after('tiktok_engagement_rate_post');
            $table->datetime('tiktok_updated_at')->nullable()
                ->after('tiktok_external_rate_post');
            $table->datetime('tiktok_update_succeeded_at')->nullable()
                ->after('tiktok_updated_at');
            $table->datetime('tiktok_update_disabled_at')->nullable()
                ->after('tiktok_update_succeeded_at');
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
            $table->dropColumn('tiktok_id');
            $table->dropColumn('tiktok_name');
            $table->dropColumn('tiktok_photo');
            $table->dropColumn('tiktok_followers');
            $table->dropColumn('tiktok_engagements');
            $table->dropColumn('tiktok_engagement_rate_post');
            $table->dropColumn('tiktok_external_rate_post');
            $table->dropColumn('tiktok_updated_at');
            $table->dropColumn('tiktok_update_succeeded_at');
            $table->dropColumn('tiktok_update_disabled_at');
        });
    }
}
