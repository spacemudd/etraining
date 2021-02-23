<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use MacsiDigital\Zoom\Facades\Zoom;

class ZoomCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'etraining:zoom';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Zoom cmds - for testing';

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
        dd(Zoom::user()->find('7Ud78mp_TkW-gyg5v66xoQ')->meetings()->all()->toArray());
        //$d = Zoom::user()->all()->toArray();
        //$d = Zoom::user()->create([
        //    'action' => 'create',
        //    'user_info' => [
        //        'email' => 'shafiqalshaar@clarastars.com',
        //        'type' => 1,
        //        'first_name' => 'Shafiq',
        //        'last_name' => 'Shaar',
        //        'password' => 'Shafiq@123123',
        //    ],
        //]);
        $d = Zoom::user()->find('GswH6Y2oSg-2La5Ym_rsTQ');
        dd($d);
        return 0;
    }
}
