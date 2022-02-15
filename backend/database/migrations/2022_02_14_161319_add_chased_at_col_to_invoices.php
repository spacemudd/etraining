<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddChasedAtColToInvoices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->timestamp('chased_at')->nullable()->useCurrent();
            $table->uuid('chased_by_id')->nullable();
            $table->foreign('chased_by_id')->references('id')->on('users');
            $table->string('chased_note')->nullable();
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
            $table->dropColumn(['chased_at']);
            $table->dropColumn(['chased_note']);
        });
    }
}
