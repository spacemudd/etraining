<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class TestMultipleBccEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:multiple-bcc 
                            {--emails= : BCC email addresses (comma separated)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test multiple BCC emails to verify they all receive the email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Testing Multiple BCC Emails...');
        $this->newLine();

        // الحصول على الايميلات
        $bccEmailsString = $this->option('emails') ?: $this->ask(
            'Enter BCC emails (comma separated)', 
            'email1@example.com,email2@example.com,email3@example.com'
        );

        // معالجة الايميلات
        $bccEmails = array_filter(array_map('trim', explode(',', $bccEmailsString)), function($email) {
            $email = trim($email);
            return !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL);
        });

        if (empty($bccEmails)) {
            $this->error('❌ No valid BCC emails provided!');
            return 1;
        }

        $this->info('📧 BCC Emails to test:');
        foreach ($bccEmails as $index => $email) {
            $this->line("   " . ($index + 1) . ". {$email}");
        }
        $this->newLine();

        if (!$this->confirm('Send test email to all these BCC addresses?', true)) {
            $this->info('❌ Test cancelled.');
            return 0;
        }

        // إرسال الإيميل
        $this->info('📤 Sending test email...');
        
        try {
            $testId = uniqid('bcc_test_');
            $timestamp = now()->format('Y-m-d H:i:s');

            // تسجيل تفاصيل الاختبار
            Log::info('Starting Multiple BCC Test', [
                'test_id' => $testId,
                'bcc_emails' => $bccEmails,
                'bcc_count' => count($bccEmails),
                'timestamp' => $timestamp
            ]);

            // إنشاء إيميل تجريبي
            $testMail = new class($testId, $timestamp, $bccEmails) {
                private $testId;
                private $timestamp;
                private $bccEmails;

                public function __construct($testId, $timestamp, $bccEmails)
                {
                    $this->testId = $testId;
                    $this->timestamp = $timestamp;
                    $this->bccEmails = $bccEmails;
                }

                public function build()
                {
                    return $this->subject('🧪 Multiple BCC Test - ' . $this->testId)
                               ->view('emails.test-multiple-bcc')
                               ->with([
                                   'test_id' => $this->testId,
                                   'timestamp' => $this->timestamp,
                                   'bcc_emails' => $this->bccEmails,
                                   'bcc_count' => count($this->bccEmails)
                               ]);
                }
            };

            // الطريقة الجديدة: استخدام مصفوفة واحدة للـ BCC
            $this->info('🔄 Using array method for BCC...');
            
            $mailInstance = Mail::to('test@example.com'); // TO مؤقت
            $mailInstance->bcc($bccEmails); // إرسال المصفوفة كاملة
            
            Log::info('About to send email with BCC array', [
                'test_id' => $testId,
                'bcc_emails_array' => $bccEmails,
                'method' => 'array'
            ]);

            $mailInstance->send($testMail);

            $this->info('✅ Test email sent successfully using array method!');
            
            // اختبار الطريقة القديمة للمقارنة
            $this->newLine();
            if ($this->confirm('Also test the old loop method for comparison?', false)) {
                $this->info('🔄 Using loop method for BCC (old way)...');
                
                $testId2 = uniqid('bcc_loop_test_');
                $testMail2 = new class($testId2, $timestamp, $bccEmails) {
                    private $testId;
                    private $timestamp;
                    private $bccEmails;

                    public function __construct($testId, $timestamp, $bccEmails)
                    {
                        $this->testId = $testId;
                        $this->timestamp = $timestamp;
                        $this->bccEmails = $bccEmails;
                    }

                    public function build()
                    {
                        return $this->subject('🧪 Loop BCC Test - ' . $this->testId)
                                   ->view('emails.test-multiple-bcc')
                                   ->with([
                                       'test_id' => $this->testId,
                                       'timestamp' => $this->timestamp,
                                       'bcc_emails' => $this->bccEmails,
                                       'bcc_count' => count($this->bccEmails),
                                       'method' => 'loop'
                                   ]);
                    }
                };

                $mailInstance2 = Mail::to('test@example.com');
                
                // الطريقة القديمة: loop
                foreach ($bccEmails as $bccEmail) {
                    $mailInstance2->bcc($bccEmail);
                }
                
                Log::info('About to send email with BCC loop', [
                    'test_id' => $testId2,
                    'bcc_emails_loop' => $bccEmails,
                    'method' => 'loop'
                ]);

                $mailInstance2->send($testMail2);
                $this->info('✅ Test email sent using loop method!');
            }

        } catch (\Exception $e) {
            $this->error('❌ Exception occurred: ' . $e->getMessage());
            Log::error('Multiple BCC Test Exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }

        $this->newLine();
        $this->info('🎉 Multiple BCC test completed!');
        $this->info('📝 Next steps:');
        $this->line('   1. Check ALL BCC email inboxes');
        $this->line('   2. Count how many emails were received');
        $this->line('   3. Check Mailgun logs for delivery confirmations');
        $this->line('   4. Monitor: tail -f storage/logs/laravel.log | grep -i "bcc"');

        return 0;
    }
}
