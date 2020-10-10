<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinancialInvoiceLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('financial_invoice_lines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('team_id');
            $table->foreign('team_id')->references('id')->on('teams');
            $table->uuid('financial_invoice_id');
            $table->foreign('financial_invoice_id')->references('id')->on('financial_invoices')->onDelete('cascade');
            $table->string('description');
            $table->unsignedBigInteger('unit_price');
            $table->unsignedBigInteger('qty');
            $table->unsignedBigInteger('subtotal');
            $table->unsignedBigInteger('tax');
            $table->unsignedBigInteger('grand_total');
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
        Schema::dropIfExists('financial_invoice_lines');
    }
}
