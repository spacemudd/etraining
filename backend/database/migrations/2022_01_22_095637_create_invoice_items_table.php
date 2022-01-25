<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignIdFor(\App\Models\Back\Invoice::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->string('name_en');
            $table->string('name_ar');
            $table->unsignedInteger('quantity')->default(1);
            $table->unsignedDecimal('tax', 10)->default(0);
            $table->unsignedInteger('amount');

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
        Schema::dropIfExists('invoice_items');
    }
}
