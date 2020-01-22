<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAddressAndBankToRecords extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('records', function (Blueprint $table) {
            $table->text('address')->nullable()->after('city');
            $table->string('postal_code', 8)->nullable()->after('address');
            $table->string('bank_name', 240)->nullable()->after('wechat');
            $table->string('bank_code', 240)->nullable()->after('bank_name');
            $table->string('bank_account_number', 240)->nullable()->after('bank_code');
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
            $table->dropColumn('address');
            $table->dropColumn('postal_code');
            $table->dropColumn('bank_name');
            $table->dropColumn('bank_code');
            $table->dropColumn('bank_account_number');
        });
    }
}
