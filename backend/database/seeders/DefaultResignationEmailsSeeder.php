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
        // Default CC emails for resignations
        AppSetting::updateOrCreate(
            ['name' => 'resignation_default_cc_emails'],
            ['value' => 'ceo@hadaf-hq.com, sara@hadaf-hq.com, M_SHEHATAH@hadaf-hq.com, mashael.a@hadaf-hq.com, eslam@hadaf-hq.com, mahmoud.h@hadaf-hq.com, halim@hadaf-hq.com']
        );

        // Default BCC emails for resignations
        AppSetting::updateOrCreate(
            ['name' => 'resignation_default_bcc_emails'],
            ['value' => 'trainee.affairs@hadaf-hq.com, cfo@hadaf-hq.com']
        );
    }
} 