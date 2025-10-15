<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailHelper
{
    /**
     * معالجة وإرسال الايميلات بشكل صحيح مع دعم TO, CC, BCC
     * 
     * @param array $emailData - مصفوفة تحتوي على to, cc, bcc
     * @param mixed $mailClass - كلاس الإيميل المراد إرساله
     * @param string $logContext - سياق للـ logs
     * @return bool
     */
    public static function sendEmail(array $emailData, $mailClass, string $logContext = 'Email')
    {
        try {
            // معالجة TO emails
            $toEmails = self::processEmails($emailData['to'] ?? '');
            
            // معالجة CC emails
            $ccEmails = self::processEmails($emailData['cc'] ?? '');
            
            // معالجة BCC emails
            $bccEmails = self::processEmails($emailData['bcc'] ?? '');

            // التأكد من وجود مستلمين
            $totalRecipients = count($toEmails) + count($ccEmails) + count($bccEmails);
            
            if ($totalRecipients === 0) {
                Log::error("No recipients found for {$logContext}", [
                    'to_emails' => $toEmails,
                    'cc_emails' => $ccEmails,
                    'bcc_emails' => $bccEmails
                ]);
                return false;
            }

            // إنشاء instance الإيميل
            $mailInstance = null;
            
            if (!empty($toEmails)) {
                $mailInstance = Mail::to($toEmails);
            } else if (!empty($ccEmails)) {
                // إذا لم تكن هناك TO emails، استخدم أول CC كـ TO
                $mailInstance = Mail::to($ccEmails[0]);
                array_shift($ccEmails);
            } else if (!empty($bccEmails)) {
                // إذا لم تكن هناك TO أو CC emails، استخدم أول BCC كـ TO
                $mailInstance = Mail::to($bccEmails[0]);
                array_shift($bccEmails);
            }
            
            // إضافة CC emails
            if (!empty($ccEmails)) {
                foreach ($ccEmails as $ccEmail) {
                    $mailInstance->cc($ccEmail);
                }
            }
            
            // إضافة BCC emails
            if (!empty($bccEmails)) {
                foreach ($bccEmails as $bccEmail) {
                    $mailInstance->bcc($bccEmail);
                }
            }

            // تسجيل تفاصيل الإرسال
            Log::info("Sending {$logContext}", [
                'to_count' => count($toEmails),
                'cc_count' => count($ccEmails),
                'bcc_count' => count($bccEmails),
                'to_emails' => $toEmails,
                'cc_emails' => $ccEmails,
                'bcc_emails' => $bccEmails
            ]);

            $mailInstance->send($mailClass);
            
            Log::info("{$logContext} sent successfully");
            return true;
            
        } catch (\Exception $e) {
            Log::error("Exception in {$logContext}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * معالجة نص الايميلات وتحويله إلى مصفوفة صحيحة
     * 
     * @param string $emailString
     * @return array
     */
    public static function processEmails(string $emailString): array
    {
        if (empty($emailString)) {
            return [];
        }

        return array_filter(array_map('trim', explode(',', $emailString)), function($email) {
            $email = trim($email);
            return !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL);
        });
    }

    /**
     * معالجة مصفوفة الايميلات من Collection
     * 
     * @param \Illuminate\Support\Collection $emailCollection
     * @return array
     */
    public static function processEmailCollection($emailCollection): array
    {
        return $emailCollection->filter(function($email) {
            return !empty(trim($email)) && filter_var(trim($email), FILTER_VALIDATE_EMAIL);
        })->map(function($email) {
            return trim($email);
        })->toArray();
    }
}
