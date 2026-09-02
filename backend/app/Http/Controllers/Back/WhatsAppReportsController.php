<?php

declare(strict_types=1);

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Back\Invoice;
use App\Models\Back\WhatsAppConversation;
use App\Models\Back\WhatsAppMessage;
use App\Models\User;
use App\Support\WhatsAppConversationHandoff;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class WhatsAppReportsController extends Controller
{
    private const FIRST_RESPONSE_PHONE_LIMIT = 500;

    public function __construct()
    {
        $this->middleware('can:view-whatsapp-reports');
    }

    public function index(Request $request): Response
    {
        $dateFrom = $this->resolveDate($request->input('date_from'), now()->subDays(29)->startOfDay());
        $dateTo = $this->resolveDate($request->input('date_to'), now()->endOfDay());

        if ($dateFrom->gt($dateTo)) {
            [$dateFrom, $dateTo] = [$dateTo->copy()->startOfDay(), $dateFrom->copy()->endOfDay()];
        } else {
            $dateFrom = $dateFrom->copy()->startOfDay();
            $dateTo = $dateTo->copy()->endOfDay();
        }

        return Inertia::render('Back/Chat/Reports', [
            'date_from' => $dateFrom->toDateString(),
            'date_to' => $dateTo->toDateString(),
            'queue' => $this->queueHealth(),
            'activity' => $this->activity($dateFrom, $dateTo),
            'chase' => $this->chaseOutcomes($dateFrom, $dateTo),
            'agents' => $this->agentPerformance($dateFrom, $dateTo),
        ]);
    }

    /**
     * @return array{open: int, pending: int, closed: int, unassigned: int, unread: int, need_human_agent: int}
     */
    private function queueHealth(): array
    {
        $byStatus = WhatsAppConversation::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $needHuman = (int) WhatsAppConversation::query()
            ->whereHas('tags', function ($query) {
                $query->where('whatsapp_tags.name', WhatsAppConversationHandoff::NEED_HUMAN_AGENT_TAG);
            })
            ->where(function ($builder) {
                $builder->where('status', WhatsAppConversation::STATUS_OPEN)
                    ->orWhereExists(function ($agents) {
                        $agents->selectRaw('1')
                            ->from('whatsapp_conversation_agents')
                            ->whereColumn('whatsapp_conversation_agents.conversation_id', 'whatsapp_conversations.id');
                    });
            })
            ->count();

        return [
            'open' => (int) ($byStatus[WhatsAppConversation::STATUS_OPEN] ?? 0),
            'pending' => (int) ($byStatus[WhatsAppConversation::STATUS_PENDING] ?? 0),
            'closed' => (int) ($byStatus[WhatsAppConversation::STATUS_CLOSED] ?? 0),
            'unassigned' => (int) WhatsAppConversation::query()
                ->whereDoesntHave('agents')
                ->count(),
            'unread' => (int) WhatsAppConversation::query()
                ->where('has_unread', true)
                ->count(),
            'need_human_agent' => $needHuman,
        ];
    }

    /**
     * @return array{inbound: int, outbound: int, bot_outbound: int, new_conversations: int, with_unpaid_invoices: int}
     */
    private function activity(Carbon $from, Carbon $to): array
    {
        $inbound = (int) WhatsAppMessage::query()
            ->where('direction', WhatsAppMessage::DIRECTION_INBOUND)
            ->where('is_note', false)
            ->whereBetween('sent_at', [$from, $to])
            ->count();

        $outboundHuman = (int) WhatsAppMessage::query()
            ->where('direction', WhatsAppMessage::DIRECTION_OUTBOUND)
            ->where('is_note', false)
            ->whereNotNull('user_id')
            ->whereBetween('sent_at', [$from, $to])
            ->count();

        $botOutbound = (int) WhatsAppMessage::query()
            ->where('direction', WhatsAppMessage::DIRECTION_OUTBOUND)
            ->where('is_note', false)
            ->whereBetween('sent_at', [$from, $to])
            ->where(function ($query) {
                $query->where('metadata->is_bot', true)
                    ->orWhere('metadata->is_bot', 1)
                    ->orWhere('metadata->is_bot', 'true');
            })
            ->count();

        $newConversations = (int) WhatsAppConversation::query()
            ->whereBetween('created_at', [$from, $to])
            ->count();

        $withUnpaid = (int) WhatsAppConversation::query()
            ->whereNotNull('trainee_id')
            ->whereExists(function ($query) {
                $query->selectRaw('1')
                    ->from('invoices')
                    ->whereColumn('invoices.trainee_id', 'whatsapp_conversations.trainee_id')
                    ->whereNull('paid_at')
                    ->where('status', '!=', Invoice::STATUS_ARCHIVED);
            })
            ->count();

        return [
            'inbound' => $inbound,
            'outbound' => $outboundHuman,
            'bot_outbound' => $botOutbound,
            'new_conversations' => $newConversations,
            'with_unpaid_invoices' => $withUnpaid,
        ];
    }

    /**
     * @return array{invoices_chased: int, active_chasers: int}
     */
    private function chaseOutcomes(Carbon $from, Carbon $to): array
    {
        $chasedByIds = Invoice::query()
            ->whereNotNull('chased_by_id')
            ->whereBetween('chased_at', [$from, $to])
            ->pluck('chased_by_id');

        return [
            'invoices_chased' => $chasedByIds->count(),
            'active_chasers' => $chasedByIds->unique()->count(),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function agentPerformance(Carbon $from, Carbon $to): array
    {
        $assigned = DB::table('whatsapp_conversation_agents')
            ->join('whatsapp_conversations', 'whatsapp_conversations.id', '=', 'whatsapp_conversation_agents.conversation_id')
            ->selectRaw('whatsapp_conversation_agents.user_id')
            ->selectRaw('COUNT(*) as assigned_total')
            ->selectRaw('SUM(CASE WHEN whatsapp_conversations.status IN (?, ?) THEN 1 ELSE 0 END) as open_pending', [
                WhatsAppConversation::STATUS_OPEN,
                WhatsAppConversation::STATUS_PENDING,
            ])
            ->groupBy('whatsapp_conversation_agents.user_id')
            ->get()
            ->keyBy('user_id');

        $outbound = WhatsAppMessage::query()
            ->where('direction', WhatsAppMessage::DIRECTION_OUTBOUND)
            ->where('is_note', false)
            ->whereNotNull('user_id')
            ->whereBetween('sent_at', [$from, $to])
            ->selectRaw('user_id, COUNT(*) as total')
            ->groupBy('user_id')
            ->pluck('total', 'user_id');

        $notes = WhatsAppMessage::query()
            ->where('is_note', true)
            ->whereNotNull('user_id')
            ->whereBetween('sent_at', [$from, $to])
            ->selectRaw('user_id, COUNT(*) as total')
            ->groupBy('user_id')
            ->pluck('total', 'user_id');

        $chased = Invoice::query()
            ->whereNotNull('chased_by_id')
            ->whereBetween('chased_at', [$from, $to])
            ->selectRaw('chased_by_id as user_id, COUNT(*) as total')
            ->groupBy('chased_by_id')
            ->pluck('total', 'user_id');

        $firstResponse = $this->agentFirstResponseAverages($from, $to);

        $userIds = collect()
            ->merge($assigned->keys())
            ->merge($outbound->keys())
            ->merge($notes->keys())
            ->merge($chased->keys())
            ->merge(array_keys($firstResponse))
            ->unique()
            ->filter()
            ->values();

        if ($userIds->isEmpty()) {
            return [];
        }

        $users = User::query()
            ->whereIn('id', $userIds)
            ->get(['id', 'name', 'email'])
            ->keyBy('id');

        $rows = $userIds->map(function ($userId) use ($users, $assigned, $outbound, $notes, $chased, $firstResponse) {
            $user = $users->get($userId);
            $assignment = $assigned->get($userId);

            return [
                'id' => $userId,
                'name' => $user ? $user->name : (string) $userId,
                'email' => $user ? $user->email : null,
                'assigned' => (int) ($assignment->assigned_total ?? 0),
                'open_pending' => (int) ($assignment->open_pending ?? 0),
                'outbound_messages' => (int) ($outbound[$userId] ?? 0),
                'notes' => (int) ($notes[$userId] ?? 0),
                'avg_first_response_minutes' => $firstResponse[$userId] ?? null,
                'invoices_chased' => (int) ($chased[$userId] ?? 0),
            ];
        });

        return $rows
            ->sortByDesc(function (array $row) {
                return ($row['outbound_messages'] * 1000) + $row['assigned'];
            })
            ->values()
            ->all();
    }

    /**
     * @return array<string|int, float>
     */
    private function agentFirstResponseAverages(Carbon $from, Carbon $to): array
    {
        $phones = WhatsAppMessage::query()
            ->where('direction', WhatsAppMessage::DIRECTION_INBOUND)
            ->where('is_note', false)
            ->whereBetween('sent_at', [$from, $to])
            ->distinct()
            ->limit(self::FIRST_RESPONSE_PHONE_LIMIT)
            ->pluck('phone');

        if ($phones->isEmpty()) {
            return [];
        }

        /** @var Collection<int, WhatsAppMessage> $messages */
        $messages = WhatsAppMessage::query()
            ->whereIn('phone', $phones)
            ->where('is_note', false)
            ->whereBetween('sent_at', [$from, $to])
            ->orderBy('sent_at')
            ->get(['phone', 'direction', 'user_id', 'sent_at', 'metadata']);

        $sums = [];

        foreach ($messages->groupBy('phone') as $phoneMessages) {
            $pendingInboundAt = null;

            foreach ($phoneMessages as $message) {
                $isBot = (bool) data_get($message->metadata, 'is_bot');

                if ($message->direction === WhatsAppMessage::DIRECTION_INBOUND) {
                    if ($pendingInboundAt === null) {
                        $pendingInboundAt = $message->sent_at;
                    }

                    continue;
                }

                if (
                    $message->direction === WhatsAppMessage::DIRECTION_OUTBOUND
                    && ! $isBot
                    && $message->user_id
                    && $pendingInboundAt
                ) {
                    $seconds = $pendingInboundAt->diffInSeconds($message->sent_at, false);
                    if ($seconds >= 0) {
                        $userId = $message->user_id;
                        if (! isset($sums[$userId])) {
                            $sums[$userId] = ['total' => 0, 'count' => 0];
                        }
                        $sums[$userId]['total'] += $seconds;
                        $sums[$userId]['count']++;
                    }
                    $pendingInboundAt = null;
                }
            }
        }

        $averages = [];
        foreach ($sums as $userId => $payload) {
            if ($payload['count'] > 0) {
                $averages[$userId] = round($payload['total'] / $payload['count'] / 60, 1);
            }
        }

        return $averages;
    }

    private function resolveDate(?string $value, Carbon $fallback): Carbon
    {
        if (! $value) {
            return $fallback->copy();
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable $e) {
            return $fallback->copy();
        }
    }
}
