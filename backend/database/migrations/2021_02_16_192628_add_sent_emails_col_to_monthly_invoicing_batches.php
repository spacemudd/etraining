<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSentEmailsColToMonthlyInvoicingBatches extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('monthly_invoicing_batches', function (Blueprint $table) {
            $table->unsignedBigInteger('sent_emails')->nullable()->after('progress');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('monthly_invoicing_batches', function (Blueprint $table) {
            $table->dropColumn(['sent_emails']);
        });
    }
}
