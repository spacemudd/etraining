<?php

declare(strict_types=1);

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Back\Invoice;
use App\Models\Back\Trainee;
use App\Models\Back\WhatsAppMessage;
use App\Services\TelnyxWhatsAppService;
use App\Support\WhatsAppBotPause;
use App\Support\WhatsAppBotStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

class FinanceWhatsAppController extends Controller
{
    public function __construct(private readonly TelnyxWhatsAppService $whatsAppService)
    {
        $this->middleware('can:access-whatsapp-chats');
    }

    public function status(): JsonResponse
    {
        return response()->json([
            'configured' => $this->whatsAppService->isConfigured(),
        ]);
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

    public function searchTrainees(Request $request): JsonResponse
    {
        $request->validate([
            'search' => 'required|string|max:200',
        ]);

        $trainees = Trainee::query()
            ->where(function ($query) use ($request) {
                $search = $request->search;
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

    public function pendingInvoices(Trainee $trainee): JsonResponse
    {
        $invoices = $trainee->invoices()
            ->notPaid()
            ->where('status', '!=', Invoice::STATUS_ARCHIVED)
            ->orderByDesc('from_date')
            ->get([
                'id',
                'number',
                'grand_total',
                'status',
                'from_date',
                'to_date',
                'created_at',
            ]);

        return response()->json([
            'invoices' => $invoices->map(static fn (Invoice $invoice) => [
                'id' => $invoice->id,
                'number_formatted' => $invoice->number_formatted,
                'grand_total' => round((float) $invoice->grand_total, 2),
                'status' => $invoice->status,
                'status_formatted' => $invoice->status_formatted,
                'month_of' => $invoice->month_of,
                'show_url' => route('back.finance.invoices.show', $invoice->id),
            ])->values(),
            'total_owed' => round((float) $invoices->sum('grand_total'), 2),
            'count' => $invoices->count(),
        ]);
    }

    public function messages(Request $request): JsonResponse
    {
        $this->ensureConfigured();

        $request->validate([
            'phone' => 'required|string|max:30',
            'since' => 'nullable|date',
            'limit' => 'nullable|integer|min:1|max:100',
        ]);

        return response()->json([
            'messages' => $this->whatsAppService->listMessages(
                $request->phone,
                (int) ($request->limit ?? 50),
                $request->since
            ),
        ]);
    }

    public function botStatus(Request $request): JsonResponse
    {
        $this->ensureConfigured();

        $validated = $request->validate([
            'phone' => 'required|string|max:30',
        ]);

        return response()->json(
            WhatsAppBotStatus::forPhone($validated['phone'])
        );
    }

    public function pauseBot(Request $request): JsonResponse
    {
        $this->ensureConfigured();

        $validated = $request->validate([
            'phone' => 'required|string|max:30',
            'minutes' => 'nullable|integer|min:1|max:1440',
        ]);

        $minutes = (int) ($validated['minutes'] ?? config('whatsapp.bot_pause_minutes', 30));
        WhatsAppBotPause::pauseForAgent($validated['phone'], $minutes);

        return response()->json([
            'message' => __('words.whatsapp-bot-paused'),
            'bot' => WhatsAppBotStatus::forPhone($validated['phone']),
        ]);
    }

    public function resumeBot(Request $request): JsonResponse
    {
        $this->ensureConfigured();

        $validated = $request->validate([
            'phone' => 'required|string|max:30',
        ]);

        WhatsAppBotPause::resume($validated['phone']);

        return response()->json([
            'message' => __('words.whatsapp-bot-resumed'),
            'bot' => WhatsAppBotStatus::forPhone($validated['phone']),
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
            $message = $this->whatsAppService->sendTemplate(
                $validated['phone'],
                $validated['content_sid'],
                $validated['content_variables'] ?? [],
                $validated['trainee_id'] ?? null
            );

            $this->attachAgentAndPauseBot($validated['phone']);
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'message' => $message,
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
            $message = $this->whatsAppService->sendFreeformMessage(
                $validated['phone'],
                $validated['body'],
                $validated['trainee_id'] ?? null
            );

            $this->attachAgentAndPauseBot($validated['phone']);
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'message' => $message,
        ]);
    }

    private function attachAgentAndPauseBot(string $phone): void
    {
        $normalized = $this->whatsAppService->normalizePhoneDigits($phone);

        $stored = WhatsAppMessage::query()
            ->where('phone', $normalized)
            ->where('direction', WhatsAppMessage::DIRECTION_OUTBOUND)
            ->orderByDesc('created_at')
            ->first();

        if ($stored && auth()->id()) {
            $metadata = is_array($stored->metadata) ? $stored->metadata : [];
            if (empty($metadata['is_bot'])) {
                $stored->update(['user_id' => auth()->id()]);
            }
        }

        WhatsAppBotPause::pauseForAgent($normalized);
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
