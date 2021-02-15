<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'finance', link: route('back.finance')},
                    {title: 'monthly-invoicing', link: route('back.finance.invoicing.index')},
                    {title_raw: batch.invoices_date},
                ]"
            ></breadcrumb-container>

            <div class="flex justify-end mb-10">
                <jet-danger-button @click.native="deleteMonthlyInvoicingBatch()">
                    {{ $t('words.delete') }}
                </jet-danger-button>
            </div>

            <div class="grid grid-cols-1 gap-2 md:grid-cols-4 md:gap-4">
                <div class="col-span-1">
                    <div class="bg-gray-100 shadow-lg border rounded-lg p-5">
                        <table class="table w-full">
                            <colgroup>
                                <col style="width:50%;">
                            </colgroup>
                        	<tbody>
                                <tr>
                                    <td>{{ $t('words.period-from') }}</td>
                                    <td class="justify-content-end">{{ batch.period_from }}</td>
                                </tr>
                                <tr>
                                    <td>{{ $t('words.period-to') }}</td>
                                    <td class="justify-content-end">{{ batch.period_to }}</td>
                                </tr>
                                <tr>
                                    <td colspan="2"><hr></td>
                                </tr>
                                <tr>
                                    <td>{{ $t('words.date') }}</td>
                                    <td class="justify-content-end">{{ batch.invoices_date }}</td>
                                </tr>
                                <tr>
                                    <td>{{ $t('words.trainees') }}</td>
                                    <td>{{ batch.total }}</td>
                                </tr>
                                <tr>
                                    <td>{{ $t('words.status') }}</td>
                                    <td>{{ batch.status_display }}</td>
                                </tr>
                                <tr>
                                    <td>{{ $t('words.created-by') }}</td>
                                    <td v-if="batch.created_by">{{ batch.created_by.name }}</td>
                                </tr>
                        	</tbody>
                        </table>
                    </div>
                </div>
                <div class="bg-red-500 col-span-3">2</div>
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

    export default {
        props: ['batch'],

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
            JetDangerButton
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
            deleteMonthlyInvoicingBatch() {
                if (confirm(this.$t('words.are-you-sure'))) {
                    this.$inertia.delete(route('back.finance.invoicing.delete', this.batch.id));
                }
            },
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
