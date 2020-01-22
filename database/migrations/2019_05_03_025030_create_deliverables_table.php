<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliverablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deliverables', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('campaign_id')->unsigned()->index();
            $table->integer('record_id')->unsigned()->index();
            $table->enum('platform', [
                'Facebook',
                'Instagram',
                'YouTube',
                'Twitter',
            ]);
            $table->enum('type', [
                'Post',
                'Video',
                'Story',
            ]);
            $table->double('price')->unsigned()->nullable();
            $table->string('url', 240)->nullable();
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
        Schema::dropIfExists('deliverables');
    }
}
