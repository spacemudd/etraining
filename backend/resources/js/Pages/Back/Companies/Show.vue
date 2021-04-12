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
                    <inertia-link :href="`/back/companies/${this.company.id}/edit`" class="flex items-center justify-start rounded-md mx-4 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-right">
                        {{ $t('words.edit') }}
                    </inertia-link>
                    <button v-if="!company.deleted_at" class="flex items-center justify-start rounded-md px-4 py-2 bg-red-600 text-white hover:bg-red-700 text-right"
                            tabindex="-1"
                            type="button"
                            @click.prevent="deleteCompany()">
                        {{ $t('words.archive') }}
                    </button>
                </div>

                <template v-for="fieldName in [
                            'name_ar',
                            'name_en',
                            'cr_number',
                            'contact_number',
                            'company_rep',
                            'company_rep_mobile',
                            'address',
                            'email',
                            ]">
                    <div class="col-span-4 sm:col-span-1">
                        <jet-label for="name" :value="$t('words.'+fieldName)" />
                        <jet-input :id="fieldName" type="text" class="mt-1 block w-full bg-gray-200" :value="company[fieldName]" disabled />
                    </div>
                </template>
            </div>

            <jet-section-border></jet-section-border>

            <div v-can="'view-company-contracts'" class="grid grid-cols-1 md:grid-cols-6 gap-6 mt-2">
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
                    <company-contracts-pagination :company-id="company.id" :instructors="instructors" />
                </div>
            </div>

            <jet-section-border></jet-section-border>

            <div v-can="'view-company-contracts'" class="grid grid-cols-1 md:grid-cols-6 gap-6 mt-2">
                <div class="md:col-span-2 sm:col-span-3">
                    <div class="px-4 sm:px-0">
                        <h3 class="text-lg font-medium text-gray-900">
                            {{ $t('words.trainees') }} <span v-if="company.trainees_count">({{ company.trainees_count }})</span>
                        </h3>
                    </div>
                </div>

                <div class="md:col-span-4 sm:col-span-1">
                    <div class="flex justify-end items-center">
                        <a class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150"
                           target="_blank" :href="route('back.companies.trainees.excel', {company_id: company.id})">
                            <span>{{ $t('words.export') }}</span>
                        </a>
                    </div>

                    <table class="w-full whitespace-no-wrap bg-white rounded-lg my-5 p-5 shadow text-sm">
                        <tr class="text-left font-bold">
                            <th class="p-4">{{ $t('words.name') }}</th>
                            <th class="p-4">{{ $t('words.phone') }}</th>
                        </tr>
                        <tr v-for="trainees in company.trainees" :key="company.trainees.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
                            <td class="border-t">
                                <div class="px-4 py-2 flex items-center focus:text-indigo-500">
                                    <inertia-link :href="route('back.trainees.show', trainees.id)">
                                        <span v-if="trainees.is_pending_uploading_files" class="inline-block mt-2 p-1 px-2 bg-blue-300 rounded-lg">
                                            {{ $t('words.incomplete-application') }}
                                        </span>
                                        <span v-if="trainees.is_pending_approval" class="inline-block mt-2 p-1 px-2 bg-yellow-200 rounded-lg">
                                            {{ $t('words.nominated-instructor') }}
                                        </span>
                                        <span v-if="trainees.is_approved" class="inline-block mt-2 p-1 px-2 bg-green-300 rounded-lg">
                                            {{ $t('words.approved') }}
                                        </span>
                                        {{ trainees.name }}
                                    </inertia-link>
                                </div>
                            </td>
                            <td class="border-t">
                                <inertia-link class="px-4 py-2 flex items-center" :href="route('back.trainees.show', trainees.id)" tabindex="-1">
                                    <div v-if="trainees.phone">
                                        {{ trainees.phone }}
                                    </div>
                                </inertia-link>
                            </td>

                            <td class="border-t w-px">
                                <inertia-link class="px-4 flex items-center" :href="route('back.trainees.show', trainees.id)" tabindex="-1">
                                    <ion-icon name="arrow-forward-outline" class="block w-6 h-6 fill-gray-400"></ion-icon>
                                </inertia-link>
                            </td>

                        </tr>
                        <tr v-if="company.trainees.length === 0">
                            <td class="border-t px-4 py-4" colspan="4">
                                <empty-slate/>
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

export default {
    props: ['sessions', 'company', 'instructors'],

    components: {
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
    },
    data() {
        return {
            form: this.$inertia.form({
                name_ar: '',
                name_en: '',
                cr_number: '',
                contact_number: '',
                company_rep: '',
                company_rep_mobile: '',
                address: '',
                email: '',
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
                this.$inertia.delete('/back/companies/'+this.company.id);
            }
        }
    }
}
</script>
