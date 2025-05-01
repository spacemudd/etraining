<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReasonToGosiEmployeeDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('gosi_employee_data', function (Blueprint $table) {
            $table->boolean('reason_employment_office')->default(false);
            $table->boolean('reason_collection')->default(false);
            $table->boolean('reason_trainee_affairs')->default(false);
            $table->boolean('reason_sales')->default(false);
            $table->boolean('reason_other')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('gosi_employee_data', function (Blueprint $table) {
            $table->dropColumn([
                'reason_employment_office',
                'reason_collection',
                'reason_trainee_affairs',
                'reason_sales',
                'reason_other',
            ]);
        });
    }
}
