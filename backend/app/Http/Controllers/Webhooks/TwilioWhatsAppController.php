<?php

declare(strict_types=1);

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\Back\WhatsAppMessage;
use App\Services\TwilioWebhookValidator;
use App\Services\TwilioWhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class TwilioWhatsAppController extends Controller
{
    public function __construct(
        private readonly TwilioWhatsAppService $whatsAppService,
        private readonly TwilioWebhookValidator $webhookValidator,
    ) {
    }

    public function incoming(Request $request): Response
    {
        if (! $this->webhookValidator->validate($request)) {
            Log::warning('Twilio WhatsApp webhook rejected: invalid signature', [
                'ip' => $request->ip(),
            ]);

            return response('Forbidden', 403);
        }

        $messageSid = (string) $request->input('MessageSid', '');
        $from = (string) $request->input('From', '');
        $to = (string) $request->input('To', '');
        $body = (string) $request->input('Body', '');

        if ($messageSid === '' || $from === '') {
            return $this->emptyTwimlResponse();
        }

        if (WhatsAppMessage::query()->where('twilio_sid', $messageSid)->exists()) {
            return $this->emptyTwimlResponse();
        }

        $numMedia = (int) $request->input('NumMedia', 0);
        $metadata = [];

        if ($numMedia > 0) {
            $metadata['media'] = [];

            for ($index = 0; $index < $numMedia; $index++) {
                $metadata['media'][] = [
                    'url' => $request->input('MediaUrl' . $index),
                    'content_type' => $request->input('MediaContentType' . $index),
                ];
            }
        }

        $this->whatsAppService->storeInboundMessage([
            'twilio_sid' => $messageSid,
            'from' => $from,
            'to' => $to,
            'body' => $body,
            'status' => (string) $request->input('SmsStatus', 'received'),
            'sent_at' => now(),
            'metadata' => $metadata !== [] ? $metadata : null,
        ]);

        Log::info('Twilio WhatsApp inbound message stored', [
            'message_sid' => $messageSid,
            'from' => $from,
        ]);

        return $this->emptyTwimlResponse();
    }

    public function status(Request $request): Response
    {
        if (! $this->webhookValidator->validate($request)) {
            Log::warning('Twilio WhatsApp status webhook rejected: invalid signature', [
                'ip' => $request->ip(),
            ]);

            return response('Forbidden', 403);
        }

        $messageSid = (string) $request->input('MessageSid', '');
        $messageStatus = (string) $request->input('MessageStatus', '');

        if ($messageSid !== '' && $messageStatus !== '') {
            $this->whatsAppService->updateMessageStatus(
                $messageSid,
                $messageStatus,
                (string) $request->input('ErrorMessage', '')
            );
        }

        return response('', 204);
    }

    private function emptyTwimlResponse(): Response
    {
        return response('<?xml version="1.0" encoding="UTF-8"?><Response></Response>', 200)
            ->header('Content-Type', 'text/xml');
    }
}
