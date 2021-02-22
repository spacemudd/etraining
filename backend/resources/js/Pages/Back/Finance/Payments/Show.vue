<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'finance', link: route('back.finance')},
                    {title: 'payments', link: route('back.finance.payments.index')},
                    {title_raw: payment.short_code},
                ]"
            ></breadcrumb-container>

            <div class="grid grid-cols-12 gap-12">
                <div class="col-span-4">
                    <div class="bg-red-500">
                        ??
                    </div>
                </div>
                <div class="col-span-3 bg-blue-400">
                    {{ payment }}
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
    import JetDangerButton from '@/Jetstream/DangerButton'
    import JetInput from '@/Jetstream/Input'
    import JetInputError from '@/Jetstream/InputError'
    import JetActionMessage from '@/Jetstream/ActionMessage';
    import JetButton from '@/Jetstream/Button';
    import JetFormSection from '@/Jetstream/FormSection';
    import JetLabel from '@/Jetstream/Label';
    import CompanyContractsPagination from "@/Components/CompanyContractsPagination";
    import BreadcrumbContainer from "@/Components/BreadcrumbContainer";
    import { SweetModal, SweetModalTab } from 'sweet-modal-vue';
    import ApproveInvoicesBatch from '@/Components/ApproveInvoicesBatch';
    import MonthlyInvoicingJobStatus from '@/Components/MonthlyInvoicingJobStatus';
    import BtnLoadingIndicator from "@/Components/BtnLoadingIndicator";
    import Pagination from '@/Shared/Pagination';

    export default {
        props: ['payment'],

        components: {
            Pagination,
            BtnLoadingIndicator,
            MonthlyInvoicingJobStatus,
            ApproveInvoicesBatch,
            SweetModal,
            SweetModalTab,
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
            JetDangerButton,
        },
        data() {
            return {
                refreshInvoiceInterval: null,

                invoicingBatch: null,
            }
        },
        mounted() {

        },
        methods: {
            setupRefreshInterval() {
                if (this.batch.is_processing) {
                    this.refreshInvoiceInterval = setTimeout(this.updateInvoiceBatch(), 1000);
                }
            },
            deleteMonthlyInvoicingBatch() {
                if (confirm(this.$t('words.are-you-sure'))) {
                    this.$inertia.delete(route('back.finance.invoicing.delete', this.batch.id));
                }
            },
            updateInvoiceBatch() {
                let vm = this;
                setTimeout(function() {
                    axios.get(route('back.finance.invoicing.show', vm.batch.id), {headers: {'Accept': 'text/json'}})
                        .then(response => {
                            vm.invoicingBatch = response.data;
                            if (response.data.is_processing) {
                                vm.updateInvoiceBatch();
                            }
                        })
                }, 1000);

            },
        },
    }
</script>
