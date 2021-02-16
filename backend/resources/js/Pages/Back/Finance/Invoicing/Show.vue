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

            <div class="grid grid-cols-1 gap-2 lg:grid-cols-12 md:gap-4">
                <div class="col-span-4">
                    <div class="bg-gray-100 shadow-lg border rounded-lg p-5">
                        <table class="table w-full">
                            <colgroup>
                                <col style="width:50%;">
                            </colgroup>
                        	<tbody>
                            <tr>
                                <td>{{ $t('words.date') }}</td>
                                <td class="justify-content-end">{{ batch.invoices_date }}</td>
                            </tr>
                                <tr>
                                    <td>{{ $t('words.trainees') }}</td>
                                    <td>{{ batch.total }}</td>
                                </tr>
                                <tr>
                                    <td>{{ $t('words.total-value') }}</td>
                                    <td>{{ batch.sale_invoices_sum_grand_total }}</td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="py-2"><hr></td>
                                </tr>
                                <tr>
                                    <td>{{ $t('words.period-from') }}</td>
                                    <td class="justify-content-end">{{ batch.period_from }}</td>
                                </tr>
                                <tr>
                                    <td>{{ $t('words.period-to') }}</td>
                                    <td class="justify-content-end">{{ batch.period_to }}</td>
                                </tr>
                                <tr>
                                    <td class="pt-5"></td>
                                </tr>
                                <tr>
                                    <td class="text-gray-500">{{ $t('words.status') }}</td>
                                    <td class="text-gray-500">{{ batch.status_display }}</td>
                                </tr>
                                <tr>
                                    <td class="text-gray-500">{{ $t('words.created-by') }}</td>
                                    <td class="text-gray-500" v-if="batch.created_by">{{ batch.created_by.name }}</td>
                                </tr>
                        	</tbody>
                        </table>
                    </div>
                </div>
                <div class="col-span-8">
                    <div class="bg-gray-100 shadow-lg border rounded-lg p-5">
                        <h4>{{ $t('words.invoices') }} ({{ batch.sale_invoices_count }})</h4>
                        <hr class="my-5">

                        <table class="w-full whitespace-no-wrap text-sm">
                            <colgroup>
                                <col>
                                <col style="width:150px;">
                                <col style="width:100px;">
                                <col style="width:100px;">
                            </colgroup>
                            <tr class="text-left font-bold">
                                <th class="pt-6 pb-4">{{ $t('words.customer-name') }}</th>
                                <th class="pt-6 pb-4">{{ $t('words.invoice') }}</th>
                                <th class="pt-6 pb-4">{{ $t('words.date') }}</th>
                                <th class="pt-6 pb-4 rtl:text-left text-right">{{ $t('words.total-value') }}</th>
                            </tr>
                            <tr v-for="sale_invoice in batch.sale_invoices" :key="sale_invoice.id"
                                class="hover:bg-gray-100 focus-within:bg-gray-100">
                                <td class="border-t py-5">
                                    {{ sale_invoice.billable.name }}
                                    <br/>
                                    {{ sale_invoice.billable.email }}
                                    <br/>
                                    {{ sale_invoice.billable.phone }}
                                    <br/>
                                    <span class="text-gray-500">{{ sale_invoice.billable.company.name_ar }}</span>
                                </td>
                                <td class="border-t py-5">
                                    <span :class="{'text-gray-400': !sale_invoice.number}">
                                        {{ sale_invoice.number ? sale_invoice.number : '#'+$t('words.draft') }}
                                    </span>
                                </td>
                                <td class="border-t py-5">
                                    {{ sale_invoice.issued_at }}
                                </td>
                                <td class="border-t rtl:text-left text-right">
                                    {{ sale_invoice.grand_total_display }}
                                </td>
                                <!--<td class="border-t w-px">-->
                                <!--    <inertia-link class="px-4 flex items-center" :href="'#'">-->
                                <!--        <ion-icon name="arrow-forward-outline" class="block w-6 h-6 fill-gray-400"></ion-icon>-->
                                <!--    </inertia-link>-->
                                <!--</td>-->
                            </tr>
                        </table>
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
