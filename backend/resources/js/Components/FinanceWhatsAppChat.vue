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

                    <div v-else class="flex flex-col md:flex-row min-h-[500px] max-h-[75vh]">
                        <div class="md:w-1/3 border-b md:border-b-0 md:border-r flex flex-col">
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
                                    <div class="font-medium text-sm">{{ trainee.name }}</div>
                                    <div class="text-xs text-gray-500">{{ trainee.phone }}</div>
                                    <div v-if="trainee.company_name" class="text-xs text-gray-400">{{ trainee.company_name }}</div>
                                </div>

                                <div v-if="searchQuery && !searching && searchResults.length === 0" class="p-4 text-sm text-gray-500 text-center">
                                    {{ $t('words.no-results') }}
                                </div>
                            </div>
                        </div>

                        <div class="md:w-2/3 flex flex-col">
                            <div v-if="!selectedTrainee" class="flex-1 flex items-center justify-center text-gray-400 p-8 text-center">
                                {{ $t('words.select-trainee') }}
                            </div>

                            <template v-else>
                                <div class="p-4 border-b bg-gray-50">
                                    <div class="font-semibold">{{ selectedTrainee.name }}</div>
                                    <div class="text-sm text-gray-600">{{ $t('words.phone') }}: {{ selectedTrainee.phone }}</div>
                                    <div class="text-xs text-green-600 mt-1">{{ $t('words.whatsapp-live-updates') }}</div>
                                </div>

                                <div ref="messagesContainer" class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-100 min-h-[200px]">
                                    <div v-if="loadingMessages" class="text-center text-sm text-gray-500">
                                        {{ $t('words.loading') }}...
                                    </div>

                                    <div
                                        v-for="message in messages"
                                        :key="message.sid || message.date_sent + message.body"
                                        class="flex"
                                        :class="isOutboundMessage(message) ? 'justify-end' : 'justify-start'"
                                    >
                                        <div
                                            class="max-w-[80%] rounded-lg px-3 py-2 text-sm shadow"
                                            :class="isOutboundMessage(message)
                                                ? 'bg-green-500 text-white'
                                                : 'bg-white text-gray-800'"
                                        >
                                            <p class="whitespace-pre-wrap">{{ message.body }}</p>
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
                                            <div class="text-xs mt-1 opacity-75">
                                                {{ formatMessageTime(message.date_sent) }}
                                                <span v-if="message.status"> · {{ message.status }}</span>
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

                                        <div v-if="selectedTemplate && selectedTemplate.variables.length" class="space-y-2 mb-3">
                                            <div class="text-sm font-medium text-gray-700">{{ $t('words.template-variables') }}</div>
                                            <div
                                                v-for="variableKey in selectedTemplate.variables"
                                                :key="variableKey"
                                            >
                                                <label class="text-xs text-gray-500">
                                                    {{ $t('words.template-variable') }} {{ variableKey }}
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
        };
    },
    computed: {
        lastMessageAt() {
            if (!this.messages.length) {
                return null;
            }

            const lastMessage = this.messages[this.messages.length - 1];

            return lastMessage.date_sent || null;
        },
        previewTemplateBody() {
            if (!this.selectedTemplate) {
                return '';
            }

            let body = this.selectedTemplate.body;

            Object.keys(this.templateVariables).forEach((key) => {
                const value = this.templateVariables[key] || `{{${key}}}`;
                body = body.replace(new RegExp(`\\{\\{${key}\\}\\}`, 'g'), value);
            });

            return body;
        },
    },
    beforeDestroy() {
        this.stopPolling();
    },
    methods: {
        async open() {
            this.$modal.show('financeWhatsAppChatModal');
            await this.checkStatus();

            if (this.configured) {
                await this.loadTemplates();
            }
        },
        close() {
            this.stopPolling();
            this.$modal.hide('financeWhatsAppChatModal');
            this.resetState();
        },
        resetState() {
            this.searchQuery = '';
            this.searchResults = [];
            this.selectedTrainee = null;
            this.messages = [];
            this.selectedTemplateSid = '';
            this.selectedTemplate = null;
            this.templateVariables = {};
            this.freeformMessage = '';
            this.errorMessage = '';
            this.successMessage = '';
            this.sendMode = 'template';
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
            await this.loadMessages(false);
            this.startPolling();
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
                this.selectedTemplate.variables.forEach((key) => {
                    const defaultValue = this.selectedTrainee && key === '1'
                        ? this.selectedTrainee.name
                        : '';
                    this.$set(this.templateVariables, key, defaultValue);
                });
            } catch (error) {
                this.errorMessage = this.$t('words.whatsapp-templates-load-failed');
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

            const existingSids = new Set(this.messages.map((message) => message.sid).filter(Boolean));
            let added = false;

            newMessages.forEach((message) => {
                if (message.sid && existingSids.has(message.sid)) {
                    const index = this.messages.findIndex((item) => item.sid === message.sid);
                    if (index !== -1) {
                        this.$set(this.messages, index, message);
                    }
                    return;
                }

                this.messages.push(message);
                added = true;

                if (message.sid) {
                    existingSids.add(message.sid);
                }
            });

            if (added) {
                this.$nextTick(() => this.scrollToBottom());
            }
        },
        startPolling() {
            this.stopPolling();
            this.pollInterval = setInterval(() => {
                this.loadMessages(true);
            }, 4000);
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
    },
};
</script>
