<?php

namespace Database\Seeders;

use App\Models\AppSetting;
use Illuminate\Database\Seeder;

class DefaultResignationEmailsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Defaults that used to live in CC are BCC (they were always sent as BCC historically).
        // Keep CC empty so new resignations only put intentional visible CCs there.
        AppSetting::updateOrCreate(
            ['name' => 'resignation_default_cc_emails'],
            ['value' => '']
        );

        AppSetting::updateOrCreate(
            ['name' => 'resignation_default_bcc_emails'],
            ['value' => 'trainee.affairs@hadaf-hq.com, cfo@hadaf-hq.com, mahmood.hasan@hadaf-hq.com, ceo@hadaf-hq.com, sara@hadaf-hq.com, mashael.a@hadaf-hq.com, afnan@hadaf-hq.com, mahmoud.h@hadaf-hq.com, mohammad.salah@hadaf-hq.com, mahmoud.azmy@hadaf-hq.com, abdulrahman@hadaf-hq.com']
        );
    }
} 