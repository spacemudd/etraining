<?php

declare(strict_types=1);

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Back\Trainee;
use App\Models\Back\WhatsAppConversation;
use App\Models\Back\WhatsAppMessage;
use App\Models\Back\WhatsAppTag;
use App\Services\TelnyxWhatsAppService;
use App\Support\WhatsAppBroadcast;
use App\Support\WhatsAppBotPause;
use App\Support\WhatsAppConversationSync;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use RuntimeException;

class ChatController extends Controller
{
    private const CONVERSATIONS_PER_PAGE = 7;

    private const MESSAGES_PER_PAGE = 50;

    public function __construct(private readonly TelnyxWhatsAppService $whatsAppService)
    {
        $this->middleware('can:access-whatsapp-chats');
    }

    public function index(): Response
    {
        return Inertia::render('Back/Chat/Index', [
            'configured' => $this->whatsAppService->isConfigured(),
            'canManageTemplates' => $this->whatsAppService->canManageTemplates(),
        ]);
    }

    public function conversations(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'page' => 'nullable|integer|min:1',
            'q' => 'nullable|string|max:200',
            'mine' => 'nullable|boolean',
            'unassigned' => 'nullable|boolean',
            'tag_id' => 'nullable|uuid|exists:whatsapp_tags,id',
            'agent_id' => 'nullable|uuid|exists:users,id',
            'status' => 'nullable|string|in:open,pending,closed',
        ]);

        $status = $validated['status'] ?? WhatsAppConversation::STATUS_OPEN;

        $query = WhatsAppConversation::query()
            ->with([
                'agents:id,name',
                'tags:id,name,color',
                'trainee:id,name,phone,identity_number,company_id',
                'trainee.company:id,name_ar',
            ])
            ->where('status', $status)
            ->orderByDesc('last_message_at')
            ->orderByDesc('updated_at');

        if (! empty($validated['q'])) {
            $search = $validated['q'];
            $query->where(function ($builder) use ($search) {
                $builder->where('phone', 'LIKE', '%' . $search . '%')
                    ->orWhereHas('trainee', function ($traineeQuery) use ($search) {
                        $traineeQuery->where('name', 'LIKE', '%' . $search . '%')
                            ->orWhere('phone', 'LIKE', '%' . $search . '%')
                            ->orWhere('identity_number', 'LIKE', '%' . $search . '%');
                    });
            });
        }

        if (! empty($validated['mine'])) {
            $query->whereHas('agents', function ($agentsQuery) {
                $agentsQuery->where('users.id', auth()->id());
            });
        }

        if (! empty($validated['unassigned'])) {
            $query->whereDoesntHave('agents');
        }

        if (! empty($validated['tag_id'])) {
            $query->whereHas('tags', function ($tagsQuery) use ($validated) {
                $tagsQuery->where('whatsapp_tags.id', $validated['tag_id']);
            });
        }

        if (! empty($validated['agent_id'])) {
            $query->whereHas('agents', function ($agentsQuery) use ($validated) {
                $agentsQuery->where('users.id', $validated['agent_id']);
            });
        }

        $paginator = $query->paginate(self::CONVERSATIONS_PER_PAGE);
        $authId = auth()->id();

        $paginator->getCollection()->transform(function (WhatsAppConversation $conversation) use ($authId) {
            return $this->formatConversation($conversation, $authId);
        });

        $payload = $paginator->toArray();
        $payload['counts'] = $this->conversationCounts();

        return response()->json($payload);
    }

    public function assignAgent(WhatsAppConversation $conversation): JsonResponse
    {
        $userId = auth()->id();

        if (! $conversation->agents()->where('users.id', $userId)->exists()) {
            $conversation->agents()->attach($userId, [
                'assigned_at' => now(),
            ]);
        }

        $conversation->load([
            'agents:id,name',
            'tags:id,name,color',
            'trainee.company:id,name_ar',
        ]);

        WhatsAppConversationSync::broadcast($conversation);

        return response()->json([
            'conversation' => $this->formatConversation($conversation, $userId),
        ]);
    }

    public function unassignAgent(WhatsAppConversation $conversation): JsonResponse
    {
        $userId = auth()->id();
        $conversation->agents()->detach($userId);

        $conversation->load([
            'agents:id,name',
            'tags:id,name,color',
            'trainee.company:id,name_ar',
        ]);

        WhatsAppConversationSync::broadcast($conversation);

        return response()->json([
            'conversation' => $this->formatConversation($conversation, $userId),
        ]);
    }

    public function updateStatus(Request $request, WhatsAppConversation $conversation): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|string|in:open,pending,closed',
        ]);

        $conversation->update([
            'status' => $validated['status'],
        ]);

        $conversation->load([
            'agents:id,name',
            'tags:id,name,color',
            'trainee.company:id,name_ar',
        ]);

        WhatsAppConversationSync::broadcast($conversation);

        return response()->json([
            'conversation' => $this->formatConversation($conversation, auth()->id()),
        ]);
    }

    public function tags(): JsonResponse
    {
        $tags = WhatsAppTag::query()
            ->orderBy('name')
            ->get(['id', 'name', 'color']);

        return response()->json([
            'tags' => $tags,
        ]);
    }

    public function storeTag(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:60',
            'color' => 'nullable|string|max:32',
        ]);

        $name = trim($validated['name']);

        $tag = WhatsAppTag::query()->firstOrCreate(
            ['name' => $name],
            ['color' => $validated['color'] ?? null]
        );

        return response()->json([
            'tag' => [
                'id' => $tag->id,
                'name' => $tag->name,
                'color' => $tag->color,
            ],
        ]);
    }

    public function attachTag(Request $request, WhatsAppConversation $conversation): JsonResponse
    {
        $validated = $request->validate([
            'tag_id' => 'nullable|uuid|exists:whatsapp_tags,id',
            'name' => 'nullable|string|max:60',
            'color' => 'nullable|string|max:32',
        ]);

        if (empty($validated['tag_id']) && empty($validated['name'])) {
            return response()->json([
                'message' => 'A tag id or name is required.',
            ], 422);
        }

        if (! empty($validated['tag_id'])) {
            $tag = WhatsAppTag::query()->findOrFail($validated['tag_id']);
        } else {
            $tag = WhatsAppTag::query()->firstOrCreate(
                ['name' => trim($validated['name'])],
                ['color' => $validated['color'] ?? null]
            );
        }

        $conversation->tags()->syncWithoutDetaching([$tag->id]);

        $conversation->load([
            'agents:id,name',
            'tags:id,name,color',
            'trainee.company:id,name_ar',
        ]);

        WhatsAppConversationSync::broadcast($conversation);

        return response()->json([
            'conversation' => $this->formatConversation($conversation, auth()->id()),
            'tag' => [
                'id' => $tag->id,
                'name' => $tag->name,
                'color' => $tag->color,
            ],
        ]);
    }

    public function detachTag(WhatsAppConversation $conversation, WhatsAppTag $tag): JsonResponse
    {
        $conversation->tags()->detach($tag->id);

        $conversation->load([
            'agents:id,name',
            'tags:id,name,color',
            'trainee.company:id,name_ar',
        ]);

        WhatsAppConversationSync::broadcast($conversation);

        return response()->json([
            'conversation' => $this->formatConversation($conversation, auth()->id()),
        ]);
    }

    public function messages(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'phone' => 'required|string|max:30',
            'limit' => 'nullable|integer|min:1|max:100',
            'before' => 'nullable|date',
            'before_id' => 'nullable|uuid',
        ]);

        $phone = $this->whatsAppService->normalizePhoneDigits($validated['phone']);
        $phoneDigits = preg_replace('/\D+/', '', $phone) ?? '';
        $limit = (int) ($validated['limit'] ?? self::MESSAGES_PER_PAGE);

        $query = WhatsAppMessage::query()
            ->where(function ($builder) use ($phone, $phoneDigits) {
                $builder->where('phone', $phone);
                if ($phoneDigits !== '' && $phoneDigits !== $phone) {
                    $builder->orWhere('phone', $phoneDigits);
                }
            })
            ->with(['user:id,name', 'media']);

        if (! empty($validated['before'])) {
            $before = $validated['before'];
            $beforeId = $validated['before_id'] ?? null;

            $query->where(function ($builder) use ($before, $beforeId) {
                $builder->where('sent_at', '<', $before);
                if ($beforeId) {
                    $builder->orWhere(function ($sameSecond) use ($before, $beforeId) {
                        $sameSecond->where('sent_at', '=', $before)
                            ->where('id', '<', $beforeId);
                    });
                }
            });
        }

        $messages = $query
            ->orderByDesc('sent_at')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit($limit + 1)
            ->get();

        $hasMore = $messages->count() > $limit;
        if ($hasMore) {
            $messages = $messages->take($limit);
        }

        $formatted = $messages
            ->reverse()
            ->values()
            ->map(fn (WhatsAppMessage $msg) => $this->formatMessage($msg));

        $oldest = $formatted->first();

        return response()->json([
            'messages' => $formatted,
            'has_more' => $hasMore,
            'next_before' => $oldest['date_sent'] ?? null,
            'next_before_id' => $oldest['id'] ?? null,
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

            WhatsAppBotPause::pauseForAgent($validated['phone']);

            $stored?->load('user:id,name');
            $formatted = $stored ? $this->formatMessage($stored) : $response;
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

            WhatsAppBotPause::pauseForAgent($validated['phone']);

            $stored?->load('user:id,name');
            $formatted = $stored ? $this->formatMessage($stored) : $response;
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
            'message' => $this->formatMessage($note),
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
            'can_manage' => $this->whatsAppService->canManageTemplates(),
            'available_auto_tags' => $this->whatsAppService->availableAutoTags(),
        ]);
    }

    public function showTemplate(string $contentSid): JsonResponse
    {
        $this->ensureConfigured();

        return response()->json([
            'template' => $this->whatsAppService->getTemplate($contentSid),
            'available_auto_tags' => $this->whatsAppService->availableAutoTags(),
        ]);
    }

    public function storeTemplate(Request $request): JsonResponse
    {
        $this->ensureCanManageTemplates();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:512', 'regex:/^[a-z0-9_]+$/'],
            'category' => 'required|string|in:UTILITY,MARKETING,AUTHENTICATION',
            'language' => 'required|string|max:20',
            'body' => 'required|string|max:1024',
            'header' => 'nullable|string|max:60',
            'footer' => 'nullable|string|max:60',
            'variable_samples' => 'nullable|array',
            'variable_samples.*' => 'nullable|string|max:200',
            'buttons' => 'nullable|array|max:3',
            'buttons.*.type' => 'required_with:buttons|string|in:QUICK_REPLY,URL,PHONE_NUMBER',
            'buttons.*.text' => 'required_with:buttons|string|max:25',
            'buttons.*.url' => 'nullable|string|max:2000',
            'buttons.*.phone_number' => 'nullable|string|max:30',
            'buttons.*.example' => 'nullable|string|max:200',
        ]);

        try {
            $template = $this->whatsAppService->createTemplate($validated);
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'template' => $template,
            'message' => __('words.whatsapp-template-created'),
        ], 201);
    }

    public function updateTemplate(Request $request, string $contentSid): JsonResponse
    {
        $this->ensureCanManageTemplates();

        $validated = $request->validate([
            'body' => 'required|string|max:1024',
            'header' => 'nullable|string|max:60',
            'footer' => 'nullable|string|max:60',
            'variable_samples' => 'nullable|array',
            'variable_samples.*' => 'nullable|string|max:200',
            'buttons' => 'nullable|array|max:3',
            'buttons.*.type' => 'required_with:buttons|string|in:QUICK_REPLY,URL,PHONE_NUMBER',
            'buttons.*.text' => 'required_with:buttons|string|max:25',
            'buttons.*.url' => 'nullable|string|max:2000',
            'buttons.*.phone_number' => 'nullable|string|max:30',
            'buttons.*.example' => 'nullable|string|max:200',
        ]);

        try {
            $template = $this->whatsAppService->updateTemplate($contentSid, $validated);
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'template' => $template,
            'message' => __('words.whatsapp-template-updated'),
        ]);
    }

    public function destroyTemplate(string $contentSid): JsonResponse
    {
        $this->ensureCanManageTemplates();

        try {
            $this->whatsAppService->deleteTemplate($contentSid);
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'message' => __('words.whatsapp-template-deleted'),
        ]);
    }

    /**
     * @return array{open: int, pending: int, closed: int, unassigned: int}
     */
    private function conversationCounts(): array
    {
        $byStatus = WhatsAppConversation::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return [
            'open' => (int) ($byStatus[WhatsAppConversation::STATUS_OPEN] ?? 0),
            'pending' => (int) ($byStatus[WhatsAppConversation::STATUS_PENDING] ?? 0),
            'closed' => (int) ($byStatus[WhatsAppConversation::STATUS_CLOSED] ?? 0),
            'unassigned' => (int) WhatsAppConversation::query()
                ->whereDoesntHave('agents')
                ->count(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function formatConversation(WhatsAppConversation $conversation, $authId = null): array
    {
        return [
            'id' => $conversation->id,
            'phone' => $conversation->phone,
            'status' => $conversation->status ?: WhatsAppConversation::STATUS_OPEN,
            'trainee' => $conversation->trainee ? [
                'id' => $conversation->trainee->id,
                'name' => $conversation->trainee->name,
                'phone' => $conversation->trainee->phone,
                'identity_number' => $conversation->trainee->identity_number,
                'company_name' => $conversation->trainee->company?->name_ar,
                'show_url' => route('back.trainees.show', $conversation->trainee->id),
            ] : null,
            'last_message' => [
                'body' => $conversation->last_message_body,
                'direction' => $conversation->last_message_direction,
                'is_note' => (bool) $conversation->last_message_is_note,
                'sent_at' => optional($conversation->last_message_at)->toIso8601String(),
            ],
            'updated_at' => optional($conversation->last_message_at)->timestamp
                ?? optional($conversation->updated_at)->timestamp
                ?? 0,
            'agents' => $conversation->agents->map(fn ($user) => [
                'id' => $user->id,
                'name' => $user->name,
            ])->values()->all(),
            'tags' => $conversation->tags->map(fn ($tag) => [
                'id' => $tag->id,
                'name' => $tag->name,
                'color' => $tag->color,
            ])->values()->all(),
            'is_assigned_to_me' => $authId
                ? $conversation->agents->contains('id', $authId)
                : false,
            'is_unassigned' => $conversation->agents->isEmpty(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function formatMessage(WhatsAppMessage $msg): array
    {
        $formatted = $this->whatsAppService->formatStoredMessage($msg);
        $formatted['id'] = $msg->id;
        $formatted['phone'] = $msg->phone;
        $formatted['is_note'] = (bool) $msg->is_note;
        $formatted['is_bot'] = ! empty($formatted['is_bot']);

        if ($formatted['is_bot']) {
            $formatted['author'] = [
                'id' => null,
                'name' => 'Bot',
                'is_bot' => true,
            ];
        } else {
            $formatted['author'] = $msg->user ? [
                'id' => $msg->user->id,
                'name' => $msg->user->name,
            ] : null;
        }

        $formatted['saved_media'] = $msg->getMedia('whatsapp_media')->map(fn ($m) => [
            'id' => $m->id,
            'url' => $m->getUrl(),
            'name' => $m->file_name,
        ]);

        return $formatted;
    }

    private function ensureConfigured(): void
    {
        if (! $this->whatsAppService->isConfigured()) {
            abort(503, __('words.whatsapp-not-configured'));
        }
    }

    private function ensureCanManageTemplates(): void
    {
        $this->ensureConfigured();

        if (! $this->whatsAppService->canManageTemplates()) {
            abort(503, __('words.whatsapp-templates-manage-not-configured'));
        }
    }
}
