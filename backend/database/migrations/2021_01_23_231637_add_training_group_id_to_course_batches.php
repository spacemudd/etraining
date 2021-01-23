<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTrainingGroupIdToCourseBatches extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('course_batches', 'trainee_group_id'))
        {
            Schema::table('course_batches', function (Blueprint $table)
            {
                $table->uuid('trainee_group_id');
                $table->foreign('trainee_group_id')->references('id')->on('trainee_groups')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('course_batches', function (Blueprint $table) {
            //
        });
    }
}
