<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCenterIdToCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        Schema::create('companies', function (Blueprint $table) {
//            $table->increments('id');
//            $table->unsignedInteger('center_id');
//        });
//        Schema::table('companies', function ($table) {
//            $table
//                ->foreign('center_id')
//                ->references('id')
//                ->on('centers')
//                ->onDelete('cascade')
//                ->onUpdate('cascade');
//        });
        Schema::table('companies', function (Blueprint $table) {
            $table->unsignedInteger('center_id')->default(1);
            $table->foreign('center_id')->references('id')->on('centers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
//                $table->dropForeign(['center_id']);
//                $table->dropColumn(['center_id']);
        });
    }
}
