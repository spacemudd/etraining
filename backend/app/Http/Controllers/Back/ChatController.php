<?php

declare(strict_types=1);

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Back\Trainee;
use App\Models\Back\WhatsAppMessage;
use App\Services\TelnyxWhatsAppService;
use App\Support\WhatsAppBroadcast;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use RuntimeException;

class ChatController extends Controller
{
    public function __construct(private readonly TelnyxWhatsAppService $whatsAppService)
    {
    }

    public function index(): Response
    {
        return Inertia::render('Back/Chat/Index', [
            'configured' => $this->whatsAppService->isConfigured(),
        ]);
    }

    public function conversations(): JsonResponse
    {
        // Get unique phones
        $phones = WhatsAppMessage::query()
            ->select('phone')
            ->distinct()
            ->pluck('phone');

        $conversations = [];

        foreach ($phones as $phone) {
            if (! $phone) {
                continue;
            }

            $lastMessage = WhatsAppMessage::query()
                ->where('phone', $phone)
                ->orderByDesc('sent_at')
                ->orderByDesc('created_at')
                ->with('user:id,name')
                ->first();

            if (! $lastMessage) {
                continue;
            }

            $trainee = $lastMessage->trainee_id
                ? Trainee::with('company:id,name_ar')->find($lastMessage->trainee_id)
                : $this->whatsAppService->findTraineeByPhone($phone);

            $conversations[$phone] = [
                'phone' => $phone,
                'trainee' => $trainee ? [
                    'id' => $trainee->id,
                    'name' => $trainee->name,
                    'phone' => $trainee->phone,
                    'identity_number' => $trainee->identity_number,
                    'company_name' => $trainee->company?->name_ar,
                    'show_url' => route('back.trainees.show', $trainee->id),
                ] : null,
                'last_message' => [
                    'body' => $lastMessage->body,
                    'direction' => $lastMessage->direction,
                    'is_note' => $lastMessage->is_note,
                    'sent_at' => $lastMessage->sent_at?->toIso8601String() ?? $lastMessage->created_at?->toIso8601String(),
                    'author_name' => $lastMessage->user?->name,
                ],
                'updated_at' => $lastMessage->sent_at?->timestamp ?? $lastMessage->created_at?->timestamp ?? 0,
            ];
        }

        $conversations = array_values($conversations);

        // Sort by most recent activity
        usort($conversations, fn ($a, $b) => $b['updated_at'] <=> $a['updated_at']);

        return response()->json([
            'conversations' => $conversations,
        ]);
    }

    public function messages(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => 'required|string|max:30',
        ]);

        $phone = $this->whatsAppService->normalizePhoneDigits($request->phone);

        $messages = WhatsAppMessage::query()
            ->where('phone', $phone)
            ->with(['user:id,name', 'media'])
            ->orderBy('sent_at')
            ->orderBy('created_at')
            ->get()
            ->map(function (WhatsAppMessage $msg) {
                $formatted = $this->whatsAppService->formatStoredMessage($msg);
                $formatted['id'] = $msg->id;
                $formatted['is_note'] = (bool) $msg->is_note;
                $formatted['author'] = $msg->user ? [
                    'id' => $msg->user->id,
                    'name' => $msg->user->name,
                ] : null;
                $formatted['saved_media'] = $msg->getMedia('whatsapp_media')->map(fn ($m) => [
                    'id' => $m->id,
                    'url' => $m->getUrl(),
                    'name' => $m->file_name,
                ]);
                return $formatted;
            });

        return response()->json([
            'messages' => $messages,
        ]);
    }

    public function searchTrainees(Request $request): JsonResponse
    {
        $request->validate([
            'search' => 'required|string|max:200',
        ]);

        $search = $request->search;

        $trainees = Trainee::query()
            ->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', '%' . $search . '%')
                    ->orWhere('phone', 'LIKE', '%' . $search . '%')
                    ->orWhere('identity_number', 'LIKE', '%' . $search . '%');
            })
            ->whereNotNull('phone')
            ->where('phone', '!=', '')
            ->with('company:id,name_ar')
            ->take(30)
            ->get(['id', 'name', 'phone', 'identity_number', 'company_id']);

        return response()->json([
            'trainees' => $trainees->map(fn (Trainee $trainee) => [
                'id' => $trainee->id,
                'name' => $trainee->name,
                'phone' => $trainee->phone,
                'identity_number' => $trainee->identity_number,
                'company_name' => $trainee->company?->name_ar,
                'show_url' => route('back.trainees.show', $trainee->id),
            ]),
        ]);
    }

    public function sendMessage(Request $request): JsonResponse
    {
        $this->ensureConfigured();

        $validated = $request->validate([
            'phone' => 'required|string|max:30',
            'body' => 'required|string|max:1600',
            'trainee_id' => 'nullable|uuid|exists:trainees,id',
        ]);

        try {
            $response = $this->whatsAppService->sendFreeformMessage(
                $validated['phone'],
                $validated['body'],
                $validated['trainee_id'] ?? null
            );

            // Update the stored message with the current authenticated user id
            $stored = WhatsAppMessage::query()
                ->where('phone', $this->whatsAppService->normalizePhoneDigits($validated['phone']))
                ->where('direction', WhatsAppMessage::DIRECTION_OUTBOUND)
                ->where('body', $validated['body'])
                ->orderByDesc('created_at')
                ->first();

            if ($stored) {
                $stored->update([
                    'user_id' => auth()->id(),
                ]);
            }

            $stored?->load('user:id,name');
            $formatted = $stored ? $this->whatsAppService->formatStoredMessage($stored) : $response;
            $formatted['is_note'] = false;
            $formatted['author'] = [
                'id' => auth()->id(),
                'name' => auth()->user()->name,
            ];

        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'message' => $formatted,
        ]);
    }

    public function sendTemplate(Request $request): JsonResponse
    {
        $this->ensureConfigured();

        $validated = $request->validate([
            'phone' => 'required|string|max:30',
            'content_sid' => 'required|string|max:64',
            'content_variables' => 'nullable|array',
            'content_variables.*' => 'nullable|string|max:1000',
            'trainee_id' => 'nullable|uuid|exists:trainees,id',
        ]);

        try {
            $response = $this->whatsAppService->sendTemplate(
                $validated['phone'],
                $validated['content_sid'],
                $validated['content_variables'] ?? [],
                $validated['trainee_id'] ?? null
            );

            $stored = WhatsAppMessage::query()
                ->where('phone', $this->whatsAppService->normalizePhoneDigits($validated['phone']))
                ->where('direction', WhatsAppMessage::DIRECTION_OUTBOUND)
                ->orderByDesc('created_at')
                ->first();

            if ($stored) {
                $stored->update([
                    'user_id' => auth()->id(),
                ]);
            }

            $stored?->load('user:id,name');
            $formatted = $stored ? $this->whatsAppService->formatStoredMessage($stored) : $response;
            $formatted['is_note'] = false;
            $formatted['author'] = [
                'id' => auth()->id(),
                'name' => auth()->user()->name,
            ];

        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'message' => $formatted,
        ]);
    }

    public function sendNote(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'phone' => 'required|string|max:30',
            'body' => 'required|string|max:1600',
            'trainee_id' => 'nullable|uuid|exists:trainees,id',
        ]);

        $normalizedPhone = $this->whatsAppService->normalizePhoneDigits($validated['phone']);
        $trainee = $validated['trainee_id']
            ? Trainee::query()->find($validated['trainee_id'])
            : $this->whatsAppService->findTraineeByPhone($normalizedPhone);

        $note = WhatsAppMessage::query()->create([
            'trainee_id' => $trainee?->id,
            'phone' => $normalizedPhone,
            'direction' => WhatsAppMessage::DIRECTION_OUTBOUND,
            'is_note' => true,
            'user_id' => auth()->id(),
            'body' => $validated['body'],
            'status' => 'internal_note',
            'sent_at' => now(),
        ]);

        $note->load('user:id,name');

        WhatsAppBroadcast::messageStored($note);

        return response()->json([
            'message' => [
                'sid' => $note->id,
                'direction' => WhatsAppMessage::DIRECTION_OUTBOUND,
                'is_note' => true,
                'body' => $note->body,
                'status' => 'internal_note',
                'date_sent' => $note->sent_at?->toIso8601String(),
                'author' => [
                    'id' => auth()->id(),
                    'name' => auth()->user()->name,
                ],
            ],
        ]);
    }

    public function saveToS3(Request $request, string $id): JsonResponse
    {
        $message = WhatsAppMessage::query()->findOrFail($id);

        $request->validate([
            'media_url' => 'required|url',
        ]);

        $mediaUrl = $request->input('media_url');

        try {
            $media = $message->addMediaFromUrl($mediaUrl)
                ->toMediaCollection('whatsapp_media', 's3');

            return response()->json([
                'success' => true,
                'message' => __('words.saved-to-s3'),
                'media_id' => $media->id,
                'url' => $media->getUrl(),
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Failed to save media to S3: ' . $exception->getMessage(),
            ], 422);
        }
    }

    public function templates(): JsonResponse
    {
        $this->ensureConfigured();

        return response()->json([
            'templates' => $this->whatsAppService->listTemplates(),
        ]);
    }

    public function showTemplate(string $contentSid): JsonResponse
    {
        $this->ensureConfigured();

        return response()->json([
            'template' => $this->whatsAppService->getTemplate($contentSid),
        ]);
    }

    private function ensureConfigured(): void
    {
        if (! $this->whatsAppService->isConfigured()) {
            abort(503, __('words.whatsapp-not-configured'));
        }
    }
}
