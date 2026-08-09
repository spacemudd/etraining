<!--
  - Copyright (c) 2020 - Clarastars, LLC  - All Rights Reserved.
  -
  - Unauthorized copying of this file via any medium is strictly prohibited.
  - This file is a proprietary of Clarastars LLC and is confidential / educational purpose only.
  -
  - https://clarastars.com - info@clarastars.com
  - @author Shafiq al-Shaar <shafiqalshaar@gmail.com>
  -->

<template>
    <div>
        <button
            @click="open"
            class="col-span-1 bg-green-500 shadow-lg rounded-lg p-5 transition-all duration-500 ease-in-out hover:bg-green-600 text-center text-white font-semibold flex items-center justify-center gap-2"
        >
            <ion-icon name="logo-whatsapp" class="w-6 h-6"></ion-icon>
            {{ $t('words.whatsapp') }}
        </button>

        <portal-target name="finance-whatsapp-chat-modal"></portal-target>
        <portal to="finance-whatsapp-chat-modal">
            <modal name="financeWhatsAppChatModal" :width="960" :height="'auto'" :scrollable="true">
                <div class="bg-white rounded-lg max-h-[90vh] flex flex-col">
                    <div class="px-5 py-4 border-b flex items-center justify-between bg-green-600 text-white rounded-t-lg">
                        <div class="flex items-center gap-2">
                            <ion-icon name="logo-whatsapp" class="w-6 h-6"></ion-icon>
                            <h1 class="text-lg font-bold">{{ $t('words.whatsapp-chat') }}</h1>
                        </div>
                        <button @click="close" class="text-white hover:text-green-100">
                            <ion-icon name="close" class="w-6 h-6"></ion-icon>
                        </button>
                    </div>

                    <div v-if="!configured" class="p-8 text-center text-gray-600">
                        {{ $t('words.whatsapp-not-configured') }}
                    </div>

                    <div v-else class="flex flex-col md:flex-row h-[600px] max-h-[75vh]">
                        <div class="md:w-1/3 border-b md:border-b-0 md:border-r flex flex-col overflow-y-auto">
                            <div class="p-4 border-b">
                                <input
                                    v-model="searchQuery"
                                    @input="searchTrainees"
                                    type="text"
                                    class="w-full form-input text-sm"
                                    :placeholder="$t('words.search-trainee')"
                                />
                            </div>

                            <div class="overflow-y-auto flex-1">
                                <div
                                    v-for="trainee in searchResults"
                                    :key="trainee.id"
                                    @click="selectTrainee(trainee)"
                                    class="p-3 border-b cursor-pointer hover:bg-green-50 transition-colors"
                                    :class="{ 'bg-green-100': selectedTrainee && selectedTrainee.id === trainee.id }"
                                >
                                    <div class="font-medium text-sm truncate">
                                        {{ trainee.name }}
                                        <span v-if="trainee.company_name" class="font-normal text-gray-500"> · {{ trainee.company_name }}</span>
                                    </div>
                                    <div class="text-xs text-gray-500">{{ trainee.phone }}</div>
                                </div>

                                <div v-if="searchQuery && !searching && searchResults.length === 0" class="p-4 text-sm text-gray-500 text-center">
                                    {{ $t('words.no-results') }}
                                </div>
                            </div>
                        </div>

                        <div class="md:w-2/3 flex flex-col h-full overflow-hidden">
                            <div v-if="!selectedTrainee" class="flex-1 flex items-center justify-center text-gray-400 p-8 text-center">
                                {{ $t('words.select-trainee') }}
                            </div>

                            <template v-else>
                                <div class="p-4 border-b bg-gray-50 space-y-3">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <div class="font-semibold truncate">
                                                {{ selectedTrainee.name }}
                                                <span v-if="selectedTrainee.company_name" class="font-normal text-gray-500"> · {{ selectedTrainee.company_name }}</span>
                                            </div>
                                            <div class="text-sm text-gray-600">{{ $t('words.phone') }}: {{ selectedTrainee.phone }}</div>
                                            <div class="text-xs text-green-600 mt-1">{{ $t('words.whatsapp-live-updates') }}</div>
                                        </div>
                                        <div class="flex items-center gap-2 flex-shrink-0 flex-wrap justify-end">
                                            <div
                                                v-if="botStatus"
                                                class="flex items-center gap-2"
                                            >
                                                <span
                                                    class="text-xs px-2.5 py-1.5 rounded-lg font-semibold whitespace-nowrap border"
                                                    :class="botStatusBadgeClass"
                                                    :title="botStatusTitle"
                                                >
                                                    {{ botStatusLabel }}
                                                </span>
                                                <button
                                                    v-if="botStatus.workflow_assigned && !botStatus.is_paused"
                                                    type="button"
                                                    class="text-xs bg-white border border-orange-300 hover:bg-orange-50 text-orange-800 px-3 py-1.5 rounded-lg font-medium transition disabled:opacity-50"
                                                    :disabled="pausingBot"
                                                    @click="pauseBot"
                                                >
                                                    {{ pausingBot ? $t('words.saving') : $t('words.pause-bot-30m') }}
                                                </button>
                                            </div>
                                            <a
                                                v-if="selectedTrainee.show_url"
                                                :href="selectedTrainee.show_url"
                                                target="_blank"
                                                class="text-xs bg-white border border-gray-300 hover:bg-gray-100 px-3 py-1.5 rounded-lg font-medium text-gray-700 transition"
                                            >
                                                {{ $t('words.profile') }}
                                            </a>
                                            <span
                                                v-if="!loadingPendingInvoices && pendingInvoices.length"
                                                class="text-xs bg-amber-100 border border-amber-200 text-amber-900 px-2.5 py-1.5 rounded-lg font-semibold whitespace-nowrap"
                                            >
                                                {{ pendingInvoices.length }} · {{ formatAmount(pendingTotalOwed) }}
                                            </span>
                                        </div>
                                    </div>

                                    <div v-if="loadingPendingInvoices" class="text-xs text-gray-500">
                                        {{ $t('words.loading') }}...
                                    </div>

                                    <div
                                        v-else-if="pendingInvoices.length"
                                        class="rounded-lg border border-amber-200 bg-amber-50/80 overflow-hidden"
                                    >
                                        <div class="px-3 py-2 border-b border-amber-200 flex items-center justify-between gap-2">
                                            <span class="text-xs font-semibold text-amber-950">{{ $t('words.pending-invoices') }}</span>
                                            <span class="text-xs text-amber-800">
                                                {{ $t('words.outstanding-amount') }}: {{ formatAmount(pendingTotalOwed) }}
                                            </span>
                                        </div>
                                        <ul class="divide-y divide-amber-100 max-h-36 overflow-y-auto">
                                            <li
                                                v-for="invoice in pendingInvoices"
                                                :key="invoice.id"
                                                class="px-3 py-2 flex items-center justify-between gap-3 text-xs"
                                            >
                                                <div class="min-w-0 flex items-center gap-2">
                                                    <a
                                                        :href="invoice.show_url"
                                                        target="_blank"
                                                        class="font-medium text-blue-600 hover:text-blue-700 hover:underline truncate"
                                                    >
                                                        {{ invoice.number_formatted }}
                                                    </a>
                                                    <span v-if="invoice.month_of" class="text-gray-500 flex-shrink-0">{{ invoice.month_of }}</span>
                                                </div>
                                                <div class="flex items-center gap-2 flex-shrink-0">
                                                    <span class="font-semibold text-gray-800 tabular-nums">{{ formatAmount(invoice.grand_total) }}</span>
                                                    <span class="text-amber-800 bg-amber-100 px-1.5 py-0.5 rounded">{{ invoice.status_formatted }}</span>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>

                                    <div v-else class="text-xs text-gray-500">
                                        {{ $t('words.no-pending-invoices') }}
                                    </div>
                                </div>

                                <div ref="messagesContainer" class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-100 min-h-[200px]">
                                    <div v-if="loadingMessages" class="text-center text-sm text-gray-500 py-4">
                                        {{ $t('words.loading') }}...
                                    </div>

                                    <div
                                        v-for="message in messages"
                                        :key="message.id || message.sid || (message.date_sent + '-' + message.body)"
                                        class="flex"
                                        :class="messageAlignmentClass(message)"
                                    >
                                        <div
                                            class="max-w-[80%] md:max-w-[70%] rounded-2xl px-4 py-2.5 text-sm shadow-sm"
                                            :class="isOutboundMessage(message)
                                                ? (isBotMessage(message)
                                                    ? 'bg-indigo-600 text-white rounded-tr-sm'
                                                    : 'bg-green-600 text-white rounded-tr-sm')
                                                : 'bg-white text-gray-800 border border-gray-200 rounded-tl-sm'"
                                        >
                                            <div
                                                v-if="isBotMessage(message)"
                                                class="text-[10px] font-semibold uppercase tracking-wide mb-1 opacity-90"
                                            >
                                                🤖 {{ $t('words.whatsapp-bot-label') }}
                                            </div>
                                            <p class="whitespace-pre-wrap break-words" dir="auto">{{ message.body }}</p>
                                            <div v-if="message.metadata && message.metadata.media && message.metadata.media.length" class="mt-2 space-y-1">
                                                <a
                                                    v-for="(media, mediaIndex) in message.metadata.media"
                                                    :key="mediaIndex"
                                                    :href="media.url"
                                                    target="_blank"
                                                    class="block text-xs underline"
                                                    :class="isOutboundMessage(message) ? 'text-green-100' : 'text-blue-600'"
                                                >
                                                    {{ media.content_type || $t('words.attachment') }}
                                                </a>
                                            </div>
                                            <div class="text-[11px] mt-1.5 flex items-center gap-1.5 opacity-75" :class="isOutboundMessage(message) ? 'justify-end text-green-100' : 'justify-start text-gray-500'" dir="ltr">
                                                <span>{{ formatMessageTime(message.date_sent) }}</span>
                                                <span v-if="message.status" class="capitalize">· {{ message.status }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="border-t p-4 bg-white">
                                    <div class="flex gap-2 mb-3">
                                        <button
                                            @click="sendMode = 'template'"
                                            class="px-3 py-1 rounded text-sm font-medium"
                                            :class="sendMode === 'template' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700'"
                                        >
                                            {{ $t('words.whatsapp-templates') }}
                                        </button>
                                        <button
                                            @click="sendMode = 'freeform'"
                                            class="px-3 py-1 rounded text-sm font-medium"
                                            :class="sendMode === 'freeform' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700'"
                                        >
                                            {{ $t('words.message') }}
                                        </button>
                                    </div>

                                    <div v-if="sendMode === 'template'">
                                        <div v-if="loadingTemplates" class="text-sm text-gray-500 mb-3">
                                            {{ $t('words.loading') }}...
                                        </div>

                                        <select
                                            v-model="selectedTemplateSid"
                                            @change="onTemplateChange"
                                            class="w-full form-select text-sm mb-3"
                                        >
                                            <option value="">{{ $t('words.select-template') }}</option>
                                            <option
                                                v-for="template in templates"
                                                :key="template.sid"
                                                :value="template.sid"
                                            >
                                                {{ template.friendly_name }} ({{ template.language }})
                                            </option>
                                        </select>

                                        <div v-if="selectedTemplate" class="mb-3 p-3 bg-gray-50 rounded text-sm whitespace-pre-wrap border">
                                            {{ previewTemplateBody }}
                                        </div>

                                        <div v-if="selectedTemplate && manualTemplateVariables.length" class="space-y-2 mb-3">
                                            <div class="text-sm font-medium text-gray-700">{{ $t('words.template-variables') }}</div>
                                            <div
                                                v-for="variableKey in manualTemplateVariables"
                                                :key="variableKey"
                                            >
                                                <label class="text-xs text-gray-500">
                                                    {{ templateVariableLabel(variableKey) }}
                                                    <span v-if="variableSample(variableKey)" class="text-gray-400">
                                                        ({{ variableSample(variableKey) }})
                                                    </span>
                                                </label>
                                                <input
                                                    v-model="templateVariables[variableKey]"
                                                    type="text"
                                                    class="w-full form-input text-sm"
                                                    :placeholder="variableSample(variableKey)"
                                                />
                                            </div>
                                        </div>
                                        <p
                                            v-else-if="selectedTemplate && selectedTemplate.variables && selectedTemplate.variables.length"
                                            class="text-xs text-green-700 mb-3"
                                        >
                                            {{ $t('words.whatsapp-auto-filled-variables') }}
                                        </p>

                                        <jet-button
                                            @click.native="sendTemplate"
                                            :class="{ 'opacity-25': sending }"
                                            :disabled="sending || !selectedTemplateSid"
                                            class="bg-green-600 hover:bg-green-700"
                                        >
                                            {{ $t('words.send-whatsapp-template') }}
                                        </jet-button>
                                    </div>

                                    <div v-else>
                                        <jet-textarea
                                            v-model="freeformMessage"
                                            class="w-full text-sm mb-3"
                                            rows="3"
                                            :placeholder="$t('words.message')"
                                        />
                                        <p class="text-xs text-gray-500 mb-3">{{ $t('words.whatsapp-freeform-hint') }}</p>
                                        <jet-button
                                            @click.native="sendFreeform"
                                            :class="{ 'opacity-25': sending }"
                                            :disabled="sending || !freeformMessage.trim()"
                                            class="bg-green-600 hover:bg-green-700"
                                        >
                                            {{ $t('words.send') }}
                                        </jet-button>
                                    </div>

                                    <p v-if="errorMessage" class="mt-3 text-sm text-red-600">{{ errorMessage }}</p>
                                    <p v-if="successMessage" class="mt-3 text-sm text-green-600">{{ successMessage }}</p>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </modal>
        </portal>
    </div>
</template>

<script>
import axios from 'axios';
import throttle from 'lodash/throttle';
import JetButton from '@/Jetstream/Button';
import JetTextarea from '@/Jetstream/Textarea';

export default {
    components: {
        JetButton,
        JetTextarea,
    },
    data() {
        return {
            configured: false,
            searchQuery: '',
            searchResults: [],
            searching: false,
            selectedTrainee: null,
            pendingInvoices: [],
            pendingTotalOwed: 0,
            loadingPendingInvoices: false,
            templates: [],
            selectedTemplateSid: '',
            selectedTemplate: null,
            templateVariables: {},
            loadingTemplates: false,
            messages: [],
            loadingMessages: false,
            sendMode: 'template',
            freeformMessage: '',
            sending: false,
            errorMessage: '',
            successMessage: '',
            pollInterval: null,
            echoChannel: null,
            botStatus: null,
            pausingBot: false,
            messagesRefreshTimer: null,
        };
    },
    computed: {
        botStatusLabel() {
            if (!this.botStatus) {
                return '';
            }
            if (!this.botStatus.workflow_assigned) {
                return this.$t('words.bot-not-assigned');
            }
            if (this.botStatus.is_paused) {
                return this.$t('words.bot-paused');
            }
            return this.$t('words.bot-active');
        },
        botStatusTitle() {
            if (!this.botStatus) {
                return '';
            }
            if (this.botStatus.workflow_name) {
                const name = this.botStatus.workflow_name;
                if (this.botStatus.is_paused && this.botStatus.paused_until) {
                    return name + ' · ' + this.formatPausedUntil(this.botStatus.paused_until);
                }
                return name;
            }
            if (this.botStatus.is_paused && this.botStatus.paused_until) {
                return this.formatPausedUntil(this.botStatus.paused_until);
            }
            return '';
        },
        botStatusBadgeClass() {
            if (!this.botStatus) {
                return 'bg-gray-100 border-gray-200 text-gray-600';
            }
            if (!this.botStatus.workflow_assigned) {
                return 'bg-gray-100 border-gray-200 text-gray-600';
            }
            if (this.botStatus.is_paused) {
                return 'bg-orange-100 border-orange-200 text-orange-900';
            }
            return 'bg-green-100 border-green-200 text-green-800';
        },
        lastMessageAt() {
            if (!this.messages.length) {
                return null;
            }

            const lastMessage = this.messages[this.messages.length - 1];

            return lastMessage.date_sent || null;
        },
        manualTemplateVariables() {
            if (!this.selectedTemplate) {
                return [];
            }
            return this.selectedTemplate.manual_variables || this.selectedTemplate.variables || [];
        },
        previewTemplateBody() {
            if (!this.selectedTemplate) {
                return '';
            }

            let body = this.selectedTemplate.body_display || this.selectedTemplate.body;
            const values = { ...(this.templateVariables || {}) };
            const autoVariables = this.selectedTemplate.auto_variables || {};
            const bindings = this.selectedTemplate.variable_bindings || {};

            Object.keys(autoVariables).forEach((key) => {
                const autoValue = this.autoValueForTag(autoVariables[key]);
                if (autoValue) {
                    values[key] = autoValue;
                    values[autoVariables[key]] = autoValue;
                }
            });

            Object.keys(bindings).forEach((key) => {
                if (values[key]) {
                    values[bindings[key]] = values[key];
                }
            });

            Object.keys(values).forEach((key) => {
                const value = values[key] || `{{${key}}}`;
                body = body.replace(new RegExp(`\\{\\{\\s*${key}\\s*\\}\\}`, 'g'), value);
            });

            return body;
        },
    },
    beforeDestroy() {
        this.unsubscribeEcho();
        this.stopPolling();
        if (this.messagesRefreshTimer) {
            clearTimeout(this.messagesRefreshTimer);
            this.messagesRefreshTimer = null;
        }
    },
    methods: {
        async open() {
            this.$modal.show('financeWhatsAppChatModal');
            await this.checkStatus();

            if (this.configured) {
                await this.loadTemplates();
                this.subscribeEcho();
            }
        },
        close() {
            this.unsubscribeEcho();
            this.stopPolling();
            this.$modal.hide('financeWhatsAppChatModal');
            this.resetState();
        },
        normalizePhone(phone) {
            return String(phone || '').replace(/\D+/g, '');
        },
        subscribeEcho() {
            this.unsubscribeEcho();

            if (!window.Echo) {
                console.warn('[FinanceWhatsAppChat] Echo unavailable — live updates will use polling fallback when a trainee is selected');
                return;
            }

            console.log('[FinanceWhatsAppChat] Subscribing to channel whatsapp-chat');

            this.echoChannel = window.Echo.channel('whatsapp-chat')
                .listen('.WhatsAppMessageReceived', (event) => {
                    console.log('[FinanceWhatsAppChat] WhatsAppMessageReceived', event && event.message);

                    const message = event.message;
                    if (!message || !this.selectedTrainee) {
                        console.log('[FinanceWhatsAppChat] Ignoring event (no message or no selected trainee)');
                        return;
                    }

                    const selectedPhone = this.normalizePhone(this.selectedTrainee.phone);
                    const messagePhone = this.normalizePhone(message.phone);

                    if (selectedPhone && messagePhone && selectedPhone === messagePhone) {
                        console.log('[FinanceWhatsAppChat] Merging message for selected trainee');
                        this.mergeMessages([message]);
                        if (!this.isOutboundMessage(message) || this.isBotMessage(message)) {
                            this.scheduleMessagesRefresh();
                        }
                    } else {
                        console.log('[FinanceWhatsAppChat] Ignoring message for other phone', {
                            selectedPhone,
                            messagePhone,
                        });
                    }
                });
        },
        unsubscribeEcho() {
            if (window.Echo && this.echoChannel) {
                console.log('[FinanceWhatsAppChat] Leaving channel whatsapp-chat');
                window.Echo.leave('whatsapp-chat');
            }
            this.echoChannel = null;
        },
        resetState() {
            this.searchQuery = '';
            this.searchResults = [];
            this.selectedTrainee = null;
            this.pendingInvoices = [];
            this.pendingTotalOwed = 0;
            this.loadingPendingInvoices = false;
            this.messages = [];
            this.selectedTemplateSid = '';
            this.selectedTemplate = null;
            this.templateVariables = {};
            this.freeformMessage = '';
            this.errorMessage = '';
            this.successMessage = '';
            this.sendMode = 'template';
            this.botStatus = null;
            this.pausingBot = false;
        },
        formatPausedUntil(iso) {
            if (!iso) {
                return '';
            }
            try {
                const date = new Date(iso);
                if (Number.isNaN(date.getTime())) {
                    return iso;
                }
                return this.$t('words.bot-paused-until') + ' ' + date.toLocaleString();
            } catch (e) {
                return iso;
            }
        },
        async loadBotStatus() {
            if (!this.selectedTrainee || !this.selectedTrainee.phone) {
                this.botStatus = null;
                return;
            }

            try {
                const { data } = await axios.get(route('back.finance.whatsapp.bot-status'), {
                    params: { phone: this.selectedTrainee.phone },
                });
                this.botStatus = data;
            } catch (error) {
                this.botStatus = null;
            }
        },
        async pauseBot() {
            if (!this.selectedTrainee || !this.selectedTrainee.phone || this.pausingBot) {
                return;
            }

            this.pausingBot = true;
            this.errorMessage = '';
            this.successMessage = '';

            try {
                const { data } = await axios.post(route('back.finance.whatsapp.bot-pause'), {
                    phone: this.selectedTrainee.phone,
                });
                this.botStatus = data.bot || null;
                this.successMessage = data.message || this.$t('words.whatsapp-bot-paused');
            } catch (error) {
                this.errorMessage = (error.response && error.response.data && error.response.data.message)
                    || this.$t('words.whatsapp-bot-pause-failed');
            } finally {
                this.pausingBot = false;
            }
        },
        async checkStatus() {
            try {
                const { data } = await axios.get(route('back.finance.whatsapp.status'));
                this.configured = data.configured;
            } catch (error) {
                this.configured = false;
            }
        },
        searchTrainees: throttle(async function () {
            if (!this.searchQuery || this.searchQuery.length < 2) {
                this.searchResults = [];
                return;
            }

            this.searching = true;

            try {
                const { data } = await axios.get(route('back.finance.whatsapp.trainees'), {
                    params: { search: this.searchQuery },
                });
                this.searchResults = data.trainees;
            } catch (error) {
                this.searchResults = [];
            } finally {
                this.searching = false;
            }
        }, 300),
        async selectTrainee(trainee) {
            this.stopPolling();
            this.selectedTrainee = trainee;
            this.errorMessage = '';
            this.successMessage = '';
            this.messages = [];
            this.pendingInvoices = [];
            this.pendingTotalOwed = 0;
            await Promise.all([
                this.loadMessages(false),
                this.loadPendingInvoices(),
                this.loadBotStatus(),
            ]);

            // Soft-poll while a chat is open so bot replies from the queue appear live.
            this.startPolling();
        },
        scheduleMessagesRefresh() {
            if (this.messagesRefreshTimer) {
                clearTimeout(this.messagesRefreshTimer);
            }
            this.messagesRefreshTimer = setTimeout(() => {
                this.messagesRefreshTimer = null;
                if (this.selectedTrainee) {
                    this.loadMessages(true);
                    this.loadBotStatus();
                }
            }, 1200);
        },
        async loadPendingInvoices() {
            if (!this.selectedTrainee || !this.selectedTrainee.id) {
                this.pendingInvoices = [];
                this.pendingTotalOwed = 0;
                return;
            }

            this.loadingPendingInvoices = true;

            try {
                const { data } = await axios.get(
                    route('back.finance.whatsapp.trainees.pending-invoices', this.selectedTrainee.id)
                );
                this.pendingInvoices = data.invoices || [];
                this.pendingTotalOwed = data.total_owed || 0;
            } catch (error) {
                this.pendingInvoices = [];
                this.pendingTotalOwed = 0;
            } finally {
                this.loadingPendingInvoices = false;
            }
        },
        async loadTemplates() {
            this.loadingTemplates = true;

            try {
                const { data } = await axios.get(route('back.finance.whatsapp.templates'));
                this.templates = data.templates;
            } catch (error) {
                this.templates = [];
                this.errorMessage = this.$t('words.whatsapp-templates-load-failed');
            } finally {
                this.loadingTemplates = false;
            }
        },
        async onTemplateChange() {
            this.templateVariables = {};
            this.selectedTemplate = null;

            if (!this.selectedTemplateSid) {
                return;
            }

            try {
                const { data } = await axios.get(route('back.finance.whatsapp.templates.show', this.selectedTemplateSid));
                this.selectedTemplate = data.template;
                const bindings = this.selectedTemplate.variable_bindings || {};
                const autoVariables = this.selectedTemplate.auto_variables || {};
                (this.selectedTemplate.variables || []).forEach((key) => {
                    const tag = autoVariables[key] || bindings[key] || '';
                    const defaultValue = tag ? this.autoValueForTag(tag) : '';
                    this.$set(this.templateVariables, key, defaultValue);
                });
            } catch (error) {
                this.errorMessage = this.$t('words.whatsapp-templates-load-failed');
            }
        },
        templateVariableLabel(variableKey) {
            const bindings = (this.selectedTemplate && this.selectedTemplate.variable_bindings) || {};
            return bindings[variableKey] || (this.$t('words.template-variable') + ' ' + variableKey);
        },
        autoValueForTag(tag) {
            if (!this.selectedTrainee) {
                return '';
            }
            switch (tag) {
                case 'trainee_name':
                    return this.selectedTrainee.name || '';
                case 'trainee_english_name':
                    return this.selectedTrainee.english_name || '';
                case 'trainee_phone':
                    return this.selectedTrainee.phone || '';
                case 'trainee_identity':
                    return this.selectedTrainee.identity_number || '';
                case 'company_name':
                    return this.selectedTrainee.company_name || '';
                default:
                    return '';
            }
        },
        async loadMessages(poll = false) {
            if (!this.selectedTrainee) {
                return;
            }

            if (!poll) {
                this.loadingMessages = true;
            }

            try {
                const params = {
                    phone: this.selectedTrainee.phone,
                    limit: 50,
                };

                if (poll && this.lastMessageAt) {
                    params.since = this.lastMessageAt;
                }

                const { data } = await axios.get(route('back.finance.whatsapp.messages'), { params });

                if (poll) {
                    this.mergeMessages(data.messages);
                } else {
                    this.messages = data.messages;
                    this.$nextTick(() => this.scrollToBottom());
                }
            } catch (error) {
                if (!poll) {
                    this.messages = [];
                }
            } finally {
                if (!poll) {
                    this.loadingMessages = false;
                }
            }
        },
        mergeMessages(newMessages) {
            if (!newMessages.length) {
                return;
            }

            let added = false;

            newMessages.forEach((message) => {
                const messageKeys = [message.sid, message.id].filter(Boolean).map(String);
                const existingIndex = this.messages.findIndex((item) => {
                    const itemKeys = [item.sid, item.id].filter(Boolean).map(String);
                    return messageKeys.some((key) => itemKeys.includes(key));
                });

                if (existingIndex !== -1) {
                    this.$set(this.messages, existingIndex, { ...this.messages[existingIndex], ...message });
                    return;
                }

                this.messages.push(message);
                added = true;
            });

            if (added) {
                this.$nextTick(() => this.scrollToBottom());
            }
        },
        startPolling() {
            this.stopPolling();
            this.pollInterval = setInterval(() => {
                this.loadMessages(true);
            }, 3000);
        },
        stopPolling() {
            if (this.pollInterval) {
                clearInterval(this.pollInterval);
                this.pollInterval = null;
            }
        },
        isOutboundMessage(message) {
            return ['outbound-api', 'outbound-reply', 'outbound'].includes(message.direction);
        },
        isBotMessage(message) {
            if (!message) {
                return false;
            }
            if (message.is_bot) {
                return true;
            }
            if (message.author && message.author.is_bot) {
                return true;
            }
            return !!(message.metadata && message.metadata.is_bot);
        },
        isRtl() {
            return document.documentElement.dir === 'rtl' || (this.$page && this.$page.props && this.$page.props.locale === 'ar');
        },
        messageAlignmentClass(message) {
            if (message.is_note) {
                return 'justify-center';
            }
            const isOutbound = this.isOutboundMessage(message);
            const isRTL = this.isRtl();
            if (isOutbound) {
                return isRTL ? 'justify-start' : 'justify-end';
            } else {
                return isRTL ? 'justify-end' : 'justify-start';
            }
        },
        variableSample(variableKey) {
            if (!this.selectedTemplate || !this.selectedTemplate.variable_samples) {
                return '';
            }

            return this.selectedTemplate.variable_samples[variableKey] || '';
        },
        async sendTemplate() {
            if (!this.selectedTrainee || !this.selectedTemplateSid) {
                return;
            }

            this.sending = true;
            this.errorMessage = '';
            this.successMessage = '';

            try {
                const { data } = await axios.post(route('back.finance.whatsapp.send-template'), {
                    phone: this.selectedTrainee.phone,
                    trainee_id: this.selectedTrainee.id,
                    content_sid: this.selectedTemplateSid,
                    content_variables: this.templateVariables,
                });

                this.mergeMessages([data.message]);
                this.successMessage = this.$t('words.whatsapp-sent-successfully');
                this.$nextTick(() => this.scrollToBottom());
                await this.loadBotStatus();
            } catch (error) {
                this.errorMessage = error.response?.data?.message || this.$t('words.whatsapp-send-failed');
            } finally {
                this.sending = false;
            }
        },
        async sendFreeform() {
            if (!this.selectedTrainee || !this.freeformMessage.trim()) {
                return;
            }

            this.sending = true;
            this.errorMessage = '';
            this.successMessage = '';

            try {
                const { data } = await axios.post(route('back.finance.whatsapp.send-message'), {
                    phone: this.selectedTrainee.phone,
                    trainee_id: this.selectedTrainee.id,
                    body: this.freeformMessage.trim(),
                });

                this.mergeMessages([data.message]);
                this.freeformMessage = '';
                this.successMessage = this.$t('words.whatsapp-sent-successfully');
                this.$nextTick(() => this.scrollToBottom());
                await this.loadBotStatus();
            } catch (error) {
                this.errorMessage = error.response?.data?.message || this.$t('words.whatsapp-send-failed');
            } finally {
                this.sending = false;
            }
        },
        scrollToBottom() {
            const container = this.$refs.messagesContainer;
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        },
        formatMessageTime(dateString) {
            if (!dateString) {
                return '';
            }

            const date = new Date(dateString);
            return date.toLocaleString();
        },
        formatAmount(amount) {
            const value = Number(amount) || 0;
            return value.toLocaleString(undefined, {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            });
        },
    },
};
</script>

<style scoped>
.overflow-y-auto::-webkit-scrollbar {
    width: 6px;
}
.overflow-y-auto::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}
.overflow-y-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}
.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>
