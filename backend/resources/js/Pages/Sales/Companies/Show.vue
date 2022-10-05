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
                <div class="col-span-3 bg-gray-50">
                    <span>الشركة</span>
                    <h1 class="text-4xl">{{ company.name_ar }}</h1>
                    <div class="flex justify-between">
                        <div class="bg-green-600 text-center text-white p-2" style="border-radius:50%;">
                            <p>(3)</p>
                            <p>التواصل</p>
                        </div>
                    </div>
                    <!--<div class="flex justify-between">-->
                    <!--    <div class="bg-green-600 text-center text-white p-2" style="border-radius:50%;">-->
                    <!--        <p>(3)</p>-->
                    <!--        <p>عرض السعر</p>-->
                    <!--    </div>-->
                    <!--</div>-->
                    <!--<div class="flex justify-between">-->
                    <!--    <div class="bg-green-600 text-center text-white p-2" style="border-radius:50%;">-->
                    <!--        <p>(3)</p>-->
                    <!--        <p>العقد</p>-->
                    <!--    </div>-->
                    <!--</div>-->
                    <!--<div class="flex justify-between">-->
                    <!--    <div class="bg-green-600 text-center text-white p-2" style="border-radius:50%;">-->
                    <!--        <p>(3)</p>-->
                    <!--        <p>الإستقطاب</p>-->
                    <!--    </div>-->
                    <!--</div>-->
                </div>

                <div class="col-span-1">
                    <div class="flex justify-between">
                        <p>جهات الإتصال</p>
                        <jet-button class="rtl:mr-5 ltr:ml-5">
                            جديد
                        </jet-button>
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
