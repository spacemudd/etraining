<template>
    <chat-layout>
        <div class="flex flex-col h-full w-full overflow-hidden bg-gray-50">
            <div class="flex items-center justify-between gap-3 flex-wrap px-4 py-2 border-b bg-white flex-shrink-0">
                <div class="flex items-center gap-3 min-w-0">
                    <inertia-link
                        :href="route('back.chat.index')"
                        class="text-xs leading-tight text-gray-600 hover:text-gray-900 border border-gray-300 bg-white hover:bg-gray-50 px-2 py-1 rounded-md font-medium transition whitespace-nowrap"
                    >
                        {{ $t('words.whatsapp-reports-back-to-chat') }}
                    </inertia-link>
                    <h2 class="font-semibold text-sm text-gray-800 leading-tight truncate">
                        {{ $t('words.whatsapp-reports') }}
                    </h2>
                </div>

                <form class="flex items-end gap-2 flex-wrap" @submit.prevent="applyDates">
                    <div>
                        <label class="block text-xs text-gray-500 mb-0.5" for="date_from">{{ $t('words.date-from') }}</label>
                        <input
                            id="date_from"
                            v-model="form.date_from"
                            type="date"
                            class="text-xs border border-gray-300 rounded-md px-2 py-1"
                            required
                        />
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-0.5" for="date_to">{{ $t('words.date-to') }}</label>
                        <input
                            id="date_to"
                            v-model="form.date_to"
                            type="date"
                            class="text-xs border border-gray-300 rounded-md px-2 py-1"
                            required
                        />
                    </div>
                    <button
                        type="submit"
                        class="text-xs leading-tight text-white bg-green-600 hover:bg-green-700 px-3 py-1.5 rounded-md font-medium transition whitespace-nowrap"
                        :disabled="processing"
                    >
                        {{ $t('words.whatsapp-reports-apply') }}
                    </button>
                </form>
            </div>

            <div class="flex-1 overflow-y-auto p-4 space-y-4">
                <section>
                    <h3 class="text-sm font-semibold text-gray-800 mb-2">{{ $t('words.whatsapp-reports-queue-health') }}</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-2">
                        <div class="bg-white border border-gray-200 rounded-md px-3 py-2">
                            <div class="text-xs text-gray-500">{{ $t('words.chat-status-open') }}</div>
                            <div class="text-lg font-semibold text-gray-900">{{ queue.open }}</div>
                        </div>
                        <div class="bg-white border border-gray-200 rounded-md px-3 py-2">
                            <div class="text-xs text-gray-500">{{ $t('words.chat-status-pending') }}</div>
                            <div class="text-lg font-semibold text-gray-900">{{ queue.pending }}</div>
                        </div>
                        <div class="bg-white border border-gray-200 rounded-md px-3 py-2">
                            <div class="text-xs text-gray-500">{{ $t('words.chat-status-closed') }}</div>
                            <div class="text-lg font-semibold text-gray-900">{{ queue.closed }}</div>
                        </div>
                        <div class="bg-white border border-gray-200 rounded-md px-3 py-2">
                            <div class="text-xs text-gray-500">{{ $t('words.chat-filter-unassigned') }}</div>
                            <div class="text-lg font-semibold text-gray-900">{{ queue.unassigned }}</div>
                        </div>
                        <div class="bg-white border border-gray-200 rounded-md px-3 py-2">
                            <div class="text-xs text-gray-500">{{ $t('words.whatsapp-reports-unread') }}</div>
                            <div class="text-lg font-semibold text-gray-900">{{ queue.unread }}</div>
                        </div>
                        <div class="bg-white border border-gray-200 rounded-md px-3 py-2">
                            <div class="text-xs text-gray-500">{{ $t('words.whatsapp-reports-need-human') }}</div>
                            <div class="text-lg font-semibold text-gray-900">{{ queue.need_human_agent }}</div>
                        </div>
                    </div>
                </section>

                <section>
                    <h3 class="text-sm font-semibold text-gray-800 mb-2">{{ $t('words.whatsapp-reports-activity') }}</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2">
                        <div class="bg-white border border-gray-200 rounded-md px-3 py-2">
                            <div class="text-xs text-gray-500">{{ $t('words.whatsapp-reports-inbound-messages') }}</div>
                            <div class="text-lg font-semibold text-gray-900">{{ activity.inbound }}</div>
                        </div>
                        <div class="bg-white border border-gray-200 rounded-md px-3 py-2">
                            <div class="text-xs text-gray-500">{{ $t('words.whatsapp-reports-outbound-messages') }}</div>
                            <div class="text-lg font-semibold text-gray-900">{{ activity.outbound }}</div>
                        </div>
                        <div class="bg-white border border-gray-200 rounded-md px-3 py-2">
                            <div class="text-xs text-gray-500">{{ $t('words.whatsapp-reports-bot-messages') }}</div>
                            <div class="text-lg font-semibold text-gray-900">{{ activity.bot_outbound }}</div>
                        </div>
                        <div class="bg-white border border-gray-200 rounded-md px-3 py-2">
                            <div class="text-xs text-gray-500">{{ $t('words.whatsapp-reports-new-conversations') }}</div>
                            <div class="text-lg font-semibold text-gray-900">{{ activity.new_conversations }}</div>
                        </div>
                        <div class="bg-white border border-gray-200 rounded-md px-3 py-2">
                            <div class="text-xs text-gray-500">{{ $t('words.whatsapp-reports-with-unpaid') }}</div>
                            <div class="text-lg font-semibold text-gray-900">{{ activity.with_unpaid_invoices }}</div>
                        </div>
                    </div>
                </section>

                <section>
                    <h3 class="text-sm font-semibold text-gray-800 mb-1">{{ $t('words.whatsapp-reports-chase-outcomes') }}</h3>
                    <p class="text-xs text-gray-500 mb-2">{{ $t('words.whatsapp-reports-chase-outcomes-note') }}</p>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                        <div class="bg-white border border-gray-200 rounded-md px-3 py-2">
                            <div class="text-xs text-gray-500">{{ $t('words.whatsapp-reports-invoices-chased') }}</div>
                            <div class="text-lg font-semibold text-gray-900">{{ chase.invoices_chased }}</div>
                        </div>
                        <div class="bg-white border border-gray-200 rounded-md px-3 py-2">
                            <div class="text-xs text-gray-500">{{ $t('words.whatsapp-reports-active-chasers') }}</div>
                            <div class="text-lg font-semibold text-gray-900">{{ chase.active_chasers }}</div>
                        </div>
                    </div>
                </section>

                <section>
                    <h3 class="text-sm font-semibold text-gray-800 mb-2">{{ $t('words.whatsapp-reports-agent-performance') }}</h3>
                    <div class="bg-white border border-gray-200 rounded-md overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-xs text-left">
                                <thead class="bg-gray-50 border-b border-gray-200 text-gray-600">
                                    <tr>
                                        <th class="px-3 py-2 font-medium whitespace-nowrap">{{ $t('words.whatsapp-reports-agent') }}</th>
                                        <th class="px-3 py-2 font-medium whitespace-nowrap">{{ $t('words.whatsapp-reports-assigned') }}</th>
                                        <th class="px-3 py-2 font-medium whitespace-nowrap">{{ $t('words.whatsapp-reports-open-pending') }}</th>
                                        <th class="px-3 py-2 font-medium whitespace-nowrap">{{ $t('words.whatsapp-reports-outbound-messages') }}</th>
                                        <th class="px-3 py-2 font-medium whitespace-nowrap">{{ $t('words.whatsapp-reports-notes') }}</th>
                                        <th class="px-3 py-2 font-medium whitespace-nowrap">{{ $t('words.whatsapp-reports-avg-first-response') }}</th>
                                        <th class="px-3 py-2 font-medium whitespace-nowrap">{{ $t('words.whatsapp-reports-invoices-chased') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="!agents.length">
                                        <td colspan="7" class="px-3 py-6 text-center text-gray-500">
                                            {{ $t('words.whatsapp-reports-no-agents') }}
                                        </td>
                                    </tr>
                                    <tr
                                        v-for="agent in agents"
                                        :key="agent.id"
                                        class="border-b border-gray-100 last:border-0 hover:bg-gray-50"
                                    >
                                        <td class="px-3 py-2 whitespace-nowrap">
                                            <div class="font-medium text-gray-900">{{ agent.name }}</div>
                                            <div v-if="agent.email" class="text-gray-500">{{ agent.email }}</div>
                                        </td>
                                        <td class="px-3 py-2">{{ agent.assigned }}</td>
                                        <td class="px-3 py-2">{{ agent.open_pending }}</td>
                                        <td class="px-3 py-2">{{ agent.outbound_messages }}</td>
                                        <td class="px-3 py-2">{{ agent.notes }}</td>
                                        <td class="px-3 py-2">
                                            {{ agent.avg_first_response_minutes != null ? agent.avg_first_response_minutes : '—' }}
                                        </td>
                                        <td class="px-3 py-2">{{ agent.invoices_chased }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </chat-layout>
</template>

<script>
import ChatLayout from '@/Layouts/ChatLayout';

export default {
    components: {
        ChatLayout,
    },
    props: {
        date_from: { type: String, required: true },
        date_to: { type: String, required: true },
        queue: { type: Object, required: true },
        activity: { type: Object, required: true },
        chase: { type: Object, required: true },
        agents: { type: Array, required: true },
    },
    data() {
        return {
            form: {
                date_from: this.date_from,
                date_to: this.date_to,
            },
            processing: false,
        };
    },
    methods: {
        applyDates() {
            this.processing = true;
            this.$inertia.get(
                this.route('back.chat.reports'),
                {
                    date_from: this.form.date_from,
                    date_to: this.form.date_to,
                },
                {
                    preserveState: false,
                    preserveScroll: true,
                    onFinish: () => {
                        this.processing = false;
                    },
                }
            );
        },
    },
};
</script>
