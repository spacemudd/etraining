<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddZohoSignDateToTraineesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trainees', function (Blueprint $table) {
            //
            $table->timestamp('zoho_sign_date')->nullable()->after('zoho_contract_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trainees', function (Blueprint $table) {
            //
            $table->dropColumn('zoho_sign_date');
        });
    }
}
