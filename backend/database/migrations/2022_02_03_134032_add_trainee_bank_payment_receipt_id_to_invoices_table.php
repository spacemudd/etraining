<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTraineeBankPaymentReceiptIdToInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->uuid('trainee_bank_payment_receipt_id')->nullable()->after('payment_reference_id');
            $table->foreign('trainee_bank_payment_receipt_id')->references('id')->on('trainee_bank_payment_receipts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (env('DB_CONNECTION') != 'sqlite') {
                $table->dropForeign(['trainee_bank_payment_receipt_id']);
            }
            $table->dropColumn(['trainee_bank_payment_receipt_id']);
        });
    }
}
