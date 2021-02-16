<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_invoices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('team_id');
            $table->foreign('team_id')->references('id')->on('teams')->cascadeOnDelete();
            $table->uuid('monthly_invoicing_batch_id')->nullable();
            $table->foreign('monthly_invoicing_batch_id')->references('id')->on('monthly_invoicing_batches')->nullOnDelete();
            $table->string('number')->nullable();
            $table->nullableUuidMorphs('billable');
            $table->tinyInteger('status');
            $table->timestamp('issued_at');
            $table->bigInteger('sub_total')->default(0);
            $table->bigInteger('tax_total')->default(0);
            $table->bigInteger('grand_total')->default(0);
            $table->timestamps();

            $table->unique(['team_id', 'number']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sale_invoices');
    }
}
