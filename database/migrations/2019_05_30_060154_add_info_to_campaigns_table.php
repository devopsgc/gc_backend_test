<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInfoToCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->string('name', 240)->after('id');
            $table->string('brand', 240)->nullable()->after('name');
            $table->double('budget')->unsigned()->nullable()->after('brand');
            $table->string('categories', 240)->nullable()->after('budget');
            $table->text('description')->nullable()->after('categories');
            $table->datetime('start_at')->nullable()->after('description');
            $table->datetime('end_at')->nullable()->after('start_at');
            $table->integer('total_following')->unsigned()->nullable()->after('end_at');
            $table->double('engagement_rate')->unsigned()->nullable()->after('total_following');
            $table->enum('status', [
                'Pending',
                'Accepted',
                'Rejected',
            ])->after('engagement_rate');
            $table->softDeletes()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->dropColumn('brand');
            $table->dropColumn('budget');
            $table->dropColumn('categories');
            $table->dropColumn('description');
            $table->dropColumn('start_at');
            $table->dropColumn('end_at');
            $table->dropColumn('total_following');
            $table->dropColumn('engagement_rate');
            $table->dropColumn('status');
            $table->dropColumn('deleted_at');
        });
    }
}
