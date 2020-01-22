<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddXiaoHongShuColumnsToRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('records', function (Blueprint $table) {
            $table->string('xiaohongshu_id', 160)->nullable()->index()
                ->after('weibo_update_disabled_at');
            $table->string('xiaohongshu_photo', 160)->nullable()
                ->after('xiaohongshu_id');
            $table->integer('xiaohongshu_followers')->nullable()->unsigned()
                ->after('xiaohongshu_photo');
            $table->double('xiaohongshu_engagements')->nullable()->unsigned()
                ->after('xiaohongshu_followers');
            $table->double('xiaohongshu_engagement_rate')->nullable()->unsigned()
                ->after('xiaohongshu_engagements');
            $table->double('xiaohongshu_external_rate')->nullable()->unsigned()
                ->after('xiaohongshu_engagement_rate');
            $table->datetime('xiaohongshu_updated_at')->nullable()
                ->after('xiaohongshu_external_rate');
            $table->datetime('xiaohongshu_update_succeeded_at')->nullable()
                ->after('xiaohongshu_updated_at');
            $table->datetime('xiaohongshu_update_disabled_at')->nullable()
                ->after('xiaohongshu_update_succeeded_at');
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
            $table->dropColumn('xiaohongshu_id');
            $table->dropColumn('xiaohongshu_photo');
            $table->dropColumn('xiaohongshu_followers');
            $table->dropColumn('xiaohongshu_engagements');
            $table->dropColumn('xiaohongshu_engagement_rate');
            $table->dropColumn('xiaohongshu_external_rate');
            $table->dropColumn('xiaohongshu_updated_at');
            $table->dropColumn('xiaohongshu_update_succeeded_at');
            $table->dropColumn('xiaohongshu_external_rate');
            $table->dropColumn('xiaohongshu_update_disabled_at');
        });
    }
}
