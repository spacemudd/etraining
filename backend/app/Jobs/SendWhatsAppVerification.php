<?php
declare(strict_types=1);

namespace App\Jobs;

use App\Models\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendWhatsAppVerification implements ShouldQueue
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
        if (!$user) {
            Log::warning('SendWhatsAppVerification: user not found', [
                'user_id' => $this->userId,
            ]);
            return;
        }

        $body = '{
          "messaging_product": "whatsapp",
          "recipient_type": "individual",
          "to": "'.$user->phone.'",
          "type": "template",
          "template": {
            "name": "laravel_otp",
            "language": {
                "code": "ar"
            },
            "components": [
              {
                "type": "body",
                "parameters": [
                  {
                    "type": "text",
                    "text": "'.$this->code.'"
                  }
                ]
              },
              {
                "type": "button",
                "sub_type": "url",
                "index": "0",
                "parameters": [
                  {
                    "type": "text",
                    "text": "'.$this->code.'"
                  }
                ]
              }
            ]
          }
        }';

        $client = new Client([
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . config('whatsapp.access_token'),
            ],
            'timeout' => 10,
        ]);

        try {
            $response = $client->post('https://graph.facebook.com/v15.0/106103318984035/messages', [
                'body' => $body,
            ]);

            Log::info('SendWhatsAppVerification: message sent', [
                'user_id' => $this->userId,
                'status_code' => $response->getStatusCode(),
            ]);
        } catch (GuzzleException $e) {
            Log::error('SendWhatsAppVerification: failed to send message', [
                'user_id' => $this->userId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}


