<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPhoneIsOwnedColToTraineesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trainees', function (Blueprint $table) {
            // check if the phone number is owned by the ID number.
            $table->timestamp('phone_ownership_verified_at')->after('phone')->nullable();
            $table->boolean('phone_is_owned')->after('phone_ownership_verified_at')->nullable();
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
            $table->dropColumn(['phone_ownership_verified_at']);
            $table->dropColumn(['phone_is_owned']);
        });
    }
}
