<?php

namespace Database\Seeders;

use App\Models\Back\RequiredTraineesFiles;
use App\Models\Team;
use Illuminate\Database\Seeder;

class ApplicationRequirementsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (Team::get() as $team) {
            RequiredTraineesFiles::create([
                'team_id' => $team->id,
                'name_en' => 'ID photocopy',
                'name_ar' => 'صورة عن الهوية',
                'required' => true,
            ]);

            RequiredTraineesFiles::create([
                'team_id' => $team->id,
                'name_en' => 'Education level photocopy',
                'name_ar' => 'صورة عن المؤهل',
                'required' => true,
            ]);

            RequiredTraineesFiles::create([
                'team_id' => $team->id,
                'name_en' => 'Bank account information photocopy',
                'name_ar' => 'صورة عن حساب البنكي',
                'required' => true,
            ]);
        }
    }
}
