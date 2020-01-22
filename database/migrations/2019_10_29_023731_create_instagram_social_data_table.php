<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateInstagramSocialDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // todo: hack for phpunit to run, issue is that this collection is not dropped when phpunit test runs refresh database
        Schema::connection('mongodb')->drop('instagram_social_datas');

        Schema::connection('mongodb')->create('instagram_social_datas', function ($collection) {
            $collection->index('record_id');
            $collection->index('report_info.report_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mongodb')->drop('instagram_social_datas');
    }
}
