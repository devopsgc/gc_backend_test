<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTwitterDetailsToRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('records', function (Blueprint $table) {
            $table->string('twitter_id', 160)->nullable()->after('youtube_update_disabled_at');
            $table->string('twitter_name', 80)->nullable()->after('twitter_id');
            $table->string('twitter_photo', 160)->nullable()->after('twitter_name');
            $table->integer('twitter_followers')->nullable()->unsigned()->after('twitter_photo');
            $table->datetime('twitter_updated_at')->nullable()->after('twitter_followers');
            $table->datetime('twitter_update_succeeded_at')->nullable()->after('twitter_updated_at');
            $table->datetime('twitter_update_disabled_at')->nullable()->after('twitter_update_succeeded_at');
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
            $table->dropColumn('twitter_id');
            $table->dropColumn('twitter_name');
            $table->dropColumn('twitter_photo');
            $table->dropColumn('twitter_followers');
            $table->dropColumn('twitter_updated_at');
            $table->dropColumn('twitter_update_succeeded_at');
            $table->dropColumn('twitter_update_disabled_at');
        });
    }
}
