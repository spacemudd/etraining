<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('team_id');
            $table->foreign('team_id')->references('id')->on('teams')->cascadeOnDelete();
            $table->uuid('instructor_id')->nullable();
            $table->foreign('instructor_id')->references('id')->on('instructors')->nullOnDelete();
            $table->string('name');
            $table->unsignedInteger('classroom_count')->nullable();
            $table->text('description')->nullable();
            $table->boolean('sharable')->default(false);
            $table->string('approval_code')->nullable()->comment('Approved by a regularity');
            $table->tinyInteger('days_duration')->nullable();
            $table->tinyInteger('hours_duration')->nullable();
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
        Schema::dropIfExists('courses');
    }
}
