<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFixedTrainingCostsToTraineesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trainees', function (Blueprint $table) {
            $table->double('override_training_costs', 10, 2)->nullable();
            $table->boolean('ignore_attendance')->default(false);
            $table->boolean('dont_edit_notice')->default(false);
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
            $table->dropColumn(['override_training_costs', 'ignore_attendance', 'dont_edit_notice']);
        });
    }
}
