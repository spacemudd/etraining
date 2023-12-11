<?php

namespace App\Console\Commands;

use App\Mail\ResignationsMail;
use App\Mail\UnreceivedResignationMail;
use App\Models\Back\Resignation;
use Illuminate\Console\Command;
use Mail;

class CheckUnreceivedResignationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ptc:check-unreceived-emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check unreceived emails for resignations and send reminders';

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


        Resignation::where('created_at', '>', '2022-12-10')
            ->where('received_at', null)
            ->where('sent_at', '!=', null)
            ->chunk(100, function ($resignations) {

                // if 48 hour has passed since the last email was sent, send another one
                $resignations->each(function ($resignation) {

                    if (($resignation->sent_at->diffInHours(now()) >= 48 && $resignation->sent_tries === 0)) {
                        Mail::to($resignation->emails_to ? explode(', ', $resignation->emails_to) : null)
                            ->cc($resignation->emails_cc ? explode(', ', $resignation->emails_cc) : null)
                            ->bcc($resignation->emails_bcc ? explode(', ', $resignation->emails_bcc) : null)
                            ->send(new ResignationsMail($resignation));
                        $resignation->sent_tries = 1;
                        $resignation->sent_at = now();
                        $resignation->save();
                    }

                    if (($resignation->sent_at->diffInHours(now()) >= 48 && $resignation->sent_tries === 1)) {
                        Mail::to($resignation->emails_to ? explode(', ', $resignation->emails_to) : null)
                            ->cc($resignation->emails_cc ? explode(', ', $resignation->emails_cc) : null)
                            ->bcc($resignation->emails_bcc ? explode(', ', $resignation->emails_bcc) : null)
                            ->send(new ResignationsMail($resignation));
                        $resignation->sent_tries = 2;
                        $resignation->sent_at = now();
                        $resignation->save();
                    }

                    if (($resignation->sent_at->diffInHours(now()) >= 48 && $resignation->sent_tries === 2)) {
                        Mail::to(['trainees.affairs@ptc-ksa.net', 'billing@ptc-ksa.net'])
                            ->send(new UnreceivedResignationMail($resignation));
                        $resignation->sent_tries = 3;
                        $resignation->sent_at = now();
                        $resignation->save();
                    }
                });
            });
        return 1;
    }
}
