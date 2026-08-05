<template>
    <app-layout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $t('words.chat') }}
                </h2>
                <div class="flex items-center gap-3">
                    <button
                        @click="openNewChatModal"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-semibold flex items-center gap-2 shadow transition-colors"
                    >
                        <ion-icon name="add-outline" class="w-5 h-5"></ion-icon>
                        {{ $t('words.new-chat') }}
                    </button>
                    <a
                        href="/back/finance/whatsapp/status"
                        target="_blank"
                        class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold flex items-center gap-2 border border-gray-300 shadow-sm transition"
                    >
                        <ion-icon name="settings-outline" class="w-5 h-5"></ion-icon>
                        {{ $t('words.account') }}
                    </a>
                </div>
            </div>
        </template>

        <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg flex h-[calc(100vh-210px)] min-h-[600px] border border-gray-200">
                
                <!-- Left Sidebar: Conversations List -->
                <div class="w-full md:w-80 lg:w-96 border-r flex flex-col bg-gray-50">
                    <div class="p-3 border-b bg-white">
                        <input
                            v-model="conversationSearch"
                            type="text"
                            class="w-full form-input text-sm rounded-lg border-gray-300"
                            :placeholder="$t('words.search') + '...'"
                        />
                    </div>

                    <div class="overflow-y-auto flex-1 divide-y divide-gray-100 flex flex-col justify-between">
                        <div>
                            <div v-if="loadingConversations" class="p-4 text-center text-sm text-gray-500">
                                {{ $t('words.loading') }}...
                            </div>
                            <div v-else-if="filteredConversations.length === 0" class="p-6 text-center text-sm text-gray-500">
                                {{ $t('words.no-results') }}
                            </div>
                            <div
                                v-for="conv in paginatedConversations"
                                :key="conv.phone"
                                @click="selectConversation(conv)"
                                class="p-4 cursor-pointer hover:bg-green-50 transition-colors flex flex-col gap-1"
                                :class="{ 'bg-green-100 border-l-4 border-green-600': selectedConversation && selectedConversation.phone === conv.phone }"
                            >
                                <div class="flex items-center justify-between">
                                    <span class="font-semibold text-sm text-gray-900 truncate">
                                        {{ conv.trainee ? conv.trainee.name : conv.phone }}
                                    </span>
                                    <span class="text-[11px] text-gray-400" dir="ltr">
                                        {{ formatTimeShort(conv.last_message.sent_at) }}
                                    </span>
                                </div>
                                <div v-if="conv.trainee && conv.trainee.company_name" class="text-xs text-gray-600 font-medium">
                                    {{ conv.trainee.company_name }}
                                </div>
                                <div class="flex items-center justify-between text-xs text-gray-500">
                                    <span class="truncate max-w-[200px]">
                                        <span v-if="conv.last_message.is_note" class="text-yellow-700 font-medium">[{{ $t('words.internal-note') }}]: </span>
                                        {{ conv.last_message.body }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Pagination Controls -->
                        <div v-if="totalPages > 1" class="p-3 border-t bg-white flex items-center justify-between text-xs text-gray-600">
                            <button
                                @click="conversationPage--"
                                :disabled="conversationPage === 1"
                                class="px-3 py-1 bg-gray-100 hover:bg-gray-200 rounded disabled:opacity-40"
                            >
                                {{ $t('words.previous') || 'Previous' }}
                            </button>
                            <span>{{ conversationPage }} / {{ totalPages }}</span>
                            <button
                                @click="conversationPage++"
                                :disabled="conversationPage >= totalPages"
                                class="px-3 py-1 bg-gray-100 hover:bg-gray-200 rounded disabled:opacity-40"
                            >
                                {{ $t('words.next') || 'Next' }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Right Panel: Active Chat View -->
                <div class="flex-1 flex flex-col bg-white overflow-hidden">
                    <div v-if="!selectedConversation" class="flex-1 flex flex-col items-center justify-center text-gray-400 p-8 text-center space-y-3">
                        <ion-icon name="logo-whatsapp" class="w-16 h-16 text-gray-300"></ion-icon>
                        <p class="text-base font-medium">{{ $t('words.select-trainee') }}</p>
                        <button
                            @click="openNewChatModal"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium shadow"
                        >
                            {{ $t('words.new-chat') }}
                        </button>
                    </div>

                    <template v-else>
                        <!-- Chat Header -->
                        <div class="px-6 py-3.5 border-b bg-gray-50 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-green-600 text-white flex items-center justify-center font-bold text-base shadow-sm">
                                    {{ selectedConversation.trainee ? selectedConversation.trainee.name.charAt(0) : 'W' }}
                                </div>
                                <div>
                                    <div class="font-bold text-gray-800 text-sm">
                                        {{ selectedConversation.trainee ? selectedConversation.trainee.name : selectedConversation.phone }}
                                    </div>
                                    <div v-if="selectedConversation.trainee && selectedConversation.trainee.company_name" class="text-xs text-gray-600 font-medium">
                                        {{ selectedConversation.trainee.company_name }}
                                    </div>
                                    <div class="text-xs text-gray-500 flex items-center gap-2 mt-0.5">
                                        <span dir="ltr">{{ selectedConversation.phone }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <a
                                    v-if="selectedConversation.trainee"
                                    :href="selectedConversation.trainee.show_url"
                                    target="_blank"
                                    class="text-xs bg-white border border-gray-300 hover:bg-gray-100 px-3 py-1.5 rounded-lg font-medium text-gray-700 transition"
                                >
                                    {{ $t('words.profile') }}
                                </a>
                            </div>
                        </div>

                        <!-- Messages Container -->
                        <div ref="messagesContainer" class="flex-1 overflow-y-auto p-6 space-y-4 bg-[#efeae2]" style="max-height: 400px;">
                            <div v-if="loadingMessages" class="text-center text-sm text-gray-500 py-4">
                                {{ $t('words.loading') }}...
                            </div>

                            <div
                                v-for="message in messages"
                                :key="message.sid || message.date_sent + message.body"
                                class="flex"
                                :class="messageAlignmentClass(message)"
                            >
                                <!-- Internal Note Sticky Box -->
                                <div
                                    v-if="message.is_note"
                                    class="max-w-[85%] w-full bg-yellow-100 border-2 border-yellow-300 rounded-xl p-4 text-sm shadow-md text-yellow-950"
                                >
                                    <div class="flex items-center justify-between font-bold text-xs text-yellow-900 mb-1.5">
                                        <span class="flex items-center gap-1.5">
                                            <ion-icon name="document-text-outline" class="w-4 h-4 text-yellow-700"></ion-icon>
                                            {{ $t('words.internal-note') }}
                                        </span>
                                        <span v-if="message.author" class="bg-yellow-200 text-yellow-950 px-2 py-0.5 rounded text-[11px] font-semibold">
                                            👤 {{ message.author.name }}
                                        </span>
                                    </div>
                                    <p class="whitespace-pre-wrap break-words text-yellow-950 text-sm leading-relaxed" dir="auto">{{ message.body }}</p>
                                    <div class="text-[11px] text-yellow-800/80 mt-2 text-right font-medium" dir="ltr">
                                        {{ formatMessageTime(message.date_sent) }}
                                    </div>
                                </div>

                                <!-- Standard WhatsApp Message Bubble -->
                                <div
                                    v-else
                                    class="max-w-[80%] md:max-w-[70%] rounded-2xl px-4 py-3 text-sm shadow-sm"
                                    :class="message.status === 'delivery_failed' || message.status === 'failed'
                                        ? 'bg-red-100 text-red-950 rounded-tr-sm border border-red-300'
                                        : (isOutboundMessage(message)
                                            ? 'bg-green-100 text-gray-900 rounded-tr-sm border border-green-300'
                                            : 'bg-white text-gray-900 rounded-tl-sm border border-gray-200')"
                                >
                                    <p class="whitespace-pre-wrap break-words leading-relaxed" dir="auto">{{ message.body }}</p>
                                    
                                     <!-- Media Attachments -->
                                     <div v-if="message.metadata && message.metadata.media && message.metadata.media.length" class="mt-2 space-y-2">
                                         <div
                                             v-for="(media, mediaIndex) in message.metadata.media"
                                             :key="mediaIndex"
                                             class="flex flex-col gap-1 p-2 bg-black/5 rounded-lg border border-black/10"
                                         >
                                             <a
                                                 :href="media.url"
                                                 target="_blank"
                                                 class="text-xs underline font-medium text-blue-600 flex items-center gap-1"
                                             >
                                                 <ion-icon name="document-attach-outline" class="w-4 h-4"></ion-icon>
                                                 {{ media.content_type || $t('words.attachment') }}
                                             </a>
                                             <button
                                                 v-if="message.id"
                                                 @click="saveMediaToS3(message.id, media.url)"
                                                 class="self-start mt-1 text-[11px] bg-white border border-gray-300 hover:bg-gray-100 px-2 py-1 rounded font-semibold text-gray-700 shadow-sm transition"
                                             >
                                                 {{ $t('words.save-to-s3') }}
                                             </button>
                                         </div>
                                     </div>

                                     <!-- Saved to S3 Media List -->
                                     <div v-if="message.saved_media && message.saved_media.length" class="mt-2 space-y-1">
                                         <div class="text-[11px] font-bold text-green-700">✓ S3 Cloud Storage:</div>
                                         <a
                                             v-for="s3Media in message.saved_media"
                                             :key="s3Media.id"
                                             :href="s3Media.url"
                                             target="_blank"
                                             class="block text-xs underline text-green-800 font-medium"
                                         >
                                             {{ s3Media.name }}
                                         </a>
                                     </div>

                                    <!-- Author & Timestamp footer -->
                                    <div class="text-[11px] mt-1.5 flex items-center justify-between gap-3 text-gray-500 pt-1 border-t border-gray-200/40">
                                        <span class="font-medium text-[10px] text-gray-600 truncate max-w-[120px]">
                                            <span v-if="isOutboundMessage(message) && message.author">👤 {{ message.author.name }}</span>
                                            <span v-else-if="!isOutboundMessage(message)">📲 Trainee</span>
                                        </span>
                                        <span class="flex items-center gap-1" dir="ltr">
                                            <span>{{ formatMessageTime(message.date_sent) }}</span>
                                            <span v-if="message.status" class="capitalize">· {{ translateStatus(message.status) }}</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Composer / Action Bar -->
                        <div class="border-t p-4 bg-white">
                            <!-- Send Mode Tabs -->
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex gap-2">
                                    <button
                                        @click="sendMode = 'freeform'; isNoteMode = false;"
                                        class="px-3 py-1.5 rounded-lg text-xs font-semibold transition"
                                        :class="sendMode === 'freeform' && !isNoteMode ? 'bg-green-600 text-white shadow' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                    >
                                        {{ $t('words.message') }}
                                    </button>
                                    <button
                                        @click="sendMode = 'template'; isNoteMode = false;"
                                        class="px-3 py-1.5 rounded-lg text-xs font-semibold transition"
                                        :class="sendMode === 'template' && !isNoteMode ? 'bg-green-600 text-white shadow' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                    >
                                        {{ $t('words.whatsapp-templates') }}
                                    </button>
                                </div>

                                <!-- Internal Note Toggle Button -->
                                <button
                                    @click="toggleNoteMode"
                                    type="button"
                                    class="px-3 py-1.5 rounded-lg text-xs font-semibold flex items-center gap-1.5 transition shadow-sm"
                                    :class="isNoteMode ? 'bg-yellow-400 text-black border border-yellow-500' : 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200 border border-yellow-200'"
                                >
                                    <ion-icon name="create-outline" class="w-4 h-4"></ion-icon>
                                    {{ $t('words.internal-note') }}
                                </button>
                            </div>

                            <!-- Template Composer -->
                            <div v-if="sendMode === 'template' && !isNoteMode">
                                <div v-if="loadingTemplates" class="text-xs text-gray-500 mb-2">
                                    {{ $t('words.loading') }}...
                                </div>
                                <select
                                    v-model="selectedTemplateSid"
                                    @change="onTemplateChange"
                                    class="w-full form-select text-sm mb-3 rounded-lg border-gray-300"
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

                                <div v-if="selectedTemplate" class="mb-3 p-3 bg-gray-50 rounded-lg text-sm whitespace-pre-wrap border text-gray-800">
                                    {{ previewTemplateBody }}
                                </div>

                                <div v-if="selectedTemplate && selectedTemplate.variables.length" class="space-y-2 mb-3">
                                    <div class="text-xs font-medium text-gray-700">{{ $t('words.template-variables') }}</div>
                                    <div
                                        v-for="variableKey in selectedTemplate.variables"
                                        :key="variableKey"
                                    >
                                        <input
                                            v-model="templateVariables[variableKey]"
                                            type="text"
                                            class="w-full form-input text-xs rounded-lg"
                                            :placeholder="$t('words.template-variable') + ' ' + variableKey"
                                        />
                                    </div>
                                </div>

                                <button
                                    @click="sendTemplate"
                                    type="button"
                                    :disabled="sending || !selectedTemplateSid"
                                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-semibold shadow disabled:opacity-50"
                                >
                                    {{ $t('words.send-whatsapp-template') }}
                                </button>
                            </div>

                            <!-- Freeform Message or Internal Note Composer -->
                            <div v-else class="border-2 rounded-xl p-3 bg-white" :class="isNoteMode ? 'border-yellow-400 bg-yellow-50/30' : 'border-gray-200'">
                                <div class="relative">
                                    <textarea
                                        v-model="messageBody"
                                        rows="3"
                                        class="w-full text-sm rounded-lg transition-all p-3 border"
                                        :class="isNoteMode ? 'bg-yellow-100 border-yellow-300 focus:border-yellow-500 focus:ring-yellow-200 text-yellow-950 placeholder-yellow-800/60' : 'border-gray-300 focus:border-green-500 focus:ring-green-200'"
                                        :placeholder="isNoteMode ? $t('words.internal-note-hint') : $t('words.message') + '...'"
                                    ></textarea>
                                </div>

                                <div class="flex items-center justify-between mt-2">
                                    <p v-if="isNoteMode" class="text-xs text-yellow-800 font-medium flex items-center gap-1">
                                        <ion-icon name="information-circle-outline" class="w-4 h-4"></ion-icon>
                                        {{ $t('words.internal-note-hint') }}
                                    </p>
                                     <p v-else class="text-xs text-gray-400">{{ $t('words.press-send-whatsapp-hint') }}</p>

                                    <button
                                        @click="sendMessageOrNote"
                                        type="button"
                                        :disabled="sending || !messageBody.trim()"
                                        class="px-4 py-2 rounded-lg text-sm font-semibold text-white shadow disabled:opacity-50 transition"
                                        :class="isNoteMode ? 'bg-yellow-500 hover:bg-yellow-600 text-black font-bold' : 'bg-green-600 hover:bg-green-700'"
                                    >
                                        {{ isNoteMode ? $t('words.internal-note') : $t('words.send') }}
                                    </button>
                                </div>
                            </div>

                            <p v-if="errorMessage" class="mt-2 text-xs text-red-600 font-medium">{{ errorMessage }}</p>
                            <p v-if="successMessage" class="mt-2 text-xs text-green-600 font-medium">{{ successMessage }}</p>
                        </div>
                    </template>
                </div>

            </div>
        </div>

        <!-- New Chat Search Modal -->
        <portal-target name="new-chat-modal"></portal-target>
        <portal to="new-chat-modal">
            <modal name="newChatModal" :width="540" :height="'auto'" :scrollable="true">
                <div class="bg-white rounded-xl shadow-2xl p-6 flex flex-col max-h-[85vh]">
                    <div class="flex items-center justify-between pb-4 border-b mb-4">
                        <h3 class="text-lg font-bold text-gray-800">{{ $t('words.new-chat') }}</h3>
                        <button @click="$modal.hide('newChatModal')" class="text-gray-400 hover:text-gray-600">
                            <ion-icon name="close-outline" class="w-6 h-6"></ion-icon>
                        </button>
                    </div>

                    <p class="text-xs text-gray-500 mb-4">{{ $t('words.start-new-chat-hint') }}</p>

                    <div class="space-y-4 mb-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">{{ $t('words.phone-number') }}</label>
                            <input
                                v-model="newChatPhone"
                                type="text"
                                class="w-full form-input text-sm rounded-lg border-gray-300"
                                placeholder="+9665xxxxxxxx"
                                dir="ltr"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">{{ $t('words.whatsapp-templates') }}</label>
                            <select
                                v-model="newChatTemplateSid"
                                @change="onNewChatTemplateChange"
                                class="w-full form-select text-sm rounded-lg border-gray-300"
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
                        </div>

                        <div v-if="newChatTemplate" class="p-3 bg-gray-50 rounded-lg text-sm whitespace-pre-wrap border text-gray-800">
                            {{ previewNewChatTemplateBody }}
                        </div>

                        <div v-if="newChatTemplate && newChatTemplate.variables.length" class="space-y-2">
                            <div class="text-xs font-medium text-gray-700">{{ $t('words.template-variables') }}</div>
                            <div
                                v-for="variableKey in newChatTemplate.variables"
                                :key="variableKey"
                            >
                                <input
                                    v-model="newChatTemplateVariables[variableKey]"
                                    type="text"
                                    class="w-full form-input text-xs rounded-lg"
                                    :placeholder="$t('words.template-variable') + ' ' + variableKey"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t">
                        <button
                            @click="$modal.hide('newChatModal')"
                            class="px-4 py-2 rounded-lg text-sm font-semibold bg-gray-100 hover:bg-gray-200 text-gray-700 transition"
                        >
                            {{ $t('words.cancel') }}
                        </button>
                        <button
                            @click="sendNewChatTemplate"
                            :disabled="sendingNewChat || !newChatPhone.trim() || !newChatTemplateSid"
                            class="px-4 py-2 rounded-lg text-sm font-semibold bg-green-600 hover:bg-green-700 text-white shadow disabled:opacity-50 transition"
                        >
                            {{ $t('words.send') }}
                        </button>
                    </div>

                    <p v-if="newChatError" class="mt-2 text-xs text-red-600 font-medium">{{ newChatError }}</p>
                </div>
            </modal>
        </portal>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout';
import axios from 'axios';
import throttle from 'lodash/throttle';

export default {
    components: {
        AppLayout,
    },
    props: {
        configured: {
            type: Boolean,
            default: false,
        },
    },
        data() {
        return {
            conversations: [],
            conversationSearch: '',
            loadingConversations: false,
            selectedConversation: null,
            messages: [],
            loadingMessages: false,
            sendMode: 'freeform',
            isNoteMode: false,
            messageBody: '',
            templates: [],
            loadingTemplates: false,
            selectedTemplateSid: '',
            selectedTemplate: null,
            templateVariables: {},
            sending: false,
            errorMessage: '',
            successMessage: '',
            newChatPhone: '',
            newChatTemplateSid: '',
            newChatTemplate: null,
            newChatTemplateVariables: {},
            sendingNewChat: false,
            newChatError: '',
            conversationPage: 1,
            conversationsPerPage: 10,
            pollInterval: null,
        };
    },
    computed: {
        filteredConversations() {
            if (!this.conversationSearch) {
                return this.conversations;
            }
            const q = this.conversationSearch.toLowerCase();
            return this.conversations.filter((conv) => {
                const name = conv.trainee?.name?.toLowerCase() || '';
                const phone = conv.phone?.toLowerCase() || '';
                const idNum = conv.trainee?.identity_number?.toLowerCase() || '';
                return name.includes(q) || phone.includes(q) || idNum.includes(q);
            });
        },
        paginatedConversations() {
            const start = (this.conversationPage - 1) * this.conversationsPerPage;
            return this.filteredConversations.slice(start, start + this.conversationsPerPage);
        },
        totalPages() {
            return Math.ceil(this.filteredConversations.length / this.conversationsPerPage) || 1;
        },
        previewTemplateBody() {
            if (!this.selectedTemplate) {
                return '';
            }
            let body = this.selectedTemplate.body;
            Object.keys(this.templateVariables).forEach((key) => {
                const val = this.templateVariables[key] || `{{${key}}}`;
                body = body.replace(new RegExp(`\\{\\{${key}\\}\\}`, 'g'), val);
            });
            return body;
        },
        previewNewChatTemplateBody() {
            if (!this.newChatTemplate) {
                return '';
            }
            let body = this.newChatTemplate.body;
            Object.keys(this.newChatTemplateVariables).forEach((key) => {
                const val = this.newChatTemplateVariables[key] || `{{${key}}}`;
                body = body.replace(new RegExp(`\\{\\{${key}\\}\\}`, 'g'), val);
            });
            return body;
        },
    },
    mounted() {
        this.loadConversations();
        this.startPolling();
        this.subscribeEcho();
        if (this.configured) {
            this.loadTemplates();
        }
    },
    beforeDestroy() {
        this.unsubscribeEcho();
        this.stopPolling();
    },
    methods: {
        subscribeEcho() {
            if (!window.Echo) {
                return;
            }

            window.Echo.channel('whatsapp-chat')
                .listen('.WhatsAppMessageReceived', (event) => {
                    this.loadConversations();
                    const message = event.message;
                    if (
                        this.selectedConversation
                        && message
                        && this.normalizePhone(message.phone) === this.normalizePhone(this.selectedConversation.phone)
                    ) {
                        this.loadMessagesSilently();
                    }
                });
        },
        unsubscribeEcho() {
            if (window.Echo) {
                window.Echo.leave('whatsapp-chat');
            }
        },
        normalizePhone(phone) {
            return String(phone || '').replace(/\D+/g, '');
        },
        async loadConversations() {
            this.loadingConversations = true;
            try {
                const { data } = await axios.get(route('back.chat.conversations'));
                this.conversations = data.conversations;
            } catch (e) {
                this.conversations = [];
            } finally {
                this.loadingConversations = false;
            }
        },
        async selectConversation(conv) {
            this.selectedConversation = conv;
            this.errorMessage = '';
            this.successMessage = '';
            await this.loadMessages();
        },
        async loadMessages() {
            if (!this.selectedConversation) return;
            this.loadingMessages = true;
            try {
                const { data } = await axios.get(route('back.chat.messages'), {
                    params: { phone: this.selectedConversation.phone },
                });
                this.messages = data.messages;
                this.$nextTick(() => this.scrollToBottom());
            } catch (e) {
                this.messages = [];
            } finally {
                this.loadingMessages = false;
            }
        },
        async loadTemplates() {
            this.loadingTemplates = true;
            try {
                const { data } = await axios.get(route('back.chat.templates'));
                this.templates = data.templates;
            } catch (e) {
                this.templates = [];
            } finally {
                this.loadingTemplates = false;
            }
        },
        async onTemplateChange() {
            this.templateVariables = {};
            this.selectedTemplate = null;
            if (!this.selectedTemplateSid) return;
            try {
                const { data } = await axios.get(route('back.chat.templates.show', this.selectedTemplateSid));
                this.selectedTemplate = data.template;
                this.selectedTemplate.variables.forEach((key) => {
                    this.$set(this.templateVariables, key, '');
                });
            } catch (e) {
                this.errorMessage = 'Failed to load template details.';
            }
        },
        openNewChatModal() {
            this.newChatPhone = '';
            this.newChatTemplateSid = '';
            this.newChatTemplate = null;
            this.newChatTemplateVariables = {};
            this.newChatError = '';
            this.$modal.show('newChatModal');
        },
        async onNewChatTemplateChange() {
            this.newChatTemplateVariables = {};
            this.newChatTemplate = null;
            if (!this.newChatTemplateSid) return;
            try {
                const { data } = await axios.get(route('back.chat.templates.show', this.newChatTemplateSid));
                this.newChatTemplate = data.template;
                this.newChatTemplate.variables.forEach((key) => {
                    this.$set(this.newChatTemplateVariables, key, '');
                });
            } catch (e) {
                this.newChatError = 'Failed to load template details.';
            }
        },
        async sendNewChatTemplate() {
            if (!this.newChatPhone.trim() || !this.newChatTemplateSid) return;
            this.sendingNewChat = true;
            this.newChatError = '';

            try {
                const { data } = await axios.post(route('back.chat.send-template'), {
                    phone: this.newChatPhone.trim(),
                    content_sid: this.newChatTemplateSid,
                    content_variables: this.newChatTemplateVariables,
                });

                this.$modal.hide('newChatModal');
                await this.loadConversations();

                let conv = this.conversations.find((c) => c.phone === data.message.to || c.phone === this.newChatPhone.trim());
                if (!conv) {
                    conv = {
                        phone: this.newChatPhone.trim(),
                        trainee: null,
                        last_message: data.message,
                        updated_at: Date.now() / 1000,
                    };
                    this.conversations.unshift(conv);
                }
                this.selectConversation(conv);
            } catch (error) {
                this.newChatError = error.response?.data?.message || 'Failed to send template.';
            } finally {
                this.sendingNewChat = false;
            }
        },
        toggleNoteMode() {
            this.isNoteMode = !this.isNoteMode;
            this.errorMessage = '';
            this.successMessage = '';
        },
        async sendMessageOrNote() {
            if (!this.selectedConversation || !this.messageBody.trim()) return;
            this.sending = true;
            this.errorMessage = '';
            this.successMessage = '';

            try {
                let endpoint = route('back.chat.send-message');
                let payload = {
                    phone: this.selectedConversation.phone,
                    body: this.messageBody.trim(),
                    trainee_id: this.selectedConversation.trainee?.id || null,
                };

                if (this.isNoteMode) {
                    endpoint = route('back.chat.send-note');
                }

                const { data } = await axios.post(endpoint, payload);
                this.messages.push(data.message);
                this.messageBody = '';
                this.successMessage = this.isNoteMode ? 'Internal note added.' : 'Message sent successfully.';
                this.$nextTick(() => this.scrollToBottom());
                this.loadConversations();
            } catch (error) {
                this.errorMessage = error.response?.data?.message || 'Failed to send message.';
            } finally {
                this.sending = false;
            }
        },
        async sendTemplate() {
            if (!this.selectedConversation || !this.selectedTemplateSid) return;
            this.sending = true;
            this.errorMessage = '';
            this.successMessage = '';

            try {
                const { data } = await axios.post(route('back.chat.send-template'), {
                    phone: this.selectedConversation.phone,
                    content_sid: this.selectedTemplateSid,
                    content_variables: this.templateVariables,
                    trainee_id: this.selectedConversation.trainee?.id || null,
                });
                this.messages.push(data.message);
                this.successMessage = 'WhatsApp template sent successfully.';
                this.$nextTick(() => this.scrollToBottom());
                this.loadConversations();
            } catch (error) {
                this.errorMessage = error.response?.data?.message || 'Failed to send template.';
            } finally {
                this.sending = false;
            }
        },
        startPolling() {
            // Polling disabled in favor of WebSockets/Soketi broadcast
        },
        stopPolling() {
            if (this.pollInterval) {
                clearInterval(this.pollInterval);
                this.pollInterval = null;
            }
        },
        async loadMessagesSilently() {
            if (!this.selectedConversation) return;
            try {
                const { data } = await axios.get(route('back.chat.messages'), {
                    params: { phone: this.selectedConversation.phone },
                });
                if (data.messages.length !== this.messages.length) {
                    this.messages = data.messages;
                    this.$nextTick(() => this.scrollToBottom());
                }
            } catch (e) {}
        },
        isOutboundMessage(message) {
            return ['outbound-api', 'outbound-reply', 'outbound'].includes(message.direction);
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
        scrollToBottom() {
            const container = this.$refs.messagesContainer;
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        },
        formatMessageTime(dateString) {
            if (!dateString) return '';
            return new Date(dateString).toLocaleString();
        },
        formatTimeShort(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        },
        translateStatus(status) {
            if (!status) return '';
            const key = status.toLowerCase().trim();
            if (key === 'delivered') return this.$t('words.delivered');
            if (key === 'queued') return this.$t('words.queued');
            if (key === 'received') return this.$t('words.received');
            if (key === 'sent') return this.$t('words.sent');
            return status;
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
