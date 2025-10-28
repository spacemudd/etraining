<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\Back\Trainee;

class SendSupportContactEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:send-support-contact';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send support contact information email to designated recipients';

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
        $this->info('Sending support contact email to all trainees...');
        
        // Get all active trainees with email addresses
        $trainees = Trainee::whereNotNull('email')
            ->where('email', '!=', '')
            ->whereNotNull('name')
            ->where('name', '!=', '')
            ->get();
        
        $this->info("Found {$trainees->count()} trainees with email addresses");
        
        if ($trainees->count() === 0) {
            $this->warn('No trainees found with email addresses!');
            return 0;
        }
        
        // Ask for confirmation
        if (!$this->confirm('Do you want to send email to all ' . $trainees->count() . ' trainees?', true)) {
            $this->info('Email sending cancelled.');
            return 0;
        }

        $subject = 'معلومات التواصل مع الدعم';
        
        $message = '
        <!DOCTYPE html>
        <html dir="ltr">
        <head>
            <meta charset="UTF-8">
            <style>
                body {
                    font-family: Arial, sans-serif;
                    direction: ltr;
                    padding: 20px;
                    background-color: #f5f5f5;
                }
                .container {
                    max-width: 600px;
                    margin: 0 auto;
                    background-color: #ffffff;
                    padding: 30px;
                    border-radius: 10px;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                }
                h1 {
                    color: #2c3e50;
                    text-align: center;
                    border-bottom: 3px solid #3498db;
                    padding-bottom: 15px;
                }
                .contact-info {
                    margin-top: 30px;
                }
                .contact-item {
                    background-color: #f8f9fa;
                    padding: 15px;
                    margin-bottom: 15px;
                    border-left: 4px solid #3498db;
                    border-radius: 5px;
                }
                .contact-label {
                    font-weight: bold;
                    color: #2c3e50;
                    font-size: 16px;
                    margin-bottom: 5px;
                    text-align: right;
                }
                .contact-value {
                    font-size: 18px;
                    color: #27ae60;
                    font-weight: bold;
                    text-align: right;
                }
                .footer {
                    margin-top: 30px;
                    text-align: center;
                    color: #7f8c8d;
                    font-size: 16px;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <h1>معلومات التواصل مع الدعم</h1>
                <p style="text-align: center; color: #7f8c8d; margin-bottom: 30px; font-size: 18px;">يرجى التواصل معنا عبر:</p>
                
                <div class="contact-info">
                    <div class="contact-item">
                        <div class="contact-label">الرقم الموحد للإتصال:</div>
                        <div class="contact-value">920031449</div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-label">واتساب:</div>
                        <div class="contact-value">0553139979</div>
                    </div>
                </div>
                
                <div class="footer">
                    <p>نحن هنا لمساعدتكم في أي وقت</p>
                </div>
            </div>
        </body>
        </html>
        ';

        try {
            $successCount = 0;
            $failCount = 0;
            
            foreach ($trainees as $trainee) {
                try {
                    Mail::send([], [], function ($mail) use ($trainee, $subject, $message) {
                        $mail->to($trainee->email)
                             ->subject($subject)
                             ->setBody($message, 'text/html');
                    });

                    $this->info("✓ Email sent successfully to: {$trainee->name} ({$trainee->email})");
                    Log::info("Support contact email sent to: {$trainee->name} ({$trainee->email})");
                    $successCount++;
                } catch (\Exception $emailException) {
                    $this->error("✗ Failed to send email to: {$trainee->name} ({$trainee->email}) - {$emailException->getMessage()}");
                    Log::error("Failed to send email to: {$trainee->name} ({$trainee->email})", [
                        'error' => $emailException->getMessage()
                    ]);
                    $failCount++;
                }
            }

            $this->newLine();
            $this->info("============================================");
            $this->info("Email sending summary:");
            $this->info("  Success: {$successCount} emails sent");
            $this->info("  Failed: {$failCount} emails");
            $this->info("  Total: " . ($successCount + $failCount) . " trainees");
            $this->info("============================================");
            
            return 0;
        } catch (\Exception $e) {
            $this->error('Failed to send email: ' . $e->getMessage());
            Log::error('Failed to send support contact email', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }
}
