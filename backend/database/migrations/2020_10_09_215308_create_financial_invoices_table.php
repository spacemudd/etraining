<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinancialInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('financial_invoices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('team_id');
            $table->foreign('team_id')->references('id')->on('teams');
            $table->uuid('financial_account_id');
            $table->foreign('financial_account_id')->references('id')->on('financial_accounts')->onDelete('cascade');
            $table->string('reference_number');
            $table->timestamp('issued_at')->nullable();
            $table->boolean('created_by_system')->default(true);
            $table->uuid('created_by_id')->nullable();
            $table->foreign('created_by_id')->references('id')->on('users');
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
        Schema::dropIfExists('financial_invoices');
    }
}
