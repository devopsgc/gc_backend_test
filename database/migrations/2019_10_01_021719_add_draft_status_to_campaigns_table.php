<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDraftStatusToCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE campaigns MODIFY COLUMN status ENUM('Draft', 'Pending', 'Accepted', 'Rejected', 'Cancelled')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE campaigns MODIFY COLUMN status ENUM('Pending', 'Accepted', 'Rejected', 'Cancelled')");
    }
}
