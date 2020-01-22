<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTwitterTweetsAndErToRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('records', function (Blueprint $table) {
            $table->double('twitter_tweets')->nullable()->unsigned()->after('twitter_followers');
            $table->double('twitter_engagement_rate')->nullable()->unsigned()->after('twitter_tweets');
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
            $table->dropColumn('twitter_tweets');
            $table->dropColumn('twitter_engagement_rate');
        });
    }
}
