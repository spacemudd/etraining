<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'companies', link: route('sales.companies.index')},
                    {title_raw: company.name_ar},
                ]"
            ></breadcrumb-container>

            <div class="grid grid-cols-4 gap-6">
                <div class="col-span-4 bg-gray-50">
                    <div class="flex justify-between mt-5 w-full">
                        <div class="w-2/12">
                            <div class="bg-blue-600 text-center text-white p-2 rounded-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mx-auto w-6 h-6">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                                </svg>
                            </div>
                            <p class="text-xs mt-1 text-center">{{ $t('words.comms') }}</p>
                        </div>

                        <div class="w-2/12">
                            <div class="bg-gray-200 text-center text-white p-2 rounded-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mx-auto w-6 h-6">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5m.75-9l3-3 2.148 2.148A12.061 12.061 0 0116.5 7.605" />
                                </svg>
                            </div>
                            <p class="text-xs mt-1 text-center">{{ $t('words.offers') }}</p>
                        </div>

                        <div class="w-2/12">
                            <div class="bg-gray-200 text-center text-white p-2 rounded-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mx-auto w-6 h-6">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                </svg>
                            </div>
                            <p class="text-xs mt-1 text-center">{{ $t('words.contracts') }}</p>
                        </div>

                        <div class="w-2/12">
                            <div class="bg-gray-200 text-center text-white p-2 rounded-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mx-auto w-6 h-6">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 7.5h-.75A2.25 2.25 0 004.5 9.75v7.5a2.25 2.25 0 002.25 2.25h7.5a2.25 2.25 0 002.25-2.25v-7.5a2.25 2.25 0 00-2.25-2.25h-.75m-6 3.75l3 3m0 0l3-3m-3 3V1.5m6 9h.75a2.25 2.25 0 012.25 2.25v7.5a2.25 2.25 0 01-2.25 2.25h-7.5a2.25 2.25 0 01-2.25-2.25v-.75" />
                                </svg>
                            </div>
                            <p class="text-xs mt-1 text-center">{{ $t('words.recruitment') }}</p>
                        </div>
                    </div>
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
    props: ['company'],

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
        deleteInvoice(invoiceCollection) {
            if (confirm(this.$t('words.are-you-sure'))) {
                this.$inertia.delete(route('back.finance.invoices.destroy', {
                    invoice: invoiceCollection.id,
                    from_date: invoiceCollection.from_date,
                    to_date: invoiceCollection.to_date,
                    created_at_date: invoiceCollection.created_at_date,
                    created_by_id: invoiceCollection.created_by_id,
                    company_id: invoiceCollection.company_id,

                }));
            }
        },
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
