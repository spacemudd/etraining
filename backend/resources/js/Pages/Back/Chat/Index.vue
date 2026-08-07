<template>
    <app-layout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $t('words.chat') }}
                </h2>
                <div class="flex items-center gap-3">
                    <whats-app-templates-manager
                        v-if="configured"
                        :can-manage="canManageTemplates"
                        :list-route="route('back.chat.templates')"
                        :store-route="route('back.chat.templates.store')"
                        :update-route-template="route('back.chat.templates.update', { contentSid: '__SID__' })"
                        :destroy-route-template="route('back.chat.templates.destroy', { contentSid: '__SID__' })"
                        @templates-updated="onTemplatesManaged"
                    />
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
                    <div class="p-3 border-b bg-white space-y-2">
                        <div class="flex gap-1 p-0.5 bg-gray-100 rounded-lg">
                            <button
                                type="button"
                                @click="setStatusTab('open')"
                                class="flex-1 px-2 py-1.5 rounded-md text-[11px] font-semibold transition"
                                :class="statusTab === 'open' ? 'bg-white text-green-700 shadow' : 'text-gray-600 hover:text-gray-800'"
                            >
                                {{ $t('words.chat-status-open') }}
                                <span class="opacity-70" dir="ltr">({{ conversationCounts.open }})</span>
                            </button>
                            <button
                                type="button"
                                @click="setStatusTab('pending')"
                                class="flex-1 px-2 py-1.5 rounded-md text-[11px] font-semibold transition"
                                :class="statusTab === 'pending' ? 'bg-white text-amber-700 shadow' : 'text-gray-600 hover:text-gray-800'"
                            >
                                {{ $t('words.chat-status-pending') }}
                                <span class="opacity-70" dir="ltr">({{ conversationCounts.pending }})</span>
                            </button>
                            <button
                                type="button"
                                @click="setStatusTab('closed')"
                                class="flex-1 px-2 py-1.5 rounded-md text-[11px] font-semibold transition"
                                :class="statusTab === 'closed' ? 'bg-white text-gray-800 shadow' : 'text-gray-600 hover:text-gray-800'"
                            >
                                {{ $t('words.chat-status-closed') }}
                                <span class="opacity-70" dir="ltr">({{ conversationCounts.closed }})</span>
                            </button>
                        </div>
                        <input
                            v-model="conversationSearch"
                            @input="onSearchInput"
                            type="text"
                            class="w-full form-input text-sm rounded-lg border-gray-300"
                            :placeholder="$t('words.search') + '...'"
                        />
                        <div class="flex flex-wrap gap-1">
                            <button
                                type="button"
                                @click="setFilter('all')"
                                class="px-2 py-1 rounded text-[11px] font-semibold"
                                :class="listFilter === 'all' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                            >
                                {{ $t('words.all') }}
                            </button>
                            <button
                                type="button"
                                @click="setFilter('mine')"
                                class="px-2 py-1 rounded text-[11px] font-semibold"
                                :class="listFilter === 'mine' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                            >
                                {{ $t('words.chat-filter-mine') }}
                            </button>
                            <button
                                type="button"
                                @click="setFilter('unassigned')"
                                class="px-2 py-1 rounded text-[11px] font-semibold"
                                :class="listFilter === 'unassigned' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                            >
                                {{ $t('words.chat-filter-unassigned') }}
                                <span dir="ltr">({{ conversationCounts.unassigned }})</span>
                            </button>
                        </div>
                        <select
                            v-model="selectedTagFilter"
                            @change="reloadConversationsFromStart"
                            class="w-full form-select text-xs rounded-lg border-gray-300"
                        >
                            <option value="">{{ $t('words.chat-filter-all-tags') }}</option>
                            <option v-for="tag in availableTags" :key="tag.id" :value="tag.id">
                                {{ tag.name }}
                            </option>
                        </select>
                    </div>

                    <div class="overflow-y-auto flex-1 divide-y divide-gray-100 flex flex-col justify-between">
                        <div>
                            <div v-if="loadingConversations" class="p-4 text-center text-sm text-gray-500">
                                {{ $t('words.loading') }}...
                            </div>
                            <div v-else-if="conversations.length === 0" class="p-6 text-center text-sm text-gray-500">
                                {{ $t('words.no-results') }}
                            </div>
                            <div
                                v-for="conv in conversations"
                                :key="conv.id || conv.phone"
                                @click="selectConversation(conv)"
                                class="p-4 cursor-pointer hover:bg-green-50 transition-colors flex flex-col gap-1"
                                :class="{ 'bg-green-100 border-l-4 border-green-600': selectedConversation && selectedConversation.phone === conv.phone }"
                            >
                                <div class="flex items-center justify-between gap-2">
                                    <span class="font-semibold text-sm text-gray-900 truncate">
                                        <template v-if="conv.trainee">{{ conv.trainee.name }}</template>
                                        <span v-else dir="ltr">{{ conv.phone }}</span>
                                    </span>
                                    <span class="text-[11px] text-gray-400 flex-shrink-0" dir="ltr">
                                        {{ formatTimeShort(conv.last_message && conv.last_message.sent_at) }}
                                    </span>
                                </div>
                                <div v-if="conv.trainee && conv.trainee.company_name" class="text-xs text-gray-600 font-medium">
                                    {{ conv.trainee.company_name }}
                                </div>
                                <div v-if="conv.trainee" class="text-xs text-gray-500" dir="ltr">
                                    {{ conv.phone }}
                                </div>
                                <div class="flex items-center justify-between text-xs text-gray-500 gap-2">
                                    <span class="truncate max-w-[180px]">
                                        <span v-if="conv.last_message && conv.last_message.is_note" class="text-yellow-700 font-medium">[{{ $t('words.internal-note') }}]: </span>
                                        {{ conv.last_message && conv.last_message.body }}
                                    </span>
                                </div>
                                <div v-if="(conv.agents && conv.agents.length) || (conv.tags && conv.tags.length)" class="flex flex-wrap items-center gap-1 mt-1">
                                    <span
                                        v-for="agent in (conv.agents || [])"
                                        :key="'a-' + agent.id"
                                        class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-indigo-100 text-indigo-800 text-[10px] font-bold border border-indigo-200"
                                        :title="agent.name"
                                    >
                                        {{ agentInitials(agent.name) }}
                                    </span>
                                    <span
                                        v-for="tag in (conv.tags || [])"
                                        :key="'t-' + tag.id"
                                        class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-semibold bg-gray-200 text-gray-700"
                                        :style="tag.color ? { backgroundColor: tag.color, color: '#fff' } : null"
                                    >
                                        {{ tag.name }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Pagination Controls -->
                        <div v-if="totalPages > 1" class="p-3 border-t bg-white flex items-center justify-between text-xs text-gray-600">
                            <button
                                @click="goToPage(conversationPage - 1)"
                                :disabled="conversationPage === 1 || loadingConversations"
                                class="px-3 py-1 bg-gray-100 hover:bg-gray-200 rounded disabled:opacity-40"
                            >
                                {{ $t('words.previous') }}
                            </button>
                            <span dir="ltr">{{ conversationPage }} / {{ totalPages }}</span>
                            <button
                                @click="goToPage(conversationPage + 1)"
                                :disabled="conversationPage >= totalPages || loadingConversations"
                                class="px-3 py-1 bg-gray-100 hover:bg-gray-200 rounded disabled:opacity-40"
                            >
                                {{ $t('words.next') }}
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
                        <div class="px-6 py-3.5 border-b bg-gray-50 space-y-2">
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-10 h-10 rounded-full bg-green-600 text-white flex items-center justify-center font-bold text-base shadow-sm flex-shrink-0">
                                        {{ selectedConversation.trainee ? selectedConversation.trainee.name.charAt(0) : 'W' }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-bold text-gray-800 text-sm truncate">
                                            {{ selectedConversation.trainee ? selectedConversation.trainee.name : selectedConversation.phone }}
                                        </div>
                                        <div v-if="selectedConversation.trainee && selectedConversation.trainee.company_name" class="text-xs text-gray-600 font-medium truncate">
                                            {{ selectedConversation.trainee.company_name }}
                                        </div>
                                        <div class="text-xs text-gray-500 flex items-center gap-2 mt-0.5">
                                            <span dir="ltr">{{ selectedConversation.phone }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    <select
                                        :value="selectedConversation.status || 'open'"
                                        @change="onStatusChange($event)"
                                        :disabled="updatingStatus"
                                        class="form-select text-xs rounded-lg border-gray-300 py-1.5"
                                    >
                                        <option value="open">{{ $t('words.chat-status-open') }}</option>
                                        <option value="pending">{{ $t('words.chat-status-pending') }}</option>
                                        <option value="closed">{{ $t('words.chat-status-closed') }}</option>
                                    </select>
                                    <button
                                        type="button"
                                        @click="toggleAssignMe"
                                        :disabled="assigningAgent"
                                        class="text-xs px-3 py-1.5 rounded-lg font-medium border transition disabled:opacity-50"
                                        :class="selectedConversation.is_assigned_to_me
                                            ? 'bg-indigo-600 text-white border-indigo-600 hover:bg-indigo-700'
                                            : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-100'"
                                    >
                                        {{ selectedConversation.is_assigned_to_me ? $t('words.chat-unassign-me') : $t('words.chat-assign-me') }}
                                    </button>
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

                            <div class="flex flex-wrap items-center gap-2">
                                <span
                                    v-for="agent in (selectedConversation.agents || [])"
                                    :key="'ha-' + agent.id"
                                    class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-800 text-[11px] font-semibold border border-indigo-100"
                                    :title="agent.name"
                                >
                                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-indigo-100 text-[10px] font-bold">
                                        {{ agentInitials(agent.name) }}
                                    </span>
                                    {{ agent.name }}
                                </span>
                                <span
                                    v-for="tag in (selectedConversation.tags || [])"
                                    :key="'ht-' + tag.id"
                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-gray-200 text-gray-700"
                                    :style="tag.color ? { backgroundColor: tag.color, color: '#fff' } : null"
                                >
                                    {{ tag.name }}
                                    <button type="button" class="opacity-80 hover:opacity-100" @click="detachTag(tag)">×</button>
                                </span>
                                <div class="inline-flex items-center gap-1">
                                    <select
                                        v-model="tagToAttach"
                                        class="form-select text-xs rounded-lg border-gray-300 py-1"
                                    >
                                        <option value="">{{ $t('words.chat-add-tag') }}</option>
                                        <option v-for="tag in availableTags" :key="'pick-' + tag.id" :value="tag.id">
                                            {{ tag.name }}
                                        </option>
                                    </select>
                                    <button
                                        type="button"
                                        @click="attachSelectedTag"
                                        :disabled="!tagToAttach || attachingTag"
                                        class="text-xs bg-white border border-gray-300 px-2 py-1 rounded-lg font-medium disabled:opacity-40"
                                    >
                                        {{ $t('words.add') }}
                                    </button>
                                    <button
                                        type="button"
                                        @click="createAndAttachTag"
                                        :disabled="attachingTag"
                                        class="text-xs bg-white border border-gray-300 px-2 py-1 rounded-lg font-medium disabled:opacity-40"
                                    >
                                        {{ $t('words.chat-new-tag') }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Messages Container -->
                        <div ref="messagesContainer" @scroll="onMessagesScroll" class="flex-1 overflow-y-auto p-6 space-y-4 bg-[#efeae2]" style="max-height: 400px;">
                            <div v-if="hasMoreMessages" class="text-center mb-2">
                                <button
                                    type="button"
                                    @click="loadOlderMessages"
                                    :disabled="loadingOlderMessages"
                                    class="text-xs bg-white border border-gray-300 px-3 py-1.5 rounded-lg font-medium text-gray-700 disabled:opacity-50"
                                >
                                    {{ loadingOlderMessages ? ($t('words.loading') + '...') : $t('words.chat-load-older') }}
                                </button>
                            </div>
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
                                    <p
                                        v-if="message.body && message.body !== '[Media Attachment]'"
                                        class="whitespace-pre-wrap break-words leading-relaxed"
                                        dir="auto"
                                    >{{ message.body }}</p>

                                    <!-- Media Attachments (inline when possible) -->
                                    <div v-if="messageAttachments(message).length" class="mt-2 space-y-2">
                                        <div
                                            v-for="(media, mediaIndex) in messageAttachments(message)"
                                            :key="media.id || media.url || mediaIndex"
                                        >
                                            <a
                                                v-if="isImageAttachment(media)"
                                                :href="media.url"
                                                target="_blank"
                                                class="block"
                                            >
                                                <img
                                                    :src="media.url"
                                                    :alt="media.name || $t('words.attachment')"
                                                    class="max-w-full max-h-64 rounded-lg object-contain bg-black/5"
                                                    loading="lazy"
                                                />
                                            </a>
                                            <video
                                                v-else-if="isVideoAttachment(media)"
                                                :src="media.url"
                                                controls
                                                class="max-w-full max-h-64 rounded-lg bg-black"
                                            ></video>
                                            <audio
                                                v-else-if="isAudioAttachment(media)"
                                                :src="media.url"
                                                controls
                                                class="w-full"
                                            ></audio>
                                            <a
                                                v-else
                                                :href="media.url"
                                                target="_blank"
                                                class="inline-flex items-center gap-1.5 text-sm font-medium text-blue-700 underline"
                                            >
                                                <ion-icon name="document-attach-outline" class="w-4 h-4"></ion-icon>
                                                {{ media.name || $t('words.attachment') }}
                                            </a>
                                        </div>
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
import WhatsAppTemplatesManager from '@/Components/WhatsAppTemplatesManager';
import axios from 'axios';
import throttle from 'lodash/throttle';

export default {
    components: {
        AppLayout,
        WhatsAppTemplatesManager,
    },
    props: {
        configured: {
            type: Boolean,
            default: false,
        },
        canManageTemplates: {
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
            loadingOlderMessages: false,
            hasMoreMessages: false,
            nextBefore: null,
            nextBeforeId: null,
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
            totalPages: 1,
            totalConversations: 0,
            conversationCounts: {
                open: 0,
                pending: 0,
                closed: 0,
                unassigned: 0,
            },
            listFilter: 'all',
            statusTab: 'open',
            selectedTagFilter: '',
            availableTags: [],
            tagToAttach: '',
            assigningAgent: false,
            attachingTag: false,
            updatingStatus: false,
            pollInterval: null,
            searchDebounce: null,
        };
    },
    computed: {
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
        this.loadTags();
        this.loadConversations();
        this.subscribeEcho();
        if (this.configured) {
            this.loadTemplates();
        }
    },
    beforeDestroy() {
        this.unsubscribeEcho();
        this.stopPolling();
        if (this.searchDebounce) {
            clearTimeout(this.searchDebounce);
        }
    },
    methods: {
        conversationParams() {
            const params = {
                page: this.conversationPage,
                status: this.statusTab || 'open',
            };
            if (this.conversationSearch.trim()) {
                params.q = this.conversationSearch.trim();
            }
            if (this.listFilter === 'mine') {
                params.mine = 1;
            }
            if (this.listFilter === 'unassigned') {
                params.unassigned = 1;
            }
            if (this.selectedTagFilter) {
                params.tag_id = this.selectedTagFilter;
            }
            return params;
        },
        setStatusTab(status) {
            this.statusTab = status;
            this.selectedConversation = null;
            this.messages = [];
            this.reloadConversationsFromStart();
        },
        setFilter(filter) {
            this.listFilter = filter;
            this.reloadConversationsFromStart();
        },
        onSearchInput() {
            if (this.searchDebounce) {
                clearTimeout(this.searchDebounce);
            }
            this.searchDebounce = setTimeout(() => {
                this.reloadConversationsFromStart();
            }, 300);
        },
        reloadConversationsFromStart() {
            this.conversationPage = 1;
            this.loadConversations();
        },
        goToPage(page) {
            if (page < 1 || page > this.totalPages) {
                return;
            }
            this.conversationPage = page;
            this.loadConversations();
        },
        agentInitials(name) {
            const parts = String(name || '').trim().split(/\s+/).filter(Boolean);
            if (!parts.length) {
                return '?';
            }
            if (parts.length === 1) {
                return parts[0].slice(0, 2).toUpperCase();
            }
            return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
        },
        patchConversation(updated) {
            if (!updated || !updated.id) {
                return;
            }
            const currentUserId = this.$page && this.$page.props && this.$page.props.user
                ? this.$page.props.user.id
                : null;
            const merged = {
                ...updated,
                status: updated.status || 'open',
                is_assigned_to_me: currentUserId
                    ? (updated.agents || []).some((agent) => agent.id === currentUserId)
                    : false,
                is_unassigned: !(updated.agents && updated.agents.length),
            };

            const matchesTab = (merged.status || 'open') === this.statusTab;
            const index = this.conversations.findIndex((c) => c.id === merged.id || c.phone === merged.phone);

            if (!matchesTab) {
                if (index !== -1) {
                    this.conversations.splice(index, 1);
                }
                if (this.selectedConversation && (this.selectedConversation.id === merged.id || this.selectedConversation.phone === merged.phone)) {
                    this.selectedConversation = { ...this.selectedConversation, ...merged };
                }
                return;
            }

            if (index !== -1) {
                this.$set(this.conversations, index, { ...this.conversations[index], ...merged });
            } else {
                this.conversations.unshift(merged);
            }

            if (this.selectedConversation && (this.selectedConversation.id === merged.id || this.selectedConversation.phone === merged.phone)) {
                this.selectedConversation = { ...this.selectedConversation, ...merged };
            }
        },
        async onStatusChange(event) {
            if (!this.selectedConversation || !this.selectedConversation.id) {
                return;
            }
            const status = event.target.value;
            if (!status || status === this.selectedConversation.status) {
                return;
            }
            this.updatingStatus = true;
            try {
                const { data } = await axios.patch(
                    route('back.chat.conversations.status', this.selectedConversation.id),
                    { status }
                );
                this.patchConversation(data.conversation);
                if ((data.conversation.status || 'open') !== this.statusTab) {
                    this.selectedConversation = null;
                    this.messages = [];
                    await this.loadConversations();
                } else {
                    await this.refreshConversationCounts();
                }
            } catch (error) {
                this.errorMessage = error.response?.data?.message || this.$t('words.chat-status-failed');
                event.target.value = this.selectedConversation.status || 'open';
            } finally {
                this.updatingStatus = false;
            }
        },
        subscribeEcho() {
            if (!window.Echo) {
                console.warn('[Chat] Echo unavailable — falling back to polling');
                this.startPolling();
                return;
            }

            console.log('[Chat] Subscribing to channel whatsapp-chat');

            window.Echo.channel('whatsapp-chat')
                .listen('.WhatsAppMessageReceived', (event) => {
                    console.log('[Chat] WhatsAppMessageReceived', event && event.message);
                    this.loadConversations();
                    const message = event.message;
                    if (
                        this.selectedConversation
                        && message
                        && this.normalizePhone(message.phone) === this.normalizePhone(this.selectedConversation.phone)
                    ) {
                        console.log('[Chat] Appending message to open conversation');
                        this.mergeIncomingMessage(message);
                    }
                })
                .listen('.WhatsAppConversationUpdated', (event) => {
                    console.log('[Chat] WhatsAppConversationUpdated', event && event.conversation);
                    if (event && event.conversation) {
                        this.patchConversation(event.conversation);
                        this.refreshConversationCounts();
                    } else {
                        this.loadConversations();
                    }
                });
        },
        unsubscribeEcho() {
            if (window.Echo) {
                console.log('[Chat] Leaving channel whatsapp-chat');
                window.Echo.leave('whatsapp-chat');
            }
        },
        normalizePhone(phone) {
            return String(phone || '').replace(/\D+/g, '');
        },
        mergeIncomingMessage(message) {
            if (!message) {
                return;
            }

            const messageKeys = this.messageIdentityKeys(message);
            const existingIndex = this.messages.findIndex((item) => {
                const itemKeys = this.messageIdentityKeys(item);
                return messageKeys.some((key) => itemKeys.includes(key));
            });

            if (existingIndex !== -1) {
                this.$set(this.messages, existingIndex, { ...this.messages[existingIndex], ...message });
                return;
            }

            this.messages.push(message);
            this.$nextTick(() => this.scrollToBottom());
        },
        messageIdentityKeys(message) {
            return [message.sid, message.id]
                .filter(Boolean)
                .map((value) => String(value));
        },
        async loadTags() {
            try {
                const { data } = await axios.get(route('back.chat.tags'));
                this.availableTags = data.tags || [];
            } catch (e) {
                this.availableTags = [];
            }
        },
        async loadConversations() {
            this.loadingConversations = true;
            try {
                const { data } = await axios.get(route('back.chat.conversations'), {
                    params: this.conversationParams(),
                });
                this.conversations = data.data || [];
                this.conversationPage = data.current_page || 1;
                this.totalPages = data.last_page || 1;
                this.totalConversations = data.total || 0;
                this.applyConversationCounts(data.counts);
            } catch (e) {
                this.conversations = [];
                this.totalPages = 1;
                this.totalConversations = 0;
            } finally {
                this.loadingConversations = false;
            }
        },
        applyConversationCounts(counts) {
            if (!counts || typeof counts !== 'object') {
                return;
            }
            this.conversationCounts = {
                open: Number(counts.open) || 0,
                pending: Number(counts.pending) || 0,
                closed: Number(counts.closed) || 0,
                unassigned: Number(counts.unassigned) || 0,
            };
        },
        async refreshConversationCounts() {
            try {
                const { data } = await axios.get(route('back.chat.conversations'), {
                    params: {
                        ...this.conversationParams(),
                        page: 1,
                    },
                });
                this.applyConversationCounts(data.counts);
            } catch (e) {
                // keep current counts
            }
        },
        async selectConversation(conv) {
            this.selectedConversation = conv;
            this.errorMessage = '';
            this.successMessage = '';
            this.tagToAttach = '';
            await this.loadMessages();
        },
        async loadMessages() {
            if (!this.selectedConversation) return;
            this.loadingMessages = true;
            this.hasMoreMessages = false;
            this.nextBefore = null;
            this.nextBeforeId = null;
            try {
                const { data } = await axios.get(route('back.chat.messages'), {
                    params: {
                        phone: this.selectedConversation.phone,
                        limit: 50,
                    },
                });
                this.messages = data.messages || [];
                this.hasMoreMessages = !!data.has_more;
                this.nextBefore = data.next_before || null;
                this.nextBeforeId = data.next_before_id || null;
                this.$nextTick(() => this.scrollToBottom());
            } catch (e) {
                this.messages = [];
            } finally {
                this.loadingMessages = false;
            }
        },
        async loadOlderMessages() {
            if (!this.selectedConversation || !this.hasMoreMessages || this.loadingOlderMessages) {
                return;
            }
            this.loadingOlderMessages = true;
            const container = this.$refs.messagesContainer;
            const previousHeight = container ? container.scrollHeight : 0;
            try {
                const { data } = await axios.get(route('back.chat.messages'), {
                    params: {
                        phone: this.selectedConversation.phone,
                        limit: 50,
                        before: this.nextBefore,
                        before_id: this.nextBeforeId,
                    },
                });
                const older = data.messages || [];
                this.messages = [...older, ...this.messages];
                this.hasMoreMessages = !!data.has_more;
                this.nextBefore = data.next_before || null;
                this.nextBeforeId = data.next_before_id || null;
                this.$nextTick(() => {
                    if (container) {
                        container.scrollTop = container.scrollHeight - previousHeight;
                    }
                });
            } catch (e) {
                // keep current messages
            } finally {
                this.loadingOlderMessages = false;
            }
        },
        onMessagesScroll() {
            const container = this.$refs.messagesContainer;
            if (!container || !this.hasMoreMessages || this.loadingOlderMessages) {
                return;
            }
            if (container.scrollTop <= 40) {
                this.loadOlderMessages();
            }
        },
        async toggleAssignMe() {
            if (!this.selectedConversation || !this.selectedConversation.id) {
                return;
            }
            this.assigningAgent = true;
            try {
                const routeName = this.selectedConversation.is_assigned_to_me
                    ? 'back.chat.conversations.agents.unassign'
                    : 'back.chat.conversations.agents.assign';
                const method = this.selectedConversation.is_assigned_to_me ? 'delete' : 'post';
                const { data } = await axios[method](route(routeName, this.selectedConversation.id));
                this.patchConversation(data.conversation);
                await this.refreshConversationCounts();
            } catch (error) {
                this.errorMessage = error.response?.data?.message || this.$t('words.chat-assign-failed');
            } finally {
                this.assigningAgent = false;
            }
        },
        async attachSelectedTag() {
            if (!this.selectedConversation || !this.selectedConversation.id || !this.tagToAttach) {
                return;
            }
            this.attachingTag = true;
            try {
                const { data } = await axios.post(
                    route('back.chat.conversations.tags.attach', this.selectedConversation.id),
                    { tag_id: this.tagToAttach }
                );
                this.patchConversation(data.conversation);
                this.tagToAttach = '';
                if (data.tag && !this.availableTags.find((t) => t.id === data.tag.id)) {
                    this.availableTags.push(data.tag);
                }
            } catch (error) {
                this.errorMessage = error.response?.data?.message || this.$t('words.chat-tag-failed');
            } finally {
                this.attachingTag = false;
            }
        },
        async createAndAttachTag() {
            if (!this.selectedConversation || !this.selectedConversation.id) {
                return;
            }
            const name = window.prompt(this.$t('words.chat-new-tag-prompt'));
            if (!name || !name.trim()) {
                return;
            }
            this.attachingTag = true;
            try {
                const { data } = await axios.post(
                    route('back.chat.conversations.tags.attach', this.selectedConversation.id),
                    { name: name.trim() }
                );
                this.patchConversation(data.conversation);
                if (data.tag && !this.availableTags.find((t) => t.id === data.tag.id)) {
                    this.availableTags.push(data.tag);
                    this.availableTags.sort((a, b) => a.name.localeCompare(b.name));
                }
            } catch (error) {
                this.errorMessage = error.response?.data?.message || this.$t('words.chat-tag-failed');
            } finally {
                this.attachingTag = false;
            }
        },
        async detachTag(tag) {
            if (!this.selectedConversation || !this.selectedConversation.id || !tag) {
                return;
            }
            try {
    const { data } = await axios.delete(
                    route('back.chat.conversations.tags.detach', {
                        conversation: this.selectedConversation.id,
                        tag: tag.id,
                    })
                );
                this.patchConversation(data.conversation);
            } catch (error) {
                this.errorMessage = error.response?.data?.message || this.$t('words.chat-tag-failed');
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
        onTemplatesManaged(templates) {
            this.templates = templates || [];
            if (this.selectedTemplateSid && !this.templates.some((t) => t.sid === this.selectedTemplateSid)) {
                this.selectedTemplateSid = '';
                this.selectedTemplate = null;
                this.templateVariables = {};
            }
            if (this.newChatTemplateSid && !this.templates.some((t) => t.sid === this.newChatTemplateSid)) {
                this.newChatTemplateSid = '';
                this.newChatTemplate = null;
                this.newChatTemplateVariables = {};
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
                await axios.post(route('back.chat.send-template'), {
                    phone: this.newChatPhone.trim(),
                    content_sid: this.newChatTemplateSid,
                    content_variables: this.newChatTemplateVariables,
                });

                this.$modal.hide('newChatModal');
                await this.reloadConversationsFromStart();

                const normalized = this.normalizePhone(this.newChatPhone.trim());
                let conv = this.conversations.find((c) => this.normalizePhone(c.phone) === normalized);
                if (conv) {
                    this.selectConversation(conv);
                }
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
                this.mergeIncomingMessage(data.message);
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
                this.mergeIncomingMessage(data.message);
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
            this.stopPolling();
            this.pollInterval = setInterval(() => {
                this.loadConversations();
                if (this.selectedConversation) {
                    this.loadMessagesSilently();
                }
            }, 5000);
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
                    params: {
                        phone: this.selectedConversation.phone,
                        limit: 50,
                    },
                });
                if ((data.messages || []).length !== this.messages.length) {
                    this.messages = data.messages || [];
                    this.hasMoreMessages = !!data.has_more;
                    this.nextBefore = data.next_before || null;
                    this.nextBeforeId = data.next_before_id || null;
                    this.$nextTick(() => this.scrollToBottom());
                }
            } catch (e) {}
        },
        isOutboundMessage(message) {
            return ['outbound-api', 'outbound-reply', 'outbound'].includes(message.direction);
        },
        messageAttachments(message) {
            if (!message) {
                return [];
            }

            if (message.saved_media && message.saved_media.length) {
                return message.saved_media.map((media) => ({
                    id: media.id,
                    url: media.url,
                    name: media.name,
                    content_type: this.guessMediaType(media.name || media.url),
                }));
            }

            const raw = (message.metadata && message.metadata.media) || [];
            return raw
                .filter((media) => media && media.url)
                .map((media, index) => ({
                    id: media.id || `meta-${index}`,
                    url: media.url,
                    name: media.name || null,
                    content_type: media.content_type || media.mime_type || this.guessMediaType(media.url),
                }));
        },
        guessMediaType(value) {
            const source = String(value || '').toLowerCase();
            if (/\.(jpe?g|png|gif|webp|bmp|svg)(\?|$)/.test(source)) {
                return 'image/*';
            }
            if (/\.(mp4|webm|mov|m4v)(\?|$)/.test(source)) {
                return 'video/*';
            }
            if (/\.(mp3|ogg|wav|m4a|aac)(\?|$)/.test(source)) {
                return 'audio/*';
            }
            return '';
        },
        isImageAttachment(media) {
            const type = String((media && media.content_type) || '').toLowerCase();
            return type.startsWith('image/') || this.guessMediaType(media && (media.url || media.name)).startsWith('image/');
        },
        isVideoAttachment(media) {
            const type = String((media && media.content_type) || '').toLowerCase();
            return type.startsWith('video/') || this.guessMediaType(media && (media.url || media.name)).startsWith('video/');
        },
        isAudioAttachment(media) {
            const type = String((media && media.content_type) || '').toLowerCase();
            return type.startsWith('audio/') || this.guessMediaType(media && (media.url || media.name)).startsWith('audio/');
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
