<?php
declare(strict_types=1);

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendEmailVerification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    protected string $userId;

    protected string $code;

    public function __construct(string $userId, string $code)
    {
        $this->userId = $userId;
        $this->code = $code;
    }

    public function backoff(): array
    {
        return [10, 30, 60];
    }

    public function handle(): void
    {
        $user = User::find($this->userId);
        if (! $user) {
            Log::warning('SendEmailVerification: user not found', [
                'user_id' => $this->userId,
            ]);

            return;
        }

        Mail::send('emails.verification-code', [
            'code' => $this->code,
        ], function ($message) use ($user) {
            $message->to($user->email)
                ->subject('رمز التحقق - تسجيل الدخول');
        });

        Log::info('SendEmailVerification: email sent', [
            'user_id' => $this->userId,
        ]);
    }
}
