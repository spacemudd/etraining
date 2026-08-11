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
            v-if="!hideTrigger"
            @click="open"
            :class="triggerClass || 'col-span-1 bg-green-500 shadow-lg rounded-lg p-5 transition-all duration-500 ease-in-out hover:bg-green-600 text-center text-white font-semibold flex items-center justify-center gap-2'"
        >
            <ion-icon name="logo-whatsapp" class="w-6 h-6"></ion-icon>
            {{ triggerLabel || $t('words.whatsapp') }}
        </button>

        <portal-target :name="portalName"></portal-target>
        <portal :to="portalName">
            <modal :name="modalName" :width="960" :height="'auto'" :scrollable="true">
                <div class="bg-white rounded-lg max-h-[90vh] flex flex-col">
                    <div class="px-5 py-4 border-b flex items-center justify-between bg-green-600 text-white rounded-t-lg">
                        <div class="flex items-center gap-2">
                            <ion-icon name="logo-whatsapp" class="w-6 h-6"></ion-icon>
                            <h1 class="text-lg font-bold">{{ $t('words.whatsapp-chat') }}</h1>
                        </div>
                        <div class="flex items-center gap-2">
                            <button
                                v-if="configured && !lockTrainee"
                                type="button"
                                @click="openNewChatModal"
                                class="text-xs bg-white/15 hover:bg-white/25 border border-white/30 text-white px-3 py-1.5 rounded-lg font-medium transition"
                            >
                                {{ $t('words.new-chat') }}
                            </button>
                            <button @click="close" class="text-white hover:text-green-100">
                                <ion-icon name="close" class="w-6 h-6"></ion-icon>
                            </button>
                        </div>
                    </div>

                    <div v-if="!configured" class="p-8 text-center text-gray-600">
                        {{ $t('words.whatsapp-not-configured') }}
                    </div>

                    <div v-else class="flex flex-col md:flex-row h-[600px] max-h-[75vh]">
                        <div
                            v-if="!lockTrainee"
                            class="md:w-1/3 border-b md:border-b-0 md:border-r flex flex-col overflow-y-auto"
                        >
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

                        <div
                            class="flex flex-col h-full overflow-hidden"
                            :class="lockTrainee ? 'w-full' : 'md:w-2/3'"
                        >
                            <div v-if="!selectedTrainee" class="flex-1 flex items-center justify-center text-gray-400 p-8 text-center space-y-3">
                                <div>
                                    <p>{{ $t('words.select-trainee') }}</p>
                                    <button
                                        v-if="!lockTrainee"
                                        type="button"
                                        @click="openNewChatModal"
                                        class="mt-3 text-sm bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium"
                                    >
                                        {{ $t('words.new-chat') }}
                                    </button>
                                </div>
                            </div>

                            <template v-else>
                                <div class="p-4 border-b bg-gray-50 space-y-3">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <div class="font-semibold truncate">
                                                {{ selectedTrainee.name }}
                                                <span v-if="selectedTrainee.company_name" class="font-normal text-gray-500"> · {{ selectedTrainee.company_name }}</span>
                                            </div>
                                            <div class="text-sm text-gray-600 flex items-center gap-2 flex-wrap">
                                                <span>{{ $t('words.phone') }}: <span dir="ltr">{{ selectedTrainee.phone }}</span></span>
                                                <span
                                                    v-if="messagingWindowLabel"
                                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md font-semibold border text-xs"
                                                    :class="messagingWindowBadgeClass"
                                                    :title="$t('words.whatsapp-freeform-hint')"
                                                    dir="ltr"
                                                >
                                                    <ion-icon
                                                        :name="messagingWindowIsOpen ? 'timer-outline' : 'lock-closed-outline'"
                                                        class="w-3.5 h-3.5"
                                                    ></ion-icon>
                                                    {{ messagingWindowLabel }}
                                                </span>
                                            </div>
                                            <div class="text-xs text-green-600 mt-1">{{ $t('words.whatsapp-live-updates') }}</div>
                                        </div>
                                        <div class="flex items-center gap-2 flex-shrink-0 flex-wrap justify-end">
                                            <div
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
                                                    v-if="canPauseBot"
                                                    type="button"
                                                    class="text-xs bg-white border border-orange-300 hover:bg-orange-50 text-orange-800 px-3 py-1.5 rounded-lg font-medium transition disabled:opacity-50"
                                                    :disabled="pausingBot"
                                                    @click="pauseBot"
                                                >
                                                    {{ pausingBot ? $t('words.saving') : $t('words.pause-bot-30m') }}
                                                </button>
                                                <button
                                                    v-if="canResumeBot"
                                                    type="button"
                                                    class="text-xs bg-white border border-green-300 hover:bg-green-50 text-green-800 px-3 py-1.5 rounded-lg font-medium transition disabled:opacity-50"
                                                    :disabled="pausingBot"
                                                    @click="resumeBot"
                                                >
                                                    {{ pausingBot ? $t('words.saving') : $t('words.resume-bot') }}
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
                                        <div
                                            v-if="messagingWindowIsOpen === false"
                                            class="mb-2 flex items-start gap-2 rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs text-amber-900"
                                        >
                                            <ion-icon name="lock-closed-outline" class="w-4 h-4 mt-0.5 flex-shrink-0"></ion-icon>
                                            <span>{{ $t('words.whatsapp-window-locked-hint') }}</span>
                                        </div>
                                        <jet-textarea
                                            v-model="freeformMessage"
                                            class="w-full text-sm mb-3"
                                            rows="3"
                                            :placeholder="$t('words.message')"
                                            :disabled="messagingWindowIsOpen === false"
                                        />
                                        <p class="text-xs text-gray-500 mb-3">{{ $t('words.whatsapp-freeform-hint') }}</p>
                                        <jet-button
                                            @click.native="sendFreeform"
                                            :class="{ 'opacity-25': sending }"
                                            :disabled="sending || !freeformMessage.trim() || messagingWindowIsOpen === false"
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

        <portal-target :name="newChatPortalName"></portal-target>
        <portal :to="newChatPortalName">
            <modal :name="newChatModalName" :width="620" :height="'auto'" :scrollable="true">
                <div class="bg-white rounded-xl shadow-2xl p-6 flex flex-col max-h-[85vh]">
                    <div class="flex items-center justify-between pb-4 border-b mb-4">
                        <h3 class="text-lg font-bold text-gray-800">{{ $t('words.new-chat') }}</h3>
                        <button type="button" @click="closeNewChatModal" class="text-gray-400 hover:text-gray-600">
                            <ion-icon name="close-outline" class="w-6 h-6"></ion-icon>
                        </button>
                    </div>

                    <div class="flex gap-0.5 p-0.5 bg-gray-100 rounded-md mb-4 w-fit">
                        <button
                            type="button"
                            class="px-3 py-1.5 rounded text-xs font-medium transition"
                            :class="newChatMode === 'trainee' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                            @click="setNewChatMode('trainee')"
                        >
                            {{ $t('words.trainee') }}
                        </button>
                        <button
                            type="button"
                            class="px-3 py-1.5 rounded text-xs font-medium transition"
                            :class="newChatMode === 'company' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                            @click="setNewChatMode('company')"
                        >
                            {{ $t('words.company') }}
                        </button>
                    </div>

                    <div class="space-y-4 mb-4 overflow-y-auto flex-1 min-h-0">
                        <div v-if="newChatMode === 'trainee'" class="space-y-3">
                            <label class="block text-xs font-medium text-gray-600 mb-1">{{ $t('words.search-trainee') }}</label>
                            <input
                                v-model="newChatTraineeSearch"
                                @input="searchNewChatTrainees"
                                type="text"
                                class="w-full form-input text-sm rounded-md border-gray-200"
                                :placeholder="$t('words.search-trainee')"
                            />
                            <div v-if="newChatSearchingTrainees" class="text-xs text-gray-500">{{ $t('words.loading') }}</div>
                            <ul v-else-if="newChatTraineeSearch.trim().length >= 2" class="border border-gray-200 rounded-md divide-y divide-gray-100 max-h-52 overflow-y-auto">
                                <li v-if="!newChatTraineeResults.length" class="px-3 py-3 text-xs text-gray-400">
                                    {{ $t('words.no-results') }}
                                </li>
                                <li v-for="trainee in newChatTraineeResults" :key="'nct-' + trainee.id">
                                    <button
                                        type="button"
                                        class="w-full text-left px-3 py-2 hover:bg-gray-50"
                                        :class="newChatSelectedTrainee && newChatSelectedTrainee.id === trainee.id ? 'bg-gray-100' : ''"
                                        @click="pickNewChatTrainee(trainee)"
                                    >
                                        <div class="text-sm font-medium text-gray-900 truncate">{{ trainee.name }}</div>
                                        <div class="text-[11px] text-gray-500 truncate">
                                            <span dir="ltr">{{ trainee.phone }}</span>
                                            <span v-if="trainee.company_name"> · {{ trainee.company_name }}</span>
                                        </div>
                                    </button>
                                </li>
                            </ul>
                            <div
                                v-if="newChatSelectedTrainee"
                                class="rounded-md border border-gray-200 bg-gray-50 px-3 py-2 text-xs text-gray-700"
                            >
                                <div class="font-medium text-sm text-gray-900">{{ newChatSelectedTrainee.name }}</div>
                                <div dir="ltr">{{ newChatSelectedTrainee.phone }}</div>
                            </div>
                        </div>

                        <div v-else class="space-y-3">
                            <div v-if="!newChatSelectedCompany">
                                <label class="block text-xs font-medium text-gray-600 mb-1">{{ $t('words.search-company') }}</label>
                                <input
                                    v-model="newChatCompanySearch"
                                    @input="searchNewChatCompanies"
                                    type="text"
                                    class="w-full form-input text-sm rounded-md border-gray-200"
                                    :placeholder="$t('words.search-company')"
                                />
                                <div v-if="newChatSearchingCompanies" class="text-xs text-gray-500 mt-2">{{ $t('words.loading') }}</div>
                                <ul
                                    v-else-if="newChatCompanySearch.trim().length >= 2"
                                    class="mt-2 border border-gray-200 rounded-md divide-y divide-gray-100 max-h-52 overflow-y-auto"
                                >
                                    <li v-if="!newChatCompanyResults.length" class="px-3 py-3 text-xs text-gray-400">
                                        {{ $t('words.no-results') }}
                                    </li>
                                    <li v-for="company in newChatCompanyResults" :key="'ncc-' + company.id">
                                        <button
                                            type="button"
                                            class="w-full text-left px-3 py-2 hover:bg-gray-50"
                                            @click="pickNewChatCompany(company)"
                                        >
                                            <div class="text-sm font-medium text-gray-900 truncate">{{ company.name }}</div>
                                            <div class="text-[11px] text-gray-500">
                                                {{ $t('words.whatsapp-active-trainees-count', { count: company.active_trainees_count || 0 }) }}
                                            </div>
                                        </button>
                                    </li>
                                </ul>
                            </div>

                            <div v-else class="space-y-2">
                                <div class="flex items-center justify-between gap-2">
                                    <button
                                        type="button"
                                        class="text-xs text-gray-600 hover:text-gray-900"
                                        @click="clearNewChatCompany"
                                    >
                                        ← {{ $t('words.back') }}
                                    </button>
                                    <div class="text-sm font-medium text-gray-900 truncate">{{ newChatSelectedCompany.name }}</div>
                                </div>
                                <p class="text-xs text-gray-500">{{ $t('words.whatsapp-company-bulk-hint') }}</p>
                                <div class="text-sm text-gray-700">
                                    <span v-if="newChatLoadingCompanyTrainees">{{ $t('words.loading') }}</span>
                                    <span v-else>
                                        {{ $t('words.whatsapp-active-trainees-count', { count: newChatCompanyActiveCount }) }}
                                    </span>
                                </div>
                                <ul
                                    v-if="!newChatLoadingCompanyTrainees && newChatCompanyTrainees.length"
                                    class="border border-gray-200 rounded-md divide-y divide-gray-100 max-h-40 overflow-y-auto"
                                >
                                    <li
                                        v-for="trainee in newChatCompanyTrainees"
                                        :key="'ncat-' + trainee.id"
                                        class="px-3 py-2 text-sm flex items-center justify-between gap-2"
                                    >
                                        <span class="truncate font-medium text-gray-800">{{ trainee.name }}</span>
                                        <span class="text-xs text-gray-500 shrink-0" dir="ltr">{{ trainee.phone }}</span>
                                    </li>
                                </ul>
                                <div
                                    v-else-if="!newChatLoadingCompanyTrainees"
                                    class="text-xs text-gray-400 px-1 py-2"
                                >
                                    {{ $t('words.whatsapp-company-no-active-trainees') }}
                                </div>
                            </div>
                        </div>

                        <div v-if="newChatMode === 'company' && newChatSelectedCompany">
                            <label class="block text-xs font-medium text-gray-600 mb-1">{{ $t('words.whatsapp-templates') }}</label>
                            <select
                                v-model="newChatTemplateSid"
                                @change="loadNewChatTemplateDetails"
                                class="w-full form-select text-sm rounded-md border-gray-200"
                            >
                                <option value="">{{ $t('words.select-template') }}</option>
                                <option
                                    v-for="template in templates"
                                    :key="'nctpl-' + template.sid"
                                    :value="template.sid"
                                >
                                    {{ template.friendly_name }} ({{ template.language }})
                                </option>
                            </select>

                            <div v-if="newChatTemplate" class="mt-3 p-3 bg-gray-50 rounded-md text-sm whitespace-pre-wrap text-gray-800">
                                {{ previewNewChatTemplateBody }}
                            </div>

                            <div v-if="newChatManualVariables.length" class="mt-3 space-y-2">
                                <div class="text-xs font-medium text-gray-700">{{ $t('words.template-variables') }}</div>
                                <div
                                    v-for="variableKey in newChatManualVariables"
                                    :key="'ncv-' + variableKey"
                                >
                                    <input
                                        v-model="newChatTemplateVariables[variableKey]"
                                        type="text"
                                        class="w-full form-input text-xs rounded-md border-gray-200"
                                        :placeholder="newChatTemplateVariableLabel(variableKey)"
                                    />
                                </div>
                                <p class="text-[11px] text-gray-400">{{ $t('words.whatsapp-company-auto-vars-hint') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t">
                        <button
                            type="button"
                            @click="closeNewChatModal"
                            class="px-4 py-2 rounded-md text-sm font-medium bg-gray-100 hover:bg-gray-200 text-gray-700 transition"
                        >
                            {{ $t('words.cancel') }}
                        </button>
                        <button
                            v-if="newChatMode === 'trainee'"
                            type="button"
                            class="px-4 py-2 rounded-md text-sm font-medium bg-green-600 hover:bg-green-700 text-white transition disabled:opacity-50"
                            :disabled="!newChatSelectedTrainee"
                            @click="confirmNewChatTrainee"
                        >
                            {{ $t('words.start-chat') }}
                        </button>
                        <button
                            v-else
                            type="button"
                            class="px-4 py-2 rounded-md text-sm font-medium bg-green-600 hover:bg-green-700 text-white transition disabled:opacity-50"
                            :disabled="newChatSending || !newChatSelectedCompany || !newChatTemplateSid || !newChatCompanyActiveCount || newChatLoadingCompanyTrainees"
                            @click="sendNewChatCompanyTemplate"
                        >
                            {{ newChatSending ? $t('words.sending') : $t('words.whatsapp-send-template-to-company') }}
                        </button>
                    </div>

                    <p v-if="newChatError" class="mt-2 text-xs text-red-600">{{ newChatError }}</p>
                    <p v-if="newChatSuccess" class="mt-2 text-xs text-green-600">{{ newChatSuccess }}</p>
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
    props: {
        hideTrigger: {
            type: Boolean,
            default: false,
        },
        triggerLabel: {
            type: String,
            default: null,
        },
        triggerClass: {
            type: String,
            default: null,
        },
        instanceId: {
            type: String,
            default: 'finance',
        },
    },
    data() {
        return {
            configured: false,
            searchQuery: '',
            searchResults: [],
            searching: false,
            selectedTrainee: null,
            lockTrainee: false,
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
            messagingWindow: null,
            windowNowMs: Date.now(),
            messagingWindowTimer: null,
            newChatMode: 'trainee',
            newChatTraineeSearch: '',
            newChatTraineeResults: [],
            newChatSearchingTrainees: false,
            newChatSelectedTrainee: null,
            newChatCompanySearch: '',
            newChatCompanyResults: [],
            newChatSearchingCompanies: false,
            newChatSelectedCompany: null,
            newChatCompanyTrainees: [],
            newChatCompanyActiveCount: 0,
            newChatLoadingCompanyTrainees: false,
            newChatTemplateSid: '',
            newChatTemplate: null,
            newChatTemplateVariables: {},
            newChatSending: false,
            newChatError: '',
            newChatSuccess: '',
        };
    },
    computed: {
        modalName() {
            return 'financeWhatsAppChatModal-' + this.instanceId;
        },
        portalName() {
            return 'finance-whatsapp-chat-modal-' + this.instanceId;
        },
        newChatModalName() {
            return 'financeWhatsAppNewChatModal-' + this.instanceId;
        },
        newChatPortalName() {
            return 'finance-whatsapp-new-chat-modal-' + this.instanceId;
        },
        newChatManualVariables() {
            if (!this.newChatTemplate) {
                return [];
            }
            return this.newChatTemplate.manual_variables || this.newChatTemplate.variables || [];
        },
        previewNewChatTemplateBody() {
            if (!this.newChatTemplate) {
                return '';
            }
            let body = this.newChatTemplate.body_display || this.newChatTemplate.body || '';
            const values = { ...(this.newChatTemplateVariables || {}) };
            Object.keys(values).forEach((key) => {
                const raw = values[key] == null ? '' : String(values[key]);
                body = String(body).replace(new RegExp('\\{\\{\\s*' + key + '\\s*\\}\\}', 'g'), raw || ('{{' + key + '}}'));
            });
            return body;
        },
        messagingWindowRemainingSeconds() {
            const now = this.windowNowMs;
            const window = this.messagingWindow;
            if (!window) {
                return 0;
            }
            let expiresMs = null;
            if (window.expires_at) {
                const parsed = Date.parse(window.expires_at);
                if (!Number.isNaN(parsed)) {
                    expiresMs = parsed;
                }
            }
            if (expiresMs === null && window.last_inbound_at) {
                const lastMs = Date.parse(window.last_inbound_at);
                if (!Number.isNaN(lastMs)) {
                    expiresMs = lastMs + (24 * 60 * 60 * 1000);
                }
            }
            if (expiresMs === null) {
                return 0;
            }
            return Math.max(0, Math.floor((expiresMs - now) / 1000));
        },
        messagingWindowIsOpen() {
            if (!this.selectedTrainee) {
                return null;
            }
            return this.messagingWindowRemainingSeconds > 0;
        },
        messagingWindowLabel() {
            if (!this.selectedTrainee) {
                return '';
            }
            if (this.messagingWindowRemainingSeconds <= 0) {
                return this.$t('words.whatsapp-window-locked');
            }
            return this.$t('words.whatsapp-window-open') + ' · ' + this.formatCountdown(this.messagingWindowRemainingSeconds);
        },
        messagingWindowBadgeClass() {
            if (this.messagingWindowIsOpen === false) {
                return 'bg-red-50 border-red-200 text-red-800';
            }
            if (this.messagingWindowRemainingSeconds <= 3600) {
                return 'bg-amber-50 border-amber-200 text-amber-900';
            }
            return 'bg-emerald-50 border-emerald-200 text-emerald-800';
        },
        botStatusLabel() {
            if (!this.botStatus) {
                return this.$t('words.loading') + '...';
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
        canPauseBot() {
            if (!this.botStatus) {
                return false;
            }
            if (typeof this.botStatus.can_pause === 'boolean') {
                return this.botStatus.can_pause;
            }
            return !!((this.botStatus.workflow_assigned || this.botStatus.ai_enabled) && !this.botStatus.is_paused);
        },
        canResumeBot() {
            if (!this.botStatus) {
                return false;
            }
            if (typeof this.botStatus.can_resume === 'boolean') {
                return this.botStatus.can_resume;
            }
            return !!((this.botStatus.workflow_assigned || this.botStatus.ai_enabled) && this.botStatus.is_paused);
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
        this.stopMessagingWindowTicker();
        if (this.messagesRefreshTimer) {
            clearTimeout(this.messagesRefreshTimer);
            this.messagesRefreshTimer = null;
        }
    },
    methods: {
        formatCountdown(totalSeconds) {
            const seconds = Math.max(0, Math.floor(Number(totalSeconds) || 0));
            const hours = Math.floor(seconds / 3600);
            const minutes = Math.floor((seconds % 3600) / 60);
            const secs = seconds % 60;
            const pad = (value) => String(value).padStart(2, '0');
            return `${pad(hours)}:${pad(minutes)}:${pad(secs)}`;
        },
        startMessagingWindowTicker() {
            this.stopMessagingWindowTicker();
            this.windowNowMs = Date.now();
            this.messagingWindowTimer = setInterval(() => {
                this.windowNowMs = Date.now();
            }, 1000);
        },
        stopMessagingWindowTicker() {
            if (this.messagingWindowTimer) {
                clearInterval(this.messagingWindowTimer);
                this.messagingWindowTimer = null;
            }
        },
        applyMessagingWindow(window) {
            if (!window || typeof window !== 'object') {
                this.messagingWindow = {
                    last_inbound_at: null,
                    expires_at: null,
                    remaining_seconds: 0,
                    is_open: false,
                };
                return;
            }
            this.messagingWindow = window;
        },
        refreshMessagingWindowFromInbound(message) {
            if (!message || this.isOutboundMessage(message) || message.is_note) {
                return;
            }
            const sentAt = message.sent_at || message.created_at;
            if (!sentAt) {
                return;
            }
            const lastMs = Date.parse(sentAt);
            if (Number.isNaN(lastMs)) {
                return;
            }
            const expiresMs = lastMs + (24 * 60 * 60 * 1000);
            const remaining = Math.max(0, Math.floor((expiresMs - Date.now()) / 1000));
            this.messagingWindow = {
                last_inbound_at: new Date(lastMs).toISOString(),
                expires_at: new Date(expiresMs).toISOString(),
                remaining_seconds: remaining,
                is_open: remaining > 0,
            };
        },
        async open() {
            this.lockTrainee = false;
            this.$modal.show(this.modalName);
            await this.checkStatus();

            if (this.configured) {
                await this.loadTemplates();
                this.subscribeEcho();
                this.startMessagingWindowTicker();
            }
        },
        async openForTrainee(trainee) {
            if (!trainee || !trainee.phone) {
                this.errorMessage = this.$t('words.whatsapp-trainee-phone-missing');
                return;
            }

            this.lockTrainee = true;
            this.$modal.show(this.modalName);
            await this.checkStatus();

            if (!this.configured) {
                return;
            }

            await this.loadTemplates();
            this.subscribeEcho();
            this.startMessagingWindowTicker();
            await this.selectTrainee(this.normalizeTraineePayload(trainee));
        },
        normalizeTraineePayload(trainee) {
            const company = trainee.company || {};
            return {
                id: trainee.id,
                name: trainee.name,
                phone: trainee.phone,
                identity_number: trainee.identity_number,
                english_name: trainee.english_name || trainee.name_english || null,
                company_name: trainee.company_name || company.name_ar || company.name || null,
                show_url: trainee.show_url || route('back.trainees.show', trainee.id),
            };
        },
        close() {
            this.unsubscribeEcho();
            this.stopPolling();
            this.stopMessagingWindowTicker();
            this.$modal.hide(this.newChatModalName);
            this.$modal.hide(this.modalName);
            this.lockTrainee = false;
            this.resetState();
        },
        normalizePhone(phone) {
            return String(phone || '').replace(/\D+/g, '');
        },
        subscribeEcho() {
            this.unsubscribeEcho();
            this.stopPolling();

            if (!window.Echo) {
                console.warn('[FinanceWhatsAppChat] Echo unavailable — slow polling fallback when a trainee is selected');
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
        startPollingFallback() {
            this.stopPolling();
            this.pollInterval = setInterval(() => {
                this.loadMessages(true);
            }, 20000);
        },
        stopPolling() {
            if (this.pollInterval) {
                clearInterval(this.pollInterval);
                this.pollInterval = null;
            }
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
            this.messagingWindow = null;
            this.resetNewChatState();
        },
        resetNewChatState() {
            this.newChatMode = 'trainee';
            this.newChatTraineeSearch = '';
            this.newChatTraineeResults = [];
            this.newChatSearchingTrainees = false;
            this.newChatSelectedTrainee = null;
            this.newChatCompanySearch = '';
            this.newChatCompanyResults = [];
            this.newChatSearchingCompanies = false;
            this.newChatSelectedCompany = null;
            this.newChatCompanyTrainees = [];
            this.newChatCompanyActiveCount = 0;
            this.newChatLoadingCompanyTrainees = false;
            this.newChatTemplateSid = '';
            this.newChatTemplate = null;
            this.newChatTemplateVariables = {};
            this.newChatSending = false;
            this.newChatError = '';
            this.newChatSuccess = '';
        },
        openNewChatModal() {
            this.resetNewChatState();
            if (this.configured && (!this.templates || !this.templates.length)) {
                this.loadTemplates();
            }
            this.$modal.show(this.newChatModalName);
        },
        closeNewChatModal() {
            this.$modal.hide(this.newChatModalName);
            this.resetNewChatState();
        },
        setNewChatMode(mode) {
            this.newChatMode = mode;
            this.newChatError = '';
            this.newChatSuccess = '';
            this.newChatSelectedTrainee = null;
            this.newChatSelectedCompany = null;
            this.newChatCompanyTrainees = [];
            this.newChatCompanyActiveCount = 0;
            this.newChatTemplateSid = '';
            this.newChatTemplate = null;
            this.newChatTemplateVariables = {};
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
        async resumeBot() {
            if (!this.selectedTrainee || !this.selectedTrainee.phone || this.pausingBot) {
                return;
            }

            this.pausingBot = true;
            this.errorMessage = '';
            this.successMessage = '';

            try {
                const { data } = await axios.post(route('back.finance.whatsapp.bot-resume'), {
                    phone: this.selectedTrainee.phone,
                });
                this.botStatus = data.bot || null;
                this.successMessage = data.message || this.$t('words.whatsapp-bot-resumed');
            } catch (error) {
                this.errorMessage = (error.response && error.response.data && error.response.data.message)
                    || this.$t('words.whatsapp-bot-resume-failed');
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
        searchNewChatTrainees: throttle(async function () {
            if (!this.newChatTraineeSearch || this.newChatTraineeSearch.length < 2) {
                this.newChatTraineeResults = [];
                return;
            }

            this.newChatSearchingTrainees = true;
            try {
                const { data } = await axios.get(route('back.finance.whatsapp.trainees'), {
                    params: { search: this.newChatTraineeSearch },
                });
                this.newChatTraineeResults = data.trainees || [];
            } catch (error) {
                this.newChatTraineeResults = [];
            } finally {
                this.newChatSearchingTrainees = false;
            }
        }, 300),
        searchNewChatCompanies: throttle(async function () {
            if (!this.newChatCompanySearch || this.newChatCompanySearch.length < 2) {
                this.newChatCompanyResults = [];
                return;
            }

            this.newChatSearchingCompanies = true;
            try {
                const { data } = await axios.get(route('back.finance.whatsapp.companies'), {
                    params: { search: this.newChatCompanySearch, limit: 20 },
                });
                this.newChatCompanyResults = data.companies || [];
            } catch (error) {
                this.newChatCompanyResults = [];
            } finally {
                this.newChatSearchingCompanies = false;
            }
        }, 300),
        pickNewChatTrainee(trainee) {
            this.newChatSelectedTrainee = trainee;
            this.newChatError = '';
        },
        async pickNewChatCompany(company) {
            this.newChatSelectedCompany = company;
            this.newChatError = '';
            this.newChatSuccess = '';
            this.newChatTemplateSid = '';
            this.newChatTemplate = null;
            this.newChatTemplateVariables = {};
            await this.loadNewChatCompanyTrainees();
        },
        clearNewChatCompany() {
            this.newChatSelectedCompany = null;
            this.newChatCompanyTrainees = [];
            this.newChatCompanyActiveCount = 0;
            this.newChatTemplateSid = '';
            this.newChatTemplate = null;
            this.newChatTemplateVariables = {};
            this.newChatError = '';
            this.newChatSuccess = '';
        },
        async loadNewChatCompanyTrainees() {
            if (!this.newChatSelectedCompany || !this.newChatSelectedCompany.id) {
                this.newChatCompanyTrainees = [];
                this.newChatCompanyActiveCount = 0;
                return;
            }

            this.newChatLoadingCompanyTrainees = true;
            try {
                const { data } = await axios.get(
                    route('back.finance.whatsapp.companies.active-trainees', this.newChatSelectedCompany.id)
                );
                this.newChatCompanyTrainees = data.trainees || [];
                this.newChatCompanyActiveCount = data.count || this.newChatCompanyTrainees.length;
                this.$set(this.newChatSelectedCompany, 'active_trainees_count', this.newChatCompanyActiveCount);
            } catch (error) {
                this.newChatCompanyTrainees = [];
                this.newChatCompanyActiveCount = 0;
                this.newChatError = error.response?.data?.message || this.$t('words.whatsapp-send-failed');
            } finally {
                this.newChatLoadingCompanyTrainees = false;
            }
        },
        async confirmNewChatTrainee() {
            if (!this.newChatSelectedTrainee) {
                return;
            }
            const trainee = this.newChatSelectedTrainee;
            this.closeNewChatModal();
            await this.selectTrainee(trainee);
        },
        async loadNewChatTemplateDetails() {
            this.newChatTemplate = null;
            this.newChatTemplateVariables = {};
            if (!this.newChatTemplateSid) {
                return;
            }
            try {
                const { data } = await axios.get(route('back.finance.whatsapp.templates.show', this.newChatTemplateSid));
                this.newChatTemplate = data.template;
                const manual = this.newChatTemplate.manual_variables || this.newChatTemplate.variables || [];
                const samples = this.newChatTemplate.variable_samples || {};
                manual.forEach((key) => {
                    this.$set(this.newChatTemplateVariables, key, samples[key] || '');
                });
                if (this.newChatSelectedCompany && this.newChatSelectedCompany.name) {
                    const bindings = this.newChatTemplate.variable_bindings || {};
                    const autoVariables = this.newChatTemplate.auto_variables || {};
                    Object.keys(autoVariables).forEach((key) => {
                        if (autoVariables[key] === 'company_name') {
                            this.$set(this.newChatTemplateVariables, key, this.newChatSelectedCompany.name);
                        }
                    });
                    Object.keys(bindings).forEach((key) => {
                        if (bindings[key] === 'company_name') {
                            this.$set(this.newChatTemplateVariables, key, this.newChatSelectedCompany.name);
                        }
                    });
                }
            } catch (error) {
                this.newChatError = this.$t('words.whatsapp-templates-load-failed');
            }
        },
        newChatTemplateVariableLabel(variableKey) {
            const bindings = (this.newChatTemplate && this.newChatTemplate.variable_bindings) || {};
            return bindings[variableKey] || (this.$t('words.template-variable') + ' ' + variableKey);
        },
        async sendNewChatCompanyTemplate() {
            if (!this.newChatSelectedCompany || !this.newChatTemplateSid || !this.newChatCompanyActiveCount) {
                return;
            }

            const confirmed = window.confirm(
                this.$t('words.whatsapp-company-send-confirm', {
                    count: this.newChatCompanyActiveCount,
                    company: this.newChatSelectedCompany.name,
                })
            );
            if (!confirmed) {
                return;
            }

            this.newChatSending = true;
            this.newChatError = '';
            this.newChatSuccess = '';

            try {
                const { data } = await axios.post(route('back.finance.whatsapp.send-template-to-company'), {
                    company_id: this.newChatSelectedCompany.id,
                    content_sid: this.newChatTemplateSid,
                    content_variables: this.newChatTemplateVariables,
                });

                this.newChatSuccess = data.message
                    || this.$t('words.whatsapp-company-template-sent', {
                        sent: data.sent,
                        total: data.total,
                    });

                if (data.failed_count > 0) {
                    const failedNames = (data.failed || []).slice(0, 5).map((row) => row.name).join(', ');
                    this.newChatError = this.$t('words.whatsapp-company-template-partial-fail', {
                        failed: data.failed_count,
                        names: failedNames,
                    });
                }
            } catch (error) {
                this.newChatError = error.response?.data?.message || this.$t('words.whatsapp-send-failed');
            } finally {
                this.newChatSending = false;
            }
        },
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

            // Live updates come from Echo. Poll only if realtime is unavailable.
            if (!window.Echo) {
                this.startPollingFallback();
            } else {
                this.stopPolling();
            }
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

                if (Object.prototype.hasOwnProperty.call(data, 'messaging_window')) {
                    this.applyMessagingWindow(data.messaging_window);
                }

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
                    this.refreshMessagingWindowFromInbound(message);
                    return;
                }

                this.messages.push(message);
                this.refreshMessagingWindowFromInbound(message);
                added = true;
            });

            if (added) {
                this.$nextTick(() => this.scrollToBottom());
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
