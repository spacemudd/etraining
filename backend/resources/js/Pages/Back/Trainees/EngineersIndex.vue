<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    { title: 'dashboard', link: route('dashboard') },
                    { title: 'trainees', link: route('back.trainees.index') },
                    { title: 'engineers' },
                ]"
            />

            <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                <h1 class="font-bold text-2xl">
                    {{ $t('words.engineers') }}
                    <span v-if="trainees.total" class="text-base font-normal text-gray-500">({{ trainees.total }})</span>
                </h1>
            </div>

            <div class="mb-4 flex flex-nowrap items-end gap-2">
                <div class="flex-1 min-w-[140px]">
                    <label class="block text-xs font-medium text-gray-500 mb-1">{{ $t('words.search') }}</label>
                    <input
                        v-model="form.search"
                        type="text"
                        class="form-input text-sm py-1.5 rounded-md shadow-sm block w-full"
                        :placeholder="$t('words.search')"
                    />
                </div>
                <div class="w-48 flex-shrink-0">
                    <label class="block text-xs font-medium text-gray-500 mb-1">{{ $t('words.company') }}</label>
                    <company-search-select
                        :value="selectedCompanyFilter"
                        compact
                        allow-unassigned
                        :placeholder="$t('words.all')"
                        @input="onCompanySelected"
                    />
                </div>
                <div class="w-36 flex-shrink-0">
                    <label class="block text-xs font-medium text-gray-500 mb-1">{{ $t('words.status') }}</label>
                    <select v-model="form.status" class="form-select text-sm py-1.5 rounded-md shadow-sm block w-full">
                        <option value="">{{ $t('words.all') }}</option>
                        <option value="0">{{ $t('words.incomplete-application') }}</option>
                        <option value="1">{{ $t('words.nominated-instructor') }}</option>
                        <option value="2">{{ $t('words.approved') }}</option>
                    </select>
                </div>
                <button
                    type="button"
                    class="flex-shrink-0 whitespace-nowrap text-xs font-medium py-1.5 px-2 rounded-md border border-gray-300 text-gray-600 bg-gray-50 hover:bg-gray-100 shadow-sm"
                    @click="reset"
                >
                    {{ $t('words.clear') }}
                </button>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                    {{ $t('words.name') }}
                                </th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide whitespace-nowrap">
                                    {{ $t('words.identity_number') }}
                                </th>
                                <th v-if="!isSaraView" class="px-3 py-2 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide whitespace-nowrap">
                                    {{ $t('words.phone') }}
                                </th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                    {{ $t('words.company') }}
                                </th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide whitespace-nowrap">
                                    {{ $t('words.last-login-to-platform') }}
                                </th>
                                <th class="px-2 py-2 w-8" aria-hidden="true"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr
                                v-for="row in trainees.data"
                                :key="row.id"
                                class="hover:bg-gray-50 cursor-pointer group"
                                @click="goToTrainee(row.id)"
                            >
                                <td class="px-3 py-2">
                                    <div class="font-medium text-gray-900 leading-tight">{{ row.name }}</div>
                                    <div v-if="!isSaraView && hasStatusBadge(row)" class="mt-1 flex flex-wrap gap-1">
                                        <span
                                            v-if="row.is_pending_uploading_files"
                                            class="text-xs px-1.5 py-0.5 bg-blue-100 text-blue-800 rounded"
                                        >
                                            {{ $t('words.incomplete-application') }}
                                        </span>
                                        <span
                                            v-if="row.is_pending_approval"
                                            class="text-xs px-1.5 py-0.5 bg-yellow-100 text-yellow-800 rounded"
                                        >
                                            {{ $t('words.nominated-instructor') }}
                                        </span>
                                        <span
                                            v-if="row.is_approved"
                                            class="text-xs px-1.5 py-0.5 bg-green-100 text-green-800 rounded"
                                        >
                                            {{ $t('words.approved') }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-3 py-2 text-gray-700 whitespace-nowrap font-mono text-xs">
                                    {{ row.identity_number || '—' }}
                                </td>
                                <td v-if="!isSaraView" class="px-3 py-2 text-gray-700 whitespace-nowrap text-xs">
                                    <span dir="ltr" class="block text-right">{{ row.phone || '—' }}</span>
                                </td>
                                <td class="px-3 py-2 text-gray-700 max-w-[200px]">
                                    <div class="truncate" :title="row.company ? row.company.name_ar : ''">
                                        <span v-if="row.company">{{ row.company.name_ar }}</span>
                                        <span v-else class="text-gray-400 italic text-xs">{{ $t('words.not-assigned-to-a-company') }}</span>
                                    </div>
                                    <div v-if="row.trainee_group" class="text-xs text-gray-400 truncate">
                                        {{ row.trainee_group.name }}
                                    </div>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <span
                                        v-if="row.user && row.user.last_login_at_timezone"
                                        class="text-xs text-gray-600"
                                        dir="ltr"
                                    >
                                        {{ row.user.last_login_at_timezone }}
                                    </span>
                                    <span v-else-if="row.user" class="text-xs text-gray-400 italic">
                                        {{ $t('words.trainee-never-logged-in') }}
                                    </span>
                                    <span v-else class="text-xs text-gray-400 italic">
                                        {{ $t('words.trainee-has-no-account') }}
                                    </span>
                                </td>
                                <td class="px-2 py-2 text-gray-300 group-hover:text-gray-500">
                                    <ion-icon name="arrow-forward-outline" class="w-4 h-4" />
                                </td>
                            </tr>
                            <tr v-if="trainees.data.length === 0">
                                <td class="px-3 py-8" :colspan="isSaraView ? 5 : 6">
                                    <empty-slate />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-if="trainees.data.length" class="px-3 py-2 border-t border-gray-100 bg-gray-50 text-xs text-gray-500">
                    {{ paginationSummary }}
                </div>
                <div class="engineers-pagination px-2 pb-2 border-t border-gray-100">
                    <pagination :links="trainees.links" />
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout';
import BreadcrumbContainer from '@/Components/BreadcrumbContainer';
import CompanySearchSelect from '@/Components/CompanySearchSelect';
import EmptySlate from '@/Components/EmptySlate';
import Pagination from '@/Shared/Pagination';
import mapValues from 'lodash/mapValues';
import pickBy from 'lodash/pickBy';
import throttle from 'lodash/throttle';

export default {
    metaInfo: { title: 'Engineers' },
    components: {
        AppLayout,
        BreadcrumbContainer,
        CompanySearchSelect,
        EmptySlate,
        Pagination,
    },
    props: {
        trainees: { type: Object, required: true },
        filters: {
            type: Object,
            default: () => ({}),
        },
        selectedCompany: {
            type: Object,
            default: null,
        },
        isSaraView: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        return {
            selectedCompanyFilter: null,
            form: {
                search: this.filters.search || '',
                company_id: this.filters.company_id || '',
                status: this.filters.status !== undefined && this.filters.status !== null
                    ? String(this.filters.status)
                    : '',
            },
        };
    },
    created() {
        this.selectedCompanyFilter = this.buildSelectedCompany();
    },
    computed: {
        paginationSummary() {
            const { from, to, total } = this.trainees;
            if (!from || !total) {
                return '';
            }
            return `${from}–${to} / ${total}`;
        },
    },
    watch: {
        selectedCompany(company) {
            if (company) {
                this.selectedCompanyFilter = company;
            } else if (this.filters.company_id === 'none') {
                this.selectedCompanyFilter = {
                    id: 'none',
                    name_ar: this.$t('words.not-assigned-to-a-company'),
                };
            } else if (!this.filters.company_id) {
                this.selectedCompanyFilter = null;
            }
        },
        form: {
            handler: throttle(function () {
                const query = pickBy(this.form, (value) => value !== '' && value !== null);
                this.$inertia.get(
                    this.route('back.trainees.engineers.index'),
                    Object.keys(query).length ? query : {},
                    { preserveState: true, replace: true }
                );
            }, 300),
            deep: true,
        },
    },
    methods: {
        buildSelectedCompany() {
            if (this.filters.company_id === 'none') {
                return {
                    id: 'none',
                    name_ar: this.$t('words.not-assigned-to-a-company'),
                };
            }

            return this.selectedCompany;
        },
        onCompanySelected(company) {
            this.selectedCompanyFilter = company;

            if (! company) {
                this.form.company_id = '';
                return;
            }

            this.form.company_id = company.id === 'none' ? 'none' : company.id;
        },
        hasStatusBadge(row) {
            return row.is_pending_uploading_files || row.is_pending_approval || row.is_approved;
        },
        reset() {
            this.selectedCompanyFilter = null;
            this.form = mapValues(this.form, () => '');
        },
        goToTrainee(traineeId) {
            this.$inertia.visit(this.route('back.trainees.show', traineeId));
        },
    },
};
</script>

<style scoped>
.engineers-pagination >>> .mt-6 {
    margin-top: 0.5rem;
}

.engineers-pagination >>> a,
.engineers-pagination >>> div {
    padding: 0.2rem 0.5rem;
    font-size: 0.75rem;
    line-height: 1.25rem;
    margin-bottom: 0.25rem;
}
</style>
