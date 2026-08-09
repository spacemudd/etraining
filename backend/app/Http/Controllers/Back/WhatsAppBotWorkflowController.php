<?php

declare(strict_types=1);

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Back\WhatsAppBotSender;
use App\Models\Back\WhatsAppBotWorkflow;
use App\Services\TelnyxWhatsAppService;
use App\Support\WhatsAppAiSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class WhatsAppBotWorkflowController extends Controller
{
    public function __construct(private readonly TelnyxWhatsAppService $whatsAppService)
    {
        $this->middleware('can:access-whatsapp-chats');
    }

    public function index(): Response
    {
        $this->ensureDefaultSender();

        return Inertia::render('Back/Settings/WhatsAppBots/Index', [
            'workflows' => WhatsAppBotWorkflow::query()
                ->with('sender:id,phone,label,workflow_id')
                ->orderBy('name')
                ->get()
                ->map(fn (WhatsAppBotWorkflow $workflow) => [
                    'id' => $workflow->id,
                    'name' => $workflow->name,
                    'is_active' => $workflow->is_active,
                    'sender' => $workflow->sender ? [
                        'id' => $workflow->sender->id,
                        'phone' => $workflow->sender->phone,
                        'label' => $workflow->sender->label,
                    ] : null,
                    'updated_at' => optional($workflow->updated_at)->toIso8601String(),
                ]),
            'senders' => WhatsAppBotSender::query()
                ->orderBy('label')
                ->orderBy('phone')
                ->get()
                ->map(fn (WhatsAppBotSender $sender) => [
                    'id' => $sender->id,
                    'phone' => $sender->phone,
                    'label' => $sender->label,
                    'workflow_id' => $sender->workflow_id,
                ]),
            'ai_settings' => WhatsAppAiSettings::forAdmin(),
        ]);
    }

    public function updateAiSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'enabled' => 'required|boolean',
            'openai_key' => 'nullable|string|max:500',
            'model' => 'required|string|max:120',
            'system_prompt' => 'required|string|max:10000',
            'purpose' => 'required|string|max:5000',
            'tone' => 'required|string|max:1000',
            'handoff_rules' => 'required|string|max:5000',
            'max_reply_chars' => 'required|integer|min:100|max:4000',
        ]);

        WhatsAppAiSettings::save([
            'enabled' => (bool) $validated['enabled'],
            'openai_key' => $validated['openai_key'] ?? null,
            'model' => $validated['model'],
            'system_prompt' => $validated['system_prompt'],
            'purpose' => $validated['purpose'],
            'tone' => $validated['tone'],
            'handoff_rules' => $validated['handoff_rules'],
            'max_reply_chars' => (int) $validated['max_reply_chars'],
        ]);

        return redirect()->back();
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:120',
        ]);

        $workflow = WhatsAppBotWorkflow::query()->create([
            'name' => $validated['name'],
            'is_active' => true,
            'graph' => $this->defaultGraph(),
        ]);

        return redirect()->route('back.settings.whatsapp-bots.edit', $workflow->id);
    }

    public function edit(string $id): Response
    {
        $workflow = WhatsAppBotWorkflow::query()->findOrFail($id);

        return Inertia::render('Back/Settings/WhatsAppBots/Edit', [
            'workflow' => [
                'id' => $workflow->id,
                'name' => $workflow->name,
                'is_active' => $workflow->is_active,
                'graph' => $workflow->graph ?? $this->defaultGraph(),
            ],
            'node_types' => [
                ['type' => 'start', 'label' => 'Start'],
                ['type' => 'send_message', 'label' => 'Send message'],
                ['type' => 'wait_input', 'label' => 'Wait for input'],
                ['type' => 'buttons', 'label' => 'Buttons'],
                ['type' => 'condition', 'label' => 'Condition'],
                ['type' => 'assign_agent', 'label' => 'Assign agent'],
                ['type' => 'end', 'label' => 'End'],
            ],
        ]);
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $workflow = WhatsAppBotWorkflow::query()->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'is_active' => 'required|boolean',
        ]);

        $workflow->update($validated);

        return redirect()->back();
    }

    public function updateGraph(Request $request, string $id): RedirectResponse
    {
        $workflow = WhatsAppBotWorkflow::query()->findOrFail($id);

        $validated = $request->validate([
            'graph' => 'required|array',
            'graph.nodes' => 'nullable|array',
            'graph.connections' => 'nullable|array',
            'graph.drawflow' => 'nullable|array',
        ]);

        $workflow->graph = $validated['graph'];
        $workflow->save();

        return redirect()->back();
    }

    public function destroy(string $id): RedirectResponse
    {
        $workflow = WhatsAppBotWorkflow::query()->findOrFail($id);
        WhatsAppBotSender::query()->where('workflow_id', $workflow->id)->update(['workflow_id' => null]);
        $workflow->delete();

        return redirect()->route('back.settings.whatsapp-bots.index');
    }

    public function assignSender(Request $request, string $senderId): RedirectResponse
    {
        $sender = WhatsAppBotSender::query()->findOrFail($senderId);

        $validated = $request->validate([
            'workflow_id' => 'nullable|uuid|exists:whatsapp_bot_workflows,id',
        ]);

        $workflowId = $validated['workflow_id'] ?? null;

        if ($workflowId) {
            // Enforce one sender per workflow.
            WhatsAppBotSender::query()
                ->where('workflow_id', $workflowId)
                ->where('id', '!=', $sender->id)
                ->update(['workflow_id' => null]);
        }

        $sender->workflow_id = $workflowId;
        $sender->save();

        return redirect()->back();
    }

    private function ensureDefaultSender(): void
    {
        $from = $this->whatsAppService->normalizePhoneDigits(
            (string) (config('telnyx.whatsapp_from') ?: config('twilio.whatsapp_from') ?: '')
        );

        if ($from === '') {
            return;
        }

        WhatsAppBotSender::query()->firstOrCreate(
            ['phone' => $from],
            ['label' => 'Primary WhatsApp']
        );
    }

    /**
     * @return array{nodes: array<string, array<string, mixed>>, connections: array<string, array<int, array<string, mixed>>>, drawflow: array<string, mixed>}
     */
    private function defaultGraph(): array
    {
        return [
            'nodes' => [
                '1' => [
                    'id' => '1',
                    'type' => 'start',
                    'data' => [],
                ],
                '2' => [
                    'id' => '2',
                    'type' => 'send_message',
                    'data' => [
                        'body' => 'Hello {{trainee_name}}! How can we help you?',
                    ],
                ],
                '3' => [
                    'id' => '3',
                    'type' => 'end',
                    'data' => [
                        'body' => '',
                    ],
                ],
            ],
            'connections' => [
                '1' => [
                    ['from_output' => 'output_1', 'to_node' => '2'],
                ],
                '2' => [
                    ['from_output' => 'output_1', 'to_node' => '3'],
                ],
            ],
            'drawflow' => [],
        ];
    }
}
