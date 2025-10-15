<?php

namespace App\Console\Commands;

use App\Helpers\EmailHelper;
use App\Mail\TestBccMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestBccEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:bcc-emails 
                            {--to= : TO email addresses (comma separated)}
                            {--cc= : CC email addresses (comma separated)} 
                            {--bcc= : BCC email addresses (comma separated)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test BCC email functionality with the new fixes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Testing BCC Email Functionality...');
        $this->newLine();

        // الحصول على البيانات من المستخدم أو الخيارات
        $toEmails = $this->option('to') ?: $this->ask('Enter TO emails (comma separated)', 'test@example.com');
        $ccEmails = $this->option('cc') ?: $this->ask('Enter CC emails (comma separated, optional)', '');
        $bccEmails = $this->option('bcc') ?: $this->ask('Enter BCC emails (comma separated)', 'bcc1@example.com,bcc2@example.com');

        $this->info('📧 Email Configuration:');
        $this->table(['Type', 'Emails'], [
            ['TO', $toEmails ?: 'None'],
            ['CC', $ccEmails ?: 'None'],
            ['BCC', $bccEmails ?: 'None'],
        ]);

        $this->newLine();

        // معالجة الايميلات باستخدام Helper
        $this->info('🔄 Processing emails...');
        
        $processedTo = EmailHelper::processEmails($toEmails);
        $processedCc = EmailHelper::processEmails($ccEmails);
        $processedBcc = EmailHelper::processEmails($bccEmails);

        $this->info('✅ Processed emails:');
        $this->table(['Type', 'Count', 'Valid Emails'], [
            ['TO', count($processedTo), implode(', ', $processedTo)],
            ['CC', count($processedCc), implode(', ', $processedCc)],
            ['BCC', count($processedBcc), implode(', ', $processedBcc)],
        ]);

        $totalRecipients = count($processedTo) + count($processedCc) + count($processedBcc);
        
        if ($totalRecipients === 0) {
            $this->error('❌ No valid email addresses found!');
            return 1;
        }

        $this->info("📊 Total recipients: {$totalRecipients}");
        $this->newLine();

        // تأكيد الإرسال
        if (!$this->confirm('Do you want to send the test email?', true)) {
            $this->info('❌ Test cancelled.');
            return 0;
        }

        // إرسال الإيميل
        $this->info('📤 Sending test email...');
        
        try {
            $emailData = [
                'to' => $toEmails,
                'cc' => $ccEmails,
                'bcc' => $bccEmails
            ];

            // إنشاء test mail class بسيط
            $testMail = new class {
                public function build()
                {
                    return $this->subject('🧪 BCC Email Test - ' . now())
                               ->view('emails.test-bcc')
                               ->with([
                                   'message' => 'This is a test email to verify BCC functionality.',
                                   'timestamp' => now()->format('Y-m-d H:i:s'),
                                   'test_id' => uniqid('test_')
                               ]);
                }
            };

            $result = EmailHelper::sendEmail($emailData, $testMail, 'BCC Test Email');

            if ($result) {
                $this->info('✅ Test email sent successfully!');
                $this->info('📋 Check the logs for detailed information:');
                $this->line('   tail -f storage/logs/laravel.log | grep -i "BCC Test Email"');
            } else {
                $this->error('❌ Failed to send test email. Check the logs for details.');
                return 1;
            }

        } catch (\Exception $e) {
            $this->error('❌ Exception occurred: ' . $e->getMessage());
            Log::error('BCC Test Email Exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }

        $this->newLine();
        $this->info('🎉 BCC Email test completed!');
        $this->info('📝 Next steps:');
        $this->line('   1. Check your email inboxes');
        $this->line('   2. Verify all BCC recipients received the email');
        $this->line('   3. Check storage/logs/laravel.log for detailed logs');

        return 0;
    }
}