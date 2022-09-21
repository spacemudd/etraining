<?php

use App\Models\Back\TraineesComplaint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTraineesComplaintsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trainees_complaints', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignIdFor(\App\Models\Back\Trainee::class)
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignIdFor(\App\Models\Back\Company::class)
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignUuid('created_by_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('number');
            $table->tinyInteger('complaints_status')->default(TraineesComplaint::COMPLAINTS_STATUS_NEW);
            $table->string('contact_way');
            $table->string('complaints');
            $table->string('reply')->nullable();
            $table->string('note')->nullable();
            $table->string('results')->nullable();
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
        Schema::dropIfExists('trainees_complaints');
    }
}
