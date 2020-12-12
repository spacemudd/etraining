<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApprovedColsAndProvidedCoursesToInstructor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('instructors', function (Blueprint $table) {
            $table->string('provided_courses')->nullable();
            $table->boolean('is_approved')->default(FALSE);
            $table->uuid('approved_by')->nullable();
            $table->foreign('approved_by')->references('id')->on('users');
            $table->timestamp('approved_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('instructors', function (Blueprint $table) {
            $table->dropColumn('is_approved');
            $table->dropForeign('approved_by');
            $table->dropColumn('approved_by');
            $table->dropColumn('approved_at');
            $table->dropColumn('provided_courses');
        });
    }
}
