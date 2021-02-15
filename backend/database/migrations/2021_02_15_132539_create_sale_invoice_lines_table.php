<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleInvoiceLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_invoice_lines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('team_id');
            $table->foreign('team_id')->references('id')->on('teams')->cascadeOnDelete();
            $table->uuid('sale_invoice_id');
            $table->foreign('sale_invoice_id')->references('id')->on('sale_invoices')->cascadeOnDelete();
            $table->string('description');
            $table->integer('qty');
            $table->unsignedBigInteger('sub_total');
            $table->unsignedBigInteger('tax_total');
            $table->unsignedBigInteger('grand_total');
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
        Schema::dropIfExists('sale_invoice_lines');
    }
}
