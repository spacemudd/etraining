<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRecruitmentCompanyIdToCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
            Schema::table('companies', function (Blueprint $table) {
            // $table->unsignedBigInteger('recruitment_company_id')->nullable();
            $table->uuid('recruitment_company_id')->nullable();
            $table->foreign('recruitment_company_id')->references('id')->on('recruitment_companies')->nullOnDelete();

            // $table->foreign('recruitment_company_id')->references('id')->on('recruitment_companies')->onDelete('set null');

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
        $table->dropColumn(['recruitment_company_id']);
        $table->dropForeign(['recruitment_company_id']);
    });
   }

}
