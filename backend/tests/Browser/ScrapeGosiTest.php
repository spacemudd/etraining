<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ScrapeGosiTest extends DuskTestCase
{
    protected function hasHeadlessDisabled(): bool
    {
        return true;
    }


    public function testBasicExample()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('https://gosi.gov.sa/ar/CheckEmploymentStatus')
                    ->type('CivilId', 1086532098)
                    ->press('ارسال');
        });
    }
}
