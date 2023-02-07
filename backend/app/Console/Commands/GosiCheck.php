<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Dusk\Browser;
use Laravel\Dusk\Chrome\ChromeProcess;

class GosiCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gosi:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $ids = [1086532098, 1127301214];

        $process = (new ChromeProcess)->toProcess();
        $process->start();
        $driver = $this->driver();

        $browser = new Browser($driver);
        $browser = $browser->visit('https://gosi.gov.sa/ar/CheckEmploymentStatus');

        foreach ($ids as $id) {
             $text = $browser->waitFor('#CivilId', 30)
                ->typeSlowly('#CivilId', $id)
                ->pause(450)
                ->click('form.ng-dirty > div:nth-child(1) > div:nth-child(2) > div:nth-child(1) > a:nth-child(1)')
                ->pause(450)
                ->waitForText('الإشتراك الخاضع لانظمة التأمينات:', 30)
                ->text('app-checkemploymentstatus');

            // TODO: Add DB columns to track of GOSI status
            if (Str::contains($text, 'أنت على رأس العمل حالياً كمشترك في نظام التأمينات الاجتماعية ولديك مدد اشتراك سابقة')) {
                // TODO: Update status of GOSI registration
                // TODO: Send an email to PTC employees (with permission: receive-gosi-notifications)
                $this->info('GOSI: Registered');
            } else {
                // TODO: Update status of GOSI registration
                // TODO: Send an email to PTC employees if GOSI registration expired
                $this->info('GOSI: Not registered');
            }
        }

        $browser->quit();
        $process->stop();

        return 1;
    }

        /**
     * Create the RemoteWebDriver instance.
     *
     * @return \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    protected function driver()
    {
        $options = (new ChromeOptions)->addArguments([
            '--window-size=1920,1080',
        ]);
        $capabilities = DesiredCapabilities::chrome()->setCapability(ChromeOptions::CAPABILITY, $options);

        return retry(5, function () use($capabilities) {
            return RemoteWebDriver::create('http://localhost:9515', $capabilities);
        }, 50);
    }
}
