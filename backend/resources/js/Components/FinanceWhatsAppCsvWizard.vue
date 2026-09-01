<template>
    <div class="flex flex-col min-h-0 flex-1">
        <ol class="flex items-center gap-2 mb-4 text-xs">
            <li
                v-for="step in steps"
                :key="step.id"
                class="flex items-center gap-2"
            >
                <span
                    class="w-6 h-6 rounded-full flex items-center justify-center font-semibold"
                    :class="stepClass(step.id)"
                >{{ step.id }}</span>
                <span :class="csvStep === step.id ? 'font-semibold text-gray-900' : 'text-gray-500'">
                    {{ step.label }}
                </span>
                <span v-if="step.id < 3" class="text-gray-300 px-1">—</span>
            </li>
        </ol>

        <div class="overflow-y-auto flex-1 min-h-0 space-y-4">
            <div v-if="csvStep === 1" class="space-y-3">
                <p class="text-sm text-gray-600">{{ $t('words.whatsapp-csv-upload-hint') }}</p>
                <label class="block border-2 border-dashed border-gray-200 rounded-lg px-4 py-8 text-center cursor-pointer hover:border-green-400 hover:bg-green-50/40 transition">
                    <input ref="csvFileInput" type="file" accept=".csv,text/csv,text/plain" class="hidden" @change="onCsvFile">
                    <div class="text-sm font-medium text-gray-800">{{ $t('words.whatsapp-csv-choose-file') }}</div>
                    <div v-if="csvFileName" class="text-xs text-gray-500 mt-1">{{ csvFileName }}</div>
                </label>
                <div v-if="csvLoading" class="text-xs text-gray-500">{{ $t('words.loading') }}</div>
            </div>

            <div v-else-if="csvStep === 2" class="space-y-3">
                <div class="flex flex-wrap items-center gap-2 text-xs">
                    <span class="px-2 py-1 rounded bg-emerald-50 text-emerald-800 border border-emerald-200">
                        {{ $t('words.whatsapp-csv-open-count', { count: openCount }) }}
                    </span>
                    <span class="px-2 py-1 rounded bg-red-50 text-red-800 border border-red-200">
                        {{ $t('words.whatsapp-csv-closed-count', { count: closedCount }) }}
                    </span>
                    <span class="text-gray-500">{{ selectedCount }} / {{ csvRows.length }}</span>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button type="button" class="text-xs px-2.5 py-1 rounded border border-gray-200 hover:bg-gray-50" @click="selectOpenWindows">
                        {{ $t('words.whatsapp-csv-select-open') }}
                    </button>
                    <button type="button" class="text-xs px-2.5 py-1 rounded border border-gray-200 hover:bg-gray-50" @click="selectAllRows(true)">
                        {{ $t('words.select-all') }}
                    </button>
                    <button type="button" class="text-xs px-2.5 py-1 rounded border border-gray-200 hover:bg-gray-50" @click="selectAllRows(false)">
                        {{ $t('words.whatsapp-csv-clear-selection') }}
                    </button>
                </div>
                <ul class="border border-gray-200 rounded-md divide-y divide-gray-100 max-h-72 overflow-y-auto">
                    <li
                        v-for="row in csvRows"
                        :key="row.normalized_phone"
                        class="px-3 py-2 flex items-start gap-2 text-sm"
                    >
                        <input
                            type="checkbox"
                            class="mt-1 rounded border-gray-300 text-green-600 focus:ring-green-500"
                            :checked="row.selected"
                            @change="toggleRow(row)"
                        />
                        <div class="min-w-0 flex-1">
                            <div class="font-medium text-gray-900 truncate">
                                {{ row.name || $t('words.whatsapp-csv-unknown-trainee') }}
                            </div>
                            <div class="text-[11px] text-gray-500 truncate">
                                <span dir="ltr">{{ row.phone }}</span>
                                <span v-if="row.company_name"> · {{ row.company_name }}</span>
                            </div>
                        </div>
                        <span
                            class="text-[10px] font-semibold px-2 py-0.5 rounded border whitespace-nowrap"
                            :class="row.window_open
                                ? 'bg-emerald-50 border-emerald-200 text-emerald-800'
                                : 'bg-red-50 border-red-200 text-red-800'"
                        >
                            {{ row.window_open ? $t('words.whatsapp-window-open') : $t('words.whatsapp-window-locked') }}
                        </span>
                    </li>
                </ul>
            </div>

            <div v-else class="space-y-4">
                <p class="text-xs text-gray-500">
                    {{ $t('words.whatsapp-csv-send-to-selected', { count: selectedCount }) }}
                </p>

                <div class="flex gap-0.5 p-0.5 bg-gray-100 rounded-md w-fit">
                    <button
                        v-for="option in sendTypes"
                        :key="option.id"
                        type="button"
                        class="px-3 py-1.5 rounded text-xs font-medium transition"
                        :class="csvSendType === option.id ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                        @click="csvSendType = option.id"
                    >
                        {{ option.label }}
                    </button>
                </div>

                <p
                    v-if="csvSendType !== 'template' && selectedClosedCount"
                    class="text-xs text-amber-800 bg-amber-50 border border-amber-200 rounded-md px-3 py-2"
                >
                    {{ $t('words.whatsapp-csv-message-requires-open', { count: selectedClosedCount }) }}
                </p>

                <div v-if="csvSendType === 'message'">
                    <textarea
                        v-model="csvMessage"
                        rows="4"
                        class="w-full form-textarea text-sm rounded-md border-gray-200"
                        :placeholder="$t('words.message')"
                    ></textarea>
                </div>

                <div v-else-if="csvSendType === 'quick_reply'" class="space-y-2">
                    <div v-if="loadingQuickReplies" class="text-xs text-gray-500">{{ $t('words.loading') }}</div>
                    <select v-model="csvQuickReplyId" class="w-full form-select text-sm rounded-md border-gray-200">
                        <option value="">{{ $t('words.quick-reply') }}</option>
                        <option v-for="reply in quickReplies" :key="reply.id" :value="reply.id">
                            {{ reply.title }}
                        </option>
                    </select>
                    <div v-if="selectedQuickReply" class="p-3 bg-gray-50 rounded-md text-sm whitespace-pre-wrap text-gray-800">
                        {{ selectedQuickReply.body }}
                    </div>
                    <p v-if="!loadingQuickReplies && !quickReplies.length" class="text-xs text-gray-400">
                        {{ $t('words.no-quick-replies') }}
                    </p>
                </div>

                <div v-else class="space-y-2">
                    <select
                        v-model="csvTemplateSid"
                        class="w-full form-select text-sm rounded-md border-gray-200"
                        @change="loadCsvTemplateDetails"
                    >
                        <option value="">{{ $t('words.select-template') }}</option>
                        <option v-for="template in templates" :key="'csv-' + template.sid" :value="template.sid">
                            {{ template.friendly_name }} ({{ template.language }})
                        </option>
                    </select>
                    <div v-if="csvTemplate" class="p-3 bg-gray-50 rounded-md text-sm whitespace-pre-wrap text-gray-800">
                        {{ previewCsvTemplateBody }}
                    </div>
                    <div v-if="csvManualVariables.length" class="space-y-2">
                        <div class="text-xs font-medium text-gray-700">{{ $t('words.template-variables') }}</div>
                        <div v-for="variableKey in csvManualVariables" :key="'csv-var-' + variableKey">
                            <input
                                v-model="csvTemplateVariables[variableKey]"
                                type="text"
                                class="w-full form-input text-xs rounded-md border-gray-200"
                                :placeholder="csvTemplateVariableLabel(variableKey)"
                            />
                        </div>
                        <p class="text-[11px] text-gray-400">{{ $t('words.whatsapp-company-auto-vars-hint') }}</p>
                    </div>
                </div>

                <div v-if="csvResult" class="rounded-md border border-gray-200 bg-gray-50 px-3 py-2 text-xs space-y-1">
                    <div class="font-medium text-green-700">{{ csvResult.message }}</div>
                    <div v-if="csvResult.skipped_count" class="text-amber-800">
                        {{ $t('words.whatsapp-csv-skipped-count', { count: csvResult.skipped_count }) }}
                    </div>
                    <div v-if="csvResult.failed_count" class="text-red-700">
                        {{ $t('words.whatsapp-company-template-partial-fail', { failed: csvResult.failed_count, names: failedNames }) }}
                    </div>
                </div>
            </div>
        </div>

        <p v-if="csvError" class="mt-2 text-xs text-red-600">{{ csvError }}</p>

        <div class="flex justify-end gap-2 pt-3 border-t mt-4">
            <button
                v-if="csvStep > 1 && !csvSending"
                type="button"
                class="px-4 py-2 rounded-md text-sm font-medium bg-gray-100 hover:bg-gray-200 text-gray-700"
                @click="goBack"
            >
                {{ $t('words.back') }}
            </button>
            <button
                v-if="csvStep < 3"
                type="button"
                class="px-4 py-2 rounded-md text-sm font-medium bg-green-600 hover:bg-green-700 text-white disabled:opacity-50"
                :disabled="!canGoNext"
                @click="goNext"
            >
                {{ $t('words.next') }}
            </button>
            <button
                v-else
                type="button"
                class="px-4 py-2 rounded-md text-sm font-medium bg-green-600 hover:bg-green-700 text-white disabled:opacity-50"
                :disabled="csvSending || !canSend"
                @click="sendBulk"
            >
                {{ csvSending ? $t('words.sending') : $t('words.send') }}
            </button>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    props: {
        templates: {
            type: Array,
            default: () => [],
        },
    },
    data() {
        return {
            csvStep: 1,
            csvFileName: '',
            csvLoading: false,
            csvRows: [],
            csvSendType: 'message',
            csvMessage: '',
            csvQuickReplyId: '',
            quickReplies: [],
            loadingQuickReplies: false,
            csvTemplateSid: '',
            csvTemplate: null,
            csvTemplateVariables: {},
            csvSending: false,
            csvError: '',
            csvResult: null,
        };
    },
    computed: {
        steps() {
            return [
                { id: 1, label: this.$t('words.whatsapp-csv-step-upload') },
                { id: 2, label: this.$t('words.whatsapp-csv-step-recipients') },
                { id: 3, label: this.$t('words.whatsapp-csv-step-compose') },
            ];
        },
        sendTypes() {
            return [
                { id: 'message', label: this.$t('words.message') },
                { id: 'quick_reply', label: this.$t('words.quick-reply') },
                { id: 'template', label: this.$t('words.whatsapp-templates') },
            ];
        },
        openCount() {
            return this.csvRows.filter((row) => row.window_open).length;
        },
        closedCount() {
            return this.csvRows.filter((row) => !row.window_open).length;
        },
        selectedRows() {
            return this.csvRows.filter((row) => row.selected);
        },
        selectedCount() {
            return this.selectedRows.length;
        },
        selectedClosedCount() {
            return this.selectedRows.filter((row) => !row.window_open).length;
        },
        selectedQuickReply() {
            return this.quickReplies.find((reply) => reply.id === this.csvQuickReplyId) || null;
        },
        csvManualVariables() {
            if (!this.csvTemplate) {
                return [];
            }
            return this.csvTemplate.manual_variables || this.csvTemplate.variables || [];
        },
        previewCsvTemplateBody() {
            if (!this.csvTemplate) {
                return '';
            }
            let body = this.csvTemplate.body_display || this.csvTemplate.body || '';
            const values = { ...(this.csvTemplateVariables || {}) };
            Object.keys(values).forEach((key) => {
                const raw = values[key] == null ? '' : String(values[key]);
                body = String(body).replace(new RegExp('\\{\\{\\s*' + key + '\\s*\\}\\}', 'g'), raw || ('{{' + key + '}}'));
            });
            return body;
        },
        canGoNext() {
            if (this.csvStep === 1) {
                return this.csvRows.length > 0 && !this.csvLoading;
            }
            if (this.csvStep === 2) {
                return this.selectedCount > 0;
            }
            return false;
        },
        canSend() {
            if (!this.selectedCount || this.csvSending) {
                return false;
            }
            if (this.csvSendType === 'message') {
                return this.csvMessage.trim() !== '';
            }
            if (this.csvSendType === 'quick_reply') {
                return !!this.csvQuickReplyId;
            }
            return !!this.csvTemplateSid;
        },
        failedNames() {
            if (!this.csvResult || !this.csvResult.failed) {
                return '';
            }
            return this.csvResult.failed.slice(0, 5).map((row) => row.name || row.phone).join(', ');
        },
    },
    methods: {
        reset() {
            this.csvStep = 1;
            this.csvFileName = '';
            this.csvLoading = false;
            this.csvRows = [];
            this.csvSendType = 'message';
            this.csvMessage = '';
            this.csvQuickReplyId = '';
            this.csvTemplateSid = '';
            this.csvTemplate = null;
            this.csvTemplateVariables = {};
            this.csvSending = false;
            this.csvError = '';
            this.csvResult = null;
            if (this.$refs.csvFileInput) {
                this.$refs.csvFileInput.value = '';
            }
        },
        stepClass(id) {
            if (this.csvStep === id) {
                return 'bg-green-600 text-white';
            }
            if (this.csvStep > id) {
                return 'bg-green-100 text-green-800';
            }
            return 'bg-gray-100 text-gray-500';
        },
        async onCsvFile(event) {
            const file = event.target.files && event.target.files[0];
            if (!file) {
                return;
            }
            this.csvFileName = file.name;
            this.csvError = '';
            this.csvResult = null;
            this.csvLoading = true;

            try {
            const csv = await new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.onload = () => resolve(String(reader.result || ''));
                reader.onerror = () => reject(reader.error || new Error('read failed'));
                reader.readAsText(file);
            });
                const { data } = await axios.post(route('back.chat.preview-csv'), { csv });
                this.csvRows = (data.rows || []).map((row) => ({
                    ...row,
                    selected: !!row.window_open,
                }));
                if (!this.csvRows.length) {
                    this.csvError = this.$t('words.whatsapp-csv-no-phones');
                    return;
                }
                this.csvStep = 2;
            } catch (error) {
                this.csvRows = [];
                this.csvError = (error.response && error.response.data && error.response.data.message)
                    || this.$t('words.whatsapp-csv-parse-error');
            } finally {
                this.csvLoading = false;
            }
        },
        toggleRow(row) {
            this.$set(row, 'selected', !row.selected);
        },
        selectOpenWindows() {
            this.csvRows.forEach((row) => {
                this.$set(row, 'selected', !!row.window_open);
            });
        },
        selectAllRows(selected) {
            this.csvRows.forEach((row) => {
                this.$set(row, 'selected', selected);
            });
        },
        goNext() {
            if (!this.canGoNext) {
                return;
            }
            if (this.csvStep === 2) {
                this.loadQuickReplies();
            }
            this.csvStep += 1;
        },
        goBack() {
            if (this.csvStep > 1 && !this.csvSending) {
                this.csvStep -= 1;
            }
        },
        async loadQuickReplies() {
            this.loadingQuickReplies = true;
            try {
                const { data } = await axios.get(route('back.chat.quick-replies'));
                this.quickReplies = data.quick_replies || [];
            } catch (error) {
                this.quickReplies = [];
            } finally {
                this.loadingQuickReplies = false;
            }
        },
        async loadCsvTemplateDetails() {
            this.csvTemplate = null;
            this.csvTemplateVariables = {};
            if (!this.csvTemplateSid) {
                return;
            }
            try {
                const { data } = await axios.get(route('back.chat.templates.show', this.csvTemplateSid));
                this.csvTemplate = data.template;
                const manual = this.csvTemplate.manual_variables || this.csvTemplate.variables || [];
                const samples = this.csvTemplate.variable_samples || {};
                manual.forEach((key) => {
                    this.$set(this.csvTemplateVariables, key, samples[key] || '');
                });
            } catch (error) {
                this.csvError = this.$t('words.whatsapp-templates-load-failed');
            }
        },
        csvTemplateVariableLabel(variableKey) {
            const bindings = (this.csvTemplate && this.csvTemplate.variable_bindings) || {};
            return bindings[variableKey] || (this.$t('words.template-variable') + ' ' + variableKey);
        },
        async sendBulk() {
            if (!this.canSend) {
                return;
            }

            const confirmed = window.confirm(
                this.$t('words.whatsapp-csv-send-confirm', { count: this.selectedCount })
            );
            if (!confirmed) {
                return;
            }

            this.csvSending = true;
            this.csvError = '';
            this.csvResult = null;

            const payload = {
                type: this.csvSendType,
                phones: this.selectedRows.map((row) => row.normalized_phone),
            };

            if (this.csvSendType === 'message') {
                payload.body = this.csvMessage.trim();
            } else if (this.csvSendType === 'quick_reply') {
                payload.quick_reply_id = this.csvQuickReplyId;
            } else {
                payload.content_sid = this.csvTemplateSid;
                payload.content_variables = this.csvTemplateVariables;
            }

            try {
                const { data } = await axios.post(route('back.chat.send-bulk'), payload);
                this.csvResult = data;
            } catch (error) {
                this.csvError = (error.response && error.response.data && error.response.data.message)
                    || this.$t('words.whatsapp-send-failed');
            } finally {
                this.csvSending = false;
            }
        },
    },
};
</script>
