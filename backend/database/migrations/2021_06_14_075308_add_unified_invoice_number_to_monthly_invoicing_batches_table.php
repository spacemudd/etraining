<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUnifiedInvoiceNumberToMonthlyInvoicingBatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('monthly_invoicing_batches', function (Blueprint $table) {
            $table->string('unified_invoice_number')->unique()->nullable();
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
            $table->dropColumn(['unified_invoice_number']);
        });
    }
}
