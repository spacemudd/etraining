<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeCompaniesEmailColumnToText extends Migration
{
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->text('email')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
        });
    }
}
