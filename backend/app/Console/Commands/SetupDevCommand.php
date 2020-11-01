<?php

namespace App\Console\Commands;

use App\Actions\Fortify\CreateNewUser;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SetupDevCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:dev';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup dev environment';

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
        if ($this->ask('Do you want to run migrations?', true)) {
            Artisan::call('migrate');
        }

        if ($this->ask('Do you want to run seeders?', true)) {
            Artisan::call('db:seed');
        }

        if ($this->ask('Do you want to create an Admin account?', true)) {
            $email = $this->confirm('Choose an email address: [johndoe@example.com]', 'johndoe@example.com');
            $password = $this->confirm('Choose a password: [password]', 'password');
            (new CreateNewUser())->create([
                'name' => 'John Doe',
                'email' => $email,
                'password' => $password,
                'password_confirmation' => $password,
            ]);
        }

        return 1;
    }
}
