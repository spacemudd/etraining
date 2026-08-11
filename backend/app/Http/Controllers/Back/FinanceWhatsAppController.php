<?php

declare(strict_types=1);

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Back\Company;
use App\Models\Back\Invoice;
use App\Models\Back\Trainee;
use App\Models\Back\WhatsAppConversation;
use App\Models\Back\WhatsAppMessage;
use App\Services\TelnyxWhatsAppService;
use App\Support\WhatsAppBotPause;
use App\Support\WhatsAppBotStatus;
use App\Support\WhatsAppMessagingWindow;
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

    public function searchCompanies(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'search' => 'required|string|max:200',
            'page' => 'nullable|integer|min:1',
            'limit' => 'nullable|integer|min:1|max:50',
        ]);

        $search = $validated['search'];
        $page = (int) ($validated['page'] ?? 1);
        $limit = (int) ($validated['limit'] ?? 15);

        $query = Company::withoutGlobalScopes()
            ->where(function ($builder) use ($search) {
                $builder->where('name_ar', 'LIKE', '%' . $search . '%')
                    ->orWhere('name_en', 'LIKE', '%' . $search . '%');
            });

        $total = (clone $query)->count();
        $companies = $query
            ->orderBy('name_ar')
            ->skip(($page - 1) * $limit)
            ->take($limit)
            ->get(['id', 'name_ar', 'name_en']);

        $companyIds = $companies->pluck('id');
        $activeCounts = $this->activeTraineesWithPhoneQuery()
            ->whereIn('company_id', $companyIds)
            ->selectRaw('company_id, COUNT(*) as aggregate')
            ->groupBy('company_id')
            ->pluck('aggregate', 'company_id');

        return response()->json([
            'companies' => $companies->map(static fn (Company $company) => [
                'id' => $company->id,
                'name' => $company->name_ar ?: $company->name_en,
                'name_ar' => $company->name_ar,
                'name_en' => $company->name_en,
                'active_trainees_count' => (int) ($activeCounts[$company->id] ?? 0),
            ])->values(),
            'page' => $page,
            'limit' => $limit,
            'total' => $total,
            'has_more' => ($page * $limit) < $total,
        ]);
    }

    public function companyActiveTrainees(Request $request, string $company): JsonResponse
    {
        $companyModel = Company::withoutGlobalScopes()->findOrFail($company);

        $request->validate([
            'only_pending_invoices' => 'nullable|boolean',
        ]);
        $onlyPendingInvoices = $request->boolean('only_pending_invoices');

        $query = $this->activeTraineesWithPhoneQuery()
            ->where('company_id', $companyModel->id);
        $this->applyPendingInvoicesTraineeFilter($query, $onlyPendingInvoices);

        $trainees = $query
            ->orderBy('name')
            ->get(['id', 'name', 'phone', 'identity_number', 'company_id']);

        return response()->json([
            'company' => [
                'id' => $companyModel->id,
                'name' => $companyModel->name_ar ?: $companyModel->name_en,
            ],
            'only_pending_invoices' => $onlyPendingInvoices,
            'count' => $trainees->count(),
            'trainees' => $trainees->map(static fn (Trainee $trainee) => [
                'id' => $trainee->id,
                'name' => $trainee->name,
                'phone' => $trainee->phone,
                'identity_number' => $trainee->identity_number,
            ])->values(),
        ]);
    }

    public function pendingInvoices(Trainee $trainee): JsonResponse
    {
        $invoiceColumns = [
            'id',
            'number',
            'grand_total',
            'status',
            'from_date',
            'to_date',
            'paid_at',
            'created_at',
        ];

        $invoices = $trainee->invoices()
            ->notPaid()
            ->where('status', '!=', Invoice::STATUS_ARCHIVED)
            ->orderByDesc('from_date')
            ->get($invoiceColumns);

        $paidInvoices = $trainee->invoices()
            ->paid()
            ->orderByDesc('paid_at')
            ->limit(3)
            ->get($invoiceColumns);

        $mapInvoice = static fn (Invoice $invoice) => [
            'id' => $invoice->id,
            'number_formatted' => $invoice->number_formatted,
            'grand_total' => round((float) $invoice->grand_total, 2),
            'status' => $invoice->status,
            'status_formatted' => $invoice->status_formatted,
            'month_of' => $invoice->month_of,
            'paid_at' => optional($invoice->paid_at)->toDateString(),
            'show_url' => route('back.finance.invoices.show', $invoice->id),
        ];

        return response()->json([
            'invoices' => $invoices->map($mapInvoice)->values(),
            'paid_invoices' => $paidInvoices->map($mapInvoice)->values(),
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

        $normalizedPhone = $this->whatsAppService->normalizePhoneDigits($request->phone);
        $conversation = WhatsAppConversation::query()
            ->where('phone', $normalizedPhone)
            ->first();

        return response()->json([
            'messages' => $this->whatsAppService->listMessages(
                $request->phone,
                (int) ($request->limit ?? 50),
                $request->since
            ),
            'messaging_window' => $conversation
                ? WhatsAppMessagingWindow::forConversation($conversation)
                : [
                    'last_inbound_at' => null,
                    'expires_at' => null,
                    'remaining_seconds' => 0,
                    'is_open' => false,
                ],
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

    public function sendTemplateToCompany(Request $request): JsonResponse
    {
        $this->ensureConfigured();

        $validated = $request->validate([
            'company_id' => 'required|uuid',
            'content_sid' => 'required|string|max:64',
            'content_variables' => 'nullable|array',
            'content_variables.*' => 'nullable|string|max:1000',
            'only_pending_invoices' => 'nullable|boolean',
        ]);

        $company = Company::withoutGlobalScopes()->findOrFail($validated['company_id']);
        $onlyPendingInvoices = $request->boolean('only_pending_invoices');

        $query = $this->activeTraineesWithPhoneQuery()
            ->where('company_id', $company->id)
            ->with(['company' => static function ($companyQuery): void {
                $companyQuery->withoutGlobalScopes()->select(['id', 'name_ar', 'name_en']);
            }]);
        $this->applyPendingInvoicesTraineeFilter($query, $onlyPendingInvoices);

        $trainees = $query
            ->orderBy('name')
            ->get(['id', 'name', 'phone', 'company_id', 'english_name', 'identity_number']);

        if ($trainees->isEmpty()) {
            return response()->json([
                'message' => $onlyPendingInvoices
                    ? __('words.whatsapp-company-no-pending-invoice-trainees')
                    : __('words.whatsapp-company-no-active-trainees'),
            ], 422);
        }

        $sent = 0;
        $failed = [];

        foreach ($trainees as $trainee) {
            try {
                $this->whatsAppService->sendTemplate(
                    (string) $trainee->phone,
                    $validated['content_sid'],
                    $validated['content_variables'] ?? [],
                    (string) $trainee->id
                );
                $this->attachAgentAndPauseBot((string) $trainee->phone);
                $sent++;
            } catch (RuntimeException $exception) {
                $failed[] = [
                    'id' => $trainee->id,
                    'name' => $trainee->name,
                    'phone' => $trainee->phone,
                    'error' => $exception->getMessage(),
                ];
            }
        }

        return response()->json([
            'company' => [
                'id' => $company->id,
                'name' => $company->name_ar ?: $company->name_en,
            ],
            'total' => $trainees->count(),
            'sent' => $sent,
            'failed_count' => count($failed),
            'failed' => $failed,
            'message' => __('words.whatsapp-company-template-sent', [
                'sent' => $sent,
                'total' => $trainees->count(),
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

    /**
     * Active trainees: not soft-deleted, not suspended, and have a phone number.
     */
    private function activeTraineesWithPhoneQuery()
    {
        return Trainee::query()
            ->whereNull('suspended_at')
            ->whereNotNull('phone')
            ->where('phone', '!=', '');
    }

    private function applyPendingInvoicesTraineeFilter($query, bool $onlyPendingInvoices): void
    {
        if (! $onlyPendingInvoices) {
            return;
        }

        $query->whereHas('invoices', static function ($invoiceQuery): void {
            $invoiceQuery->whereNull('paid_at')
                ->where('status', '!=', Invoice::STATUS_ARCHIVED);
        });
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
