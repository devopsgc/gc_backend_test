<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFbIgExternalRateStoryColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('records', function (Blueprint $table) {
            $table->double('facebook_external_rate_story')->nullable()->unsigned()->after('facebook_external_rate_video');
            $table->double('instagram_external_rate_story')->nullable()->unsigned()->after('instagram_external_rate_video');
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
            $table->dropColumn('facebook_external_rate_story');
            $table->dropColumn('instagram_external_rate_story');
        });
    }
}
