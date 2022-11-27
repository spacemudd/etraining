<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $this->call(EducationalLevelsTableSeeder::class);
        $this->call(CitiesTableSeeder::class);
        $this->call(MaritalStatusesTableSeeder::class);
        $this->call(ApplicationRequirementsSeeder::class);
        $this->call(QuizzesTableSeeder::class);
        $this->call(AnswersTableSeeder::class);

        if(app()->environment(['local'])) {
            $this->call([
                UsersTableSeeder::class,
            ]);
        }
    }
}
