<?php

namespace Database\Seeders;

use App\Models\Back\Region;
use Illuminate\Database\Seeder;

class CompaniesRegionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Region::create(['name' => 'الرياض']);
        Region::create(['name' => 'جدة']);
        Region::create(['name' => 'الدمام']);
    }
}
