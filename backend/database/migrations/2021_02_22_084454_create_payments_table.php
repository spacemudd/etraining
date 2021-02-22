<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('team_id');
            $table->foreign('team_id')->references('id')->on('teams')->cascadeOnDelete();
            $table->uuid('sale_invoice_id');
            $table->foreign('sale_invoice_id')->references('id')->on('sale_invoices');
            $table->string('reference')->nullable()->unique();
            $table->string('sender_name', 50);
            $table->string('sender_bank', 50);
            $table->string('method');
            $table->unsignedBigInteger('amount');
            $table->tinyInteger('status');
            $table->timestamp('confirmed_at')->nullable();
            $table->string('confirmed_by', 25)->nullable();
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
        Schema::dropIfExists('payments');
    }
}
