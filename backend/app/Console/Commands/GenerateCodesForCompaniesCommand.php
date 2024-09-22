<?php

namespace App\Console\Commands;

use App\Models\Back\Company;
use App\Models\Back\MaxNumber;
use App\Models\User;
use Illuminate\Console\Command;

class GenerateCodesForCompaniesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'companies:generate-code';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates new codes for companies';

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
        // loop through all companies and give them a code if they don't have one
        $user = User::where('email', 'info@ptc-ksa.net')->first();
        auth()->login($user);

        $companies = Company::all();
        foreach ($companies as $company) {
            if (!$company->code) {
                $company->code = 'C'.MaxNumber::generateForPrefix('C', 1000);
                $company->save();
                $this->info($company->code.','.$company->name_ar);
            }
        }
        return 1;
    }
}
