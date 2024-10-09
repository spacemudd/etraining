<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTraineeAgreementIdToTraineesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trainees', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('trainee_agreement_id')->nullable()->after('id');

            $table->foreign('trainee_agreement_id')->references('id')->on('trainee_agreements')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trainees', function (Blueprint $table) {
            //
            $table->dropForeign(['trainee_agreement_id']);
            $table->dropColumn('trainee_agreement_id');
        });
    }
}
