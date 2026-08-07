<template>
    <div>
        <button
            type="button"
            @click="open"
            class="bg-white hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold flex items-center gap-2 border border-gray-300 shadow-sm transition"
        >
            <ion-icon name="document-text-outline" class="w-5 h-5"></ion-icon>
            {{ $t('words.manage-whatsapp-templates') }}
        </button>

        <portal-target name="whatsapp-templates-manager-modal"></portal-target>
        <portal to="whatsapp-templates-manager-modal">
            <modal name="whatsappTemplatesManagerModal" :width="760" :height="'auto'" :scrollable="true">
                <div class="bg-white rounded-xl shadow-2xl p-6 flex flex-col max-h-[90vh]">
                    <div class="flex items-center justify-between pb-4 border-b mb-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-800">{{ $t('words.manage-whatsapp-templates') }}</h3>
                            <p class="text-xs text-gray-500 mt-1">{{ $t('words.whatsapp-templates-manage-hint') }}</p>
                        </div>
                        <button type="button" @click="close" class="text-gray-400 hover:text-gray-600">
                            <ion-icon name="close-outline" class="w-6 h-6"></ion-icon>
                        </button>
                    </div>

                    <div v-if="!canManage" class="mb-4 p-3 rounded-lg bg-amber-50 border border-amber-200 text-sm text-amber-800">
                        {{ $t('words.whatsapp-templates-manage-not-configured') }}
                    </div>

                    <div class="flex items-center justify-between mb-4 gap-3">
                        <div class="text-sm text-gray-600">
                            <span v-if="loading">{{ $t('words.loading') }}...</span>
                            <span v-else>{{ templates.length }} {{ $t('words.whatsapp-templates') }}</span>
                        </div>
                        <button
                            v-if="canManage && view === 'list'"
                            type="button"
                            @click="startCreate"
                            class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold"
                        >
                            {{ $t('words.create-whatsapp-template') }}
                        </button>
                    </div>

                    <div v-if="errorMessage" class="mb-3 text-sm text-red-600">{{ errorMessage }}</div>
                    <div v-if="successMessage" class="mb-3 text-sm text-green-600">{{ successMessage }}</div>

                    <div v-if="view === 'list'" class="overflow-y-auto flex-1 space-y-3">
                        <div v-if="!loading && templates.length === 0" class="text-center text-sm text-gray-500 py-8">
                            {{ $t('words.no-results') }}
                        </div>

                        <div
                            v-for="template in templates"
                            :key="template.sid"
                            class="border rounded-lg p-3 bg-gray-50"
                        >
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="font-semibold text-sm text-gray-900 truncate" dir="ltr">
                                        {{ template.friendly_name }}
                                    </div>
                                    <div class="flex flex-wrap gap-2 mt-1 text-[11px]">
                                        <span class="px-2 py-0.5 rounded bg-white border text-gray-700 capitalize">
                                            {{ template.approval_status }}
                                        </span>
                                        <span v-if="template.category" class="px-2 py-0.5 rounded bg-white border text-gray-700">
                                            {{ template.category }}
                                        </span>
                                        <span class="px-2 py-0.5 rounded bg-white border text-gray-700" dir="ltr">
                                            {{ template.language }}
                                        </span>
                                    </div>
                                    <p class="mt-2 text-xs text-gray-700 whitespace-pre-wrap">{{ template.body }}</p>
                                </div>
                                <div class="flex flex-col gap-1 shrink-0">
                                    <button
                                        v-if="canManage && template.can_edit"
                                        type="button"
                                        @click="startEdit(template)"
                                        class="px-2 py-1 text-xs rounded border bg-white hover:bg-gray-100 text-gray-700"
                                    >
                                        {{ $t('words.edit') }}
                                    </button>
                                    <button
                                        v-if="canManage"
                                        type="button"
                                        @click="confirmDelete(template)"
                                        :disabled="deletingSid === template.sid"
                                        class="px-2 py-1 text-xs rounded border bg-white hover:bg-red-50 text-red-600 border-red-200 disabled:opacity-50"
                                    >
                                        {{ $t('words.delete') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-else class="overflow-y-auto flex-1">
                        <form @submit.prevent="submitForm" class="space-y-3">
                            <div v-if="view === 'create'">
                                <label class="block text-xs font-semibold text-gray-700 mb-1">
                                    {{ $t('words.template-name') }}
                                </label>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    dir="ltr"
                                    class="w-full form-input text-sm rounded-lg border-gray-300"
                                    placeholder="order_shipped"
                                    required
                                />
                                <p class="text-[11px] text-gray-500 mt-1">{{ $t('words.whatsapp-template-name-hint') }}</p>
                            </div>
                            <div v-else class="text-sm text-gray-800">
                                <span class="font-semibold">{{ $t('words.template-name') }}:</span>
                                <span dir="ltr" class="ml-1">{{ form.name }}</span>
                            </div>

                            <div v-if="view === 'create'" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">
                                        {{ $t('words.category') }}
                                    </label>
                                    <select v-model="form.category" class="w-full form-select text-sm rounded-lg border-gray-300" required>
                                        <option value="UTILITY">UTILITY</option>
                                        <option value="MARKETING">MARKETING</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">
                                        {{ $t('words.template-language') }}
                                    </label>
                                    <select v-model="form.language" class="w-full form-select text-sm rounded-lg border-gray-300" required>
                                        <option value="ar">ar</option>
                                        <option value="en_US">en_US</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">
                                    {{ $t('words.header') }} ({{ $t('words.optional') }})
                                </label>
                                <input
                                    v-model="form.header"
                                    type="text"
                                    class="w-full form-input text-sm rounded-lg border-gray-300"
                                    maxlength="60"
                                />
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">
                                    {{ $t('words.body') }}
                                </label>
                                <textarea
                                    v-model="form.body"
                                    rows="4"
                                    class="w-full form-input text-sm rounded-lg border-gray-300"
                                    :placeholder="$t('words.whatsapp-template-body-hint')"
                                    required
                                ></textarea>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">
                                    {{ $t('words.footer') }} ({{ $t('words.optional') }})
                                </label>
                                <input
                                    v-model="form.footer"
                                    type="text"
                                    class="w-full form-input text-sm rounded-lg border-gray-300"
                                    maxlength="60"
                                />
                            </div>

                            <div v-if="detectedVariables.length" class="space-y-2">
                                <div class="text-xs font-semibold text-gray-700">
                                    {{ $t('words.template-variables') }} — {{ $t('words.examples') }}
                                </div>
                                <div
                                    v-for="variableKey in detectedVariables"
                                    :key="variableKey"
                                >
                                    <label class="text-[11px] text-gray-500">
                                        {{ $t('words.template-variable') }} {{ variableKey }}
                                    </label>
                                    <input
                                        v-model="form.variable_samples[variableKey]"
                                        type="text"
                                        class="w-full form-input text-sm rounded-lg border-gray-300"
                                        :placeholder="'example' + variableKey"
                                    />
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-2 pt-2 border-t">
                                <button
                                    type="button"
                                    @click="backToList"
                                    class="px-3 py-1.5 rounded-lg text-xs font-semibold border bg-white hover:bg-gray-50 text-gray-700"
                                >
                                    {{ $t('words.cancel') }}
                                </button>
                                <button
                                    type="submit"
                                    :disabled="saving"
                                    class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-green-600 hover:bg-green-700 text-white disabled:opacity-50"
                                >
                                    {{ saving ? ($t('words.loading') + '...') : $t('words.save') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </modal>
        </portal>
    </div>
</template>

<script>
import axios from 'axios';

const emptyForm = () => ({
    name: '',
    category: 'UTILITY',
    language: 'ar',
    header: '',
    body: '',
    footer: '',
    variable_samples: {},
});

export default {
    name: 'WhatsAppTemplatesManager',
    props: {
        listRoute: {
            type: String,
            required: true,
        },
        storeRoute: {
            type: String,
            required: true,
        },
        updateRouteTemplate: {
            type: String,
            required: true,
        },
        destroyRouteTemplate: {
            type: String,
            required: true,
        },
        canManage: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        return {
            loading: false,
            saving: false,
            deletingSid: null,
            templates: [],
            view: 'list',
            editingSid: null,
            form: emptyForm(),
            errorMessage: '',
            successMessage: '',
        };
    },
    computed: {
        detectedVariables() {
            const matches = this.form.body.match(/\{\{(\d+)\}\}/g) || [];
            const keys = [...new Set(matches.map((m) => m.replace(/[{}]/g, '')))];
            keys.sort((a, b) => Number(a) - Number(b));
            return keys;
        },
    },
    methods: {
        open() {
            this.errorMessage = '';
            this.successMessage = '';
            this.view = 'list';
            this.$modal.show('whatsappTemplatesManagerModal');
            this.loadTemplates();
        },
        close() {
            this.$modal.hide('whatsappTemplatesManagerModal');
        },
        backToList() {
            this.view = 'list';
            this.editingSid = null;
            this.form = emptyForm();
            this.errorMessage = '';
        },
        async loadTemplates() {
            this.loading = true;
            this.errorMessage = '';
            try {
                const { data } = await axios.get(this.listRoute);
                this.templates = data.templates || [];
                this.$emit('templates-updated', this.templates);
            } catch (e) {
                this.errorMessage = (e.response && e.response.data && e.response.data.message)
                    || this.$t('words.whatsapp-templates-load-failed');
            } finally {
                this.loading = false;
            }
        },
        startCreate() {
            this.view = 'create';
            this.editingSid = null;
            this.form = emptyForm();
            this.errorMessage = '';
            this.successMessage = '';
        },
        startEdit(template) {
            this.view = 'edit';
            this.editingSid = template.sid;
            this.form = {
                name: template.friendly_name || '',
                category: template.category || 'UTILITY',
                language: template.language || 'ar',
                header: template.header || '',
                body: template.body || '',
                footer: template.footer || '',
                variable_samples: { ...(template.variable_samples || {}) },
            };
            this.errorMessage = '';
            this.successMessage = '';
        },
        routeWithSid(template, sid) {
            return template.replace('__SID__', encodeURIComponent(sid));
        },
        async submitForm() {
            this.saving = true;
            this.errorMessage = '';
            this.successMessage = '';

            const payload = {
                body: this.form.body,
                header: this.form.header || null,
                footer: this.form.footer || null,
                variable_samples: this.form.variable_samples,
            };

            try {
                if (this.view === 'create') {
                    await axios.post(this.storeRoute, {
                        ...payload,
                        name: this.form.name.trim().toLowerCase(),
                        category: this.form.category,
                        language: this.form.language,
                    });
                    this.successMessage = this.$t('words.whatsapp-template-created');
                } else {
                    await axios.patch(this.routeWithSid(this.updateRouteTemplate, this.editingSid), payload);
                    this.successMessage = this.$t('words.whatsapp-template-updated');
                }
                await this.loadTemplates();
                this.backToList();
            } catch (e) {
                this.errorMessage = (e.response && e.response.data && e.response.data.message)
                    || this.$t('words.whatsapp-template-save-failed');
            } finally {
                this.saving = false;
            }
        },
        async confirmDelete(template) {
            if (!window.confirm(this.$t('words.whatsapp-template-delete-confirm'))) {
                return;
            }

            this.deletingSid = template.sid;
            this.errorMessage = '';
            this.successMessage = '';

            try {
                await axios.delete(this.routeWithSid(this.destroyRouteTemplate, template.sid));
                this.successMessage = this.$t('words.whatsapp-template-deleted');
                await this.loadTemplates();
            } catch (e) {
                this.errorMessage = (e.response && e.response.data && e.response.data.message)
                    || this.$t('words.whatsapp-template-delete-failed');
            } finally {
                this.deletingSid = null;
            }
        },
    },
};
</script>
