<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeResignationEmailColumnsToText extends Migration
{
    public function up()
    {
        Schema::table('resignations', function (Blueprint $table) {
            $table->text('emails_to')->nullable()->change();
            $table->text('emails_cc')->nullable()->change();
            $table->text('emails_bcc')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('resignations', function (Blueprint $table) {
            $table->string('emails_to')->nullable()->change();
            $table->string('emails_cc')->nullable()->change();
            $table->string('emails_bcc')->nullable()->change();
        });
    }
}
