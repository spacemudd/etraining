<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'companies', link: route('back.companies.index')},
                    {title_raw: company.name_ar},
                ]"
            ></breadcrumb-container>

            <div class="grid grid-cols-4 gap-6">
                <div class="col-span-4 flex items-center justify-end bg-gray-50 text-right">
                    <inertia-link
                        :href="`/back/companies/${this.company.id}/edit`"
                        class="flex items-center justify-start rounded-md mx-4 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-right"
                    >
                        {{ $t('words.edit') }}
                    </inertia-link>
                    <button
                        v-if="!company.deleted_at"
                        class="flex items-center justify-start rounded-md px-4 py-2 bg-red-600 text-white hover:bg-red-700 text-right"
                        tabindex="-1"
                        type="button"
                        @click.prevent="deleteCompany()"
                    >
                        {{ $t('words.archive') }}
                    </button>
                </div>

                <template
                    v-for="fieldName in [
                            'name_ar',
                            'name_en',
                            'cr_number',
                            'contact_number',
                            'company_rep',
                            'company_rep_mobile',
                            'address',
                            'email',
                            'monthly_subscription_per_trainee',
                            ]"
                >
                    <div class="col-span-4 sm:col-span-1">
                        <jet-label
                            for="name"
                            :value="$t('words.'+fieldName)"
                        />
                        <jet-input
                            :id="fieldName"
                            type="text"
                            class="mt-1 block w-full bg-gray-200"
                            :value="company[fieldName]"
                            disabled
                        />
                    </div>
                </template>
            </div>

            <jet-section-border></jet-section-border>

            <div
                v-can="'view-company-contracts'"
                class="grid grid-cols-1 md:grid-cols-6 gap-6 mt-2"
            >
                <div class="md:col-span-2 sm:col-span-3">
                    <div class="px-4 sm:px-0">
                        <h3 class="text-lg font-medium text-gray-900">
                            {{ $t('words.contracts') }}
                        </h3>

                        <p class="mt-1 text-sm text-gray-600">
                            {{ $t('words.contracts-help') }}
                        </p>
                    </div>
                </div>

                <div class="md:col-span-4 sm:col-span-1">
                    <company-contracts-pagination
                        :company-id="company.id"
                        :instructors="instructors"
                    />
                </div>
            </div>

            <jet-section-border></jet-section-border>

            <div
                v-can="'view-company-contracts'"
                class="grid grid-cols-1 md:grid-cols-6 gap-6 mt-2"
            >
                <div class="md:col-span-2 sm:col-span-3">
                    <div class="px-4 sm:px-0">
                        <h3 class="text-lg font-medium text-gray-900">
                            {{ $t('words.trainees') }}
                            <span v-if="company.trainees_count">({{ company.trainees_count }})</span>
                            <span v-if="trainees_trashed_count">({{ trainees_trashed_count }})</span>
                        </h3>
                        <inertia-link class="text-sm mt-2 text-blue-500 hover:text-blue-700"
                                      :href="route('back.companies.trainees.activity-log', company.id)">
                            {{ $t('words.activity-log') }}
                        </inertia-link>
                    </div>
                </div>

                <div class="md:col-span-4 sm:col-span-1">
                    <div class="flex justify-end items-center gap-4">
                        <post-trainees-button :company-id="company.id"></post-trainees-button>
                        <inertia-link class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150"
                           :href="route('back.companies.trainees.company-trainee-link-audit', {company_id: company.id})">
                            <span>{{ $t('words.history') }}</span>
                        </inertia-link>
                        <a class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150"
                           target="_blank"
                           :href="route('back.companies.trainees.excel', {company_id: company.id})">
                            <span>{{ $t('words.export') }}</span>
                        </a>
                    </div>

                    <table class="w-full whitespace-no-wrap bg-white rounded-lg my-5 p-5 shadow text-sm">
                        <tr class="text-left font-bold">
                            <th class="p-4">{{ $t('words.name') }}</th>
                            <th class="p-4">{{ $t('words.group-name') }}</th>
                            <th class="p-4">{{ $t('words.phone') }}</th>
                        </tr>
                        <tr
                            v-for="trainees in company.trainees"
                            :key="company.trainees.id"
                            class="hover:bg-gray-100 focus-within:bg-gray-100"
                        >
                            <td class="border-t">
                                <div class="px-4 py-2 flex items-center focus:text-indigo-500">
                                    <inertia-link :href="trainees.show_url">
                                        <span
                                            v-if="trainees.deleted_at"
                                            class="inline-block mt-2 p-1 px-2 bg-red-600 text-white rounded-lg"
                                        >
                                            {{ $t('words.blocked') }}
                                        </span>
                                        <span
                                            v-if="trainees.is_pending_uploading_files"
                                            class="inline-block mt-2 p-1 px-2 bg-blue-300 rounded-lg"
                                        >
                                            {{ $t('words.incomplete-application') }}
                                        </span>
                                        <span
                                            v-if="trainees.is_pending_approval"
                                            class="inline-block mt-2 p-1 px-2 bg-yellow-200 rounded-lg"
                                        >
                                            {{ $t('words.nominated-instructor') }}
                                        </span>
                                        <span
                                            v-if="trainees.is_approved"
                                            class="inline-block mt-2 p-1 px-2 bg-green-300 rounded-lg"
                                        >
                                            {{ $t('words.approved') }}
                                        </span>
                                        {{ trainees.name }}
                                    </inertia-link>
                                </div>
                            </td>
                            <td class="border-t">
                                <inertia-link
                                    id="group-name"
                                    class="px-4 py-2 flex items-center"
                                    :href="route('back.trainees.show', trainees.id)"
                                    tabindex="-1"
                                >
                                    <div v-if="trainees.trainee_group_id">
                                        {{ trainees.trainee_group.name }}
                                    </div>
                                </inertia-link>
                            </td>
                            <td class="border-t">
                                <inertia-link
                                    class="px-4 py-2 flex items-center"
                                    :href="route('back.trainees.show', trainees.id)"
                                    tabindex="-1"
                                >
                                    <div v-if="trainees.phone">
                                        {{ trainees.phone }}
                                    </div>
                                </inertia-link>
                            </td>

                            <td class="border-t w-px">
                                <inertia-link
                                    class="px-4 flex items-center"
                                    :href="route('back.trainees.show', trainees.id)"
                                    tabindex="-1"
                                >
                                    <ion-icon
                                        name="arrow-forward-outline"
                                        class="block w-6 h-6 fill-gray-400"
                                    ></ion-icon>
                                </inertia-link>
                            </td>

                        </tr>
                        <tr v-if="company.trainees.length === 0">
                            <td
                                class="border-t px-4 py-4"
                                colspan="4"
                            >
                                <empty-slate />
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <jet-section-border></jet-section-border>

            <div
                v-can="'issue-monthly-invoices'"
                class="grid grid-cols-1 gap-6 mt-2"
            >
                <div class="flex justify-between items-center">
                    <div class="px-4 sm:px-0">
                        <h3 class="text-lg font-medium text-gray-900">
                            {{ $t('words.invoices') }}
                        </h3>
                    </div>

                    <inertia-link
                        class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150"
                        :href="route('back.companies.invoices.create', {company: company.id})"
                    >
                        <span>{{ $t('words.issue-invoice') }}</span>
                    </inertia-link>
                </div>

                <div>
                    <div class="flex">
                        <div class="flex ml-2">
                            <h3>{{ $t('words.from-date') }}</h3>
                            <input class="bg-gray-200 mx-1 px-2 rounded-sm" type="date" v-model="reportDateFrom">
                        </div>
                        <div class="flex mx-2">
                            <h3>{{ $t('words.to-date') }}</h3>
                            <input class="bg-gray-200 mx-1 px-2 rounded-sm" type="date" v-model="reportDateTo">
                        </div>
                        <a class="mx-2"
                           :href="route('back.companies.invoices.bulk-pdf', {
                            company_id: company.id,
                            from_date: reportDateFrom,
                            to_date: reportDateTo})"
                           target="_blank">
                            {{ $t('words.print') }}
                        </a>
                    </div>
                    <table class="w-full whitespace-no-wrap bg-white rounded-lg my-5 p-5 shadow text-sm">
                        <tr class="text-left font-bold">
                            <th class="p-4">{{ $t('words.date-created') }}</th>
                            <th class="p-4">{{ $t('words.date-period') }}</th>
                            <th class="p-4">{{ $t('words.trainees') }}</th>
                            <th class="p-4">{{ $t('words.grand-total') }}</th>
                            <th class="p-4">{{ $t('words.initiated-by') }}</th>
                            <th class="p-4">{{ $t('words.actions') }}</th>
                        </tr>
                        <tr
                            v-for="invoice in invoices"
                            :key="invoice.id"
                            class="border-t hover:bg-gray-100 focus-within:bg-gray-100"
                        >
                            <td class="px-4 py-4">
                                {{ invoice.created_at_date }}
                            </td>

                            <td class="px-4 py-4">
                                {{ invoice.from_date | formatDate }}
                                <br />
                                {{ invoice.to_date | formatDate }}
                            </td>

                            <td class="px-4 py-4">
                                {{ invoice.trainee_count }}
                            </td>

                            <td class="px-4 py-4">
                                {{ invoice.grand_total }}
                            </td>

                            <td class="px-4 py-4">
                                {{ invoice.created_by ? invoice.created_by.name : 'Unknown' }}
                            </td>

                            <td class="w-px">
                                <a
                                    class="px-4 flex items-center"
                                    :href="route('back.companies.invoices.pdf', {company_id: company.id, from_date: invoice.from_date, to_date: invoice.to_date, created_by_id: invoice.created_by_id, created_at_date: invoice.created_at_date,})"
                                    tabindex="-1"
                                    target="_blank"
                                >
                                    {{ $t('words.download') }}
                                    <ion-icon
                                        name="arrow-forward-outline"
                                        class="block w-6 h-6 fill-gray-400"
                                    ></ion-icon>
                                </a>
                            </td>
                        </tr>

                        <tr v-if="invoices.length === 0">
                            <td
                                class="border-t px-4 py-4"
                                colspan="6"
                            >
                                <empty-slate />
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import JetSectionBorder from '@/Jetstream/SectionBorder'
import Breadcrumb from "@/Components/Breadcrumb";
import JetDialogModal from '@/Jetstream/DialogModal'
import JetInput from '@/Jetstream/Input'
import JetInputError from '@/Jetstream/InputError'
import JetActionMessage from '@/Jetstream/ActionMessage';
import JetButton from '@/Jetstream/Button';
import JetFormSection from '@/Jetstream/FormSection';
import JetLabel from '@/Jetstream/Label';
import CompanyContractsPagination from "@/Components/CompanyContractsPagination";
import BreadcrumbContainer from "@/Components/BreadcrumbContainer";
import EmptySlate from "@/Components/EmptySlate";
import PostTraineesButton from "@/Components/PostTraineesButton";

export default {
    props: ['sessions', 'company', 'instructors', 'invoices', 'trainees_trashed_count'],

    components: {
        PostTraineesButton,
        AppLayout,
        JetSectionBorder,
        Breadcrumb,
        JetDialogModal,
        JetInput,
        JetInputError,
        JetActionMessage,
        JetButton,
        JetFormSection,
        JetLabel,
        CompanyContractsPagination,
        BreadcrumbContainer,
        EmptySlate,
    },
    data() {
        return {
            reportDateFrom: null,
            reportDateTo: null,
            form: this.$inertia.form({
                name_ar: '',
                name_en: '',
                cr_number: '',
                contact_number: '',
                company_rep: '',
                company_rep_mobile: '',
                address: '',
                email: '',
                monthly_subscription_per_trainee: '',
            }, {
                bag: 'createCompany',
            })
        }
    },
    methods: {
        createCompany() {
            this.form.post('/back/companies', {
                preserveScroll: true
            });
        },
        deleteCompany() {
            if (confirm(this.$t('words.are-you-sure'))) {
                this.$inertia.delete('/back/companies/' + this.company.id);
            }
        }
    }
}
</script>
