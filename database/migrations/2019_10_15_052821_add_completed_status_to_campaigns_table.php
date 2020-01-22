<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCompletedStatusToCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE campaigns MODIFY COLUMN status ENUM('Draft', 'Pending', 'Accepted', 'Rejected', 'Cancelled', 'Completed')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE campaigns MODIFY COLUMN status ENUM('Draft', 'Pending', 'Accepted', 'Rejected', 'Cancelled')");
    }
}
