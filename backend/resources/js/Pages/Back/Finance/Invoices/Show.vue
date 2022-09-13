<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'invoices', link: route('back.finance.invoices.index')},
                    {title_raw: invoice.number_formatted},
                ]"
            ></breadcrumb-container>

            <div class="flex flex-col md:flex-row md:space-x-5">
                <div class="w-full md:w-8/12">
                    <div class="flex justify-between">
                        <h1 class="mb-8 font-bold text-2xl img {display:block} inline-flex">{{ $t('words.invoice') }}
                            <svg v-if="invoice.status === 1" width="30" height="30" class="mx-2 mt-1">
                                <image class=inline xlink:href="https://www.svgrepo.com/show/402906/white-heavy-check-mark.svg" src="https://www.svgrepo.com/show/402906/white-heavy-check-mark.svg" width="30" height="30"/>
                            </svg>
                        </h1>
                        <div class="mb-6 flex justify-end items-center">
                            <div>
                                <div v-if="invoice.status === 0"
                                     class="text-red-600 rounded-lg mx-8 px-4 py-1 font-bold border-solid border-2 border-red-600">
                                    {{ $t('words.unpaid') }}
                                </div>
                                <div v-if="invoice.status === 1"
                                     class="text-green-500 rounded-lg mx-8 px-4 py-1 font-bold border-solid border-2 border-green-500">
                                    {{ $t('words.paid') }}
                                </div>
                                <div v-if="invoice.status === 2"
                                     class="text-yellow-400 rounded-lg mx-8 px-4 py-1 font-bold border-solid border-2 border-yellow-400">
                                    {{ $t('words.audit-required') }}
                                </div>
                                <div v-if="invoice.status === 3"
                                     class="text-orange-500 rounded-lg mx-8 px-4 py-1 font-bold border-solid border-2 border-orange-500">
                                    {{ $t('words.reject-payment-receipt') }}
                                </div>
                                <div v-if="invoice.status === 4"
                                     class="text-yellow-400 rounded-lg mx-8 px-4 py-1 font-bold border-solid border-2 border-yellow-400">
                                    {{ $t('words.finance-audit-required') }}
                                </div>
                            </div>
                            <a class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150 disabled:cursor-not-allowed mx-"
                               target="_blank"
                               :href="route('back.finance.invoices.pdf', invoice.id)">
                                {{ $t('words.print') }}
                            </a>

                            <inertia-link v-if="invoice.can_upload_receipt"
                                          :href="route('back.finance.invoices.upload-receipt-form', invoice.id)"
                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest focus:outline-none focus:shadow-outline-gray transition ease-in-out duration-150 disabled:cursor-not-allowed mx-2 bg-red-500 hover:bg-red-600 active:bg-red-700 foucs:bg-red-700">
                                {{ $t('words.upload-receipt') }}
                            </inertia-link>
                            <button @click="deleteInvoice(invoice)"
                                    v-can="'can-delete-invoice-anytime'"
                                    type="button"
                                    v-if="invoice.status <= 4 && invoice.payment_method != 1"
                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest focus:outline-none focus:shadow-outline-gray transition ease-in-out duration-150 disabled:cursor-not-allowed mx-2 bg-red-500 hover:bg-red-600 active:bg-red-700 foucs:bg-red-700">
                                {{ $t('words.delete') }}
                            </button>
                            <button @click="markAsUnpaid"
                                    v-can="'approve-payment-receipt'"
                                    type="button"
                                          v-if="invoice.status === 3 || invoice.status === 2"
                                          class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest focus:outline-none focus:shadow-outline-gray transition ease-in-out duration-150 disabled:cursor-not-allowed mx-2 bg-red-500 hover:bg-red-600 active:bg-red-700 foucs:bg-red-700">
                                [{{ $t('words.chase') }}] {{ $t('words.mark-as-unpaid') }}
                            </button>
                            <button @click="markAsPaid"
                                    v-can="'approve-payment-receipt'"
                                    type="button"
                                    v-if="invoice.status === 3 || invoice.status === 2"
                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest focus:outline-none focus:shadow-outline-gray transition ease-in-out duration-150 disabled:cursor-not-allowed mx-2 bg-green-500 hover:bg-green-600 active:bg-green-700 foucs:bg-green-700">
                                [{{ $t('words.chase') }}] {{ $t('words.mark-as-paid') }}
                            </button>

                            <template v-if="invoice.status === 4">
                                <button @click="rejectPaymentReceipt"
                                        v-can="'approve-invoice-paid'"
                                        v-if="invoice.status === 4 || invoice.status === 3"
                                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest focus:outline-none focus:shadow-outline-gray transition ease-in-out duration-150 disabled:cursor-not-allowed mx-2 bg-red-500 hover:bg-red-600 active:bg-red-700 foucs:bg-red-700"
                                        type="button">
                                    [{{ $t('words.finance') }}] {{ $t('words.reject-payment-receipt') }}
                                </button>

                                <inertia-link :href="route('back.finance.invoices.approve-payment-receipt', invoice.id)"
                                              v-can="'approve-invoice-paid'"
                                            v-if="invoice.status === 3 || invoice.status === 4"
                                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest focus:outline-none focus:shadow-outline-gray transition ease-in-out duration-150 disabled:cursor-not-allowed mx-2 bg-green-500 hover:bg-green-600 active:bg-green-700 foucs:bg-green-700">
                                    [{{ $t('words.finance') }}] {{ $t('words.approve-payment-receipt') }}
                                </inertia-link>
                            </template>
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row justify-start rounded bg-gray-50 rounded shadow-lg p-5">
                        <div class="w-full p-4">
                            <div class="grid grid-cols-2">
                                <div class="font-bold">{{ $t('words.date') }}</div>
                                <div>{{ invoice.created_at | formatDate }}</div>
                                <div class="font-bold">{{ $t('words.invoice-no') }}</div>
                                <div>{{ invoice.number_formatted }}</div>
                                <div class="font-bold">{{ $t('words.from-date') }}</div>
                                <div>{{ invoice.from_date | formatDate }}</div>
                                <div class="font-bold">{{ $t('words.to-date') }}</div>
                                <div>{{ invoice.to_date | formatDate }}</div>
                                <hr class="my-2">
                                <hr class="my-2">
                                <div v-if="invoice.payment_method === 1" class="font-bold">{{ $t('words.payment-reference-id') }}</div>
                                <div v-if="invoice.payment_method === 1" class="truncate" >{{ invoice.payment_reference_id }}</div>
                                <div v-if="invoice.payment_method === 1" class="font-bold">{{ $t('words.paid-at') }}</div>
                                <div v-if="invoice.payment_method === 1" class="truncate">{{ invoice.paid_at | formatDate }}</div>
                            </div>
                        </div>
                        <div class="w-full p-4">
                            <div class="grid grid-cols-2">
                                <div class="font-bold">{{ $t('words.client-name') }}</div>
                                <div>
                                    <inertia-link class="text-blue-500 hover:text-blue-600" :href="invoice.trainee.show_url">{{ invoice.trainee ? invoice.trainee.name : '-' }}</inertia-link>
                                    -
                                    <inertia-link class="text-blue-500 hover:text-blue-600" :href="invoice.company.show_url">({{ invoice.company ? invoice.company.name_ar : 'Unknown' }})</inertia-link>
                                </div>
                                <div class="font-bold">{{ $t('words.total-amount') }}</div>
                                <div>{{ invoice.grand_total }}</div>
                                <div class="font-bold">{{ $t('words.paid') }}</div>
                                <div>{{ invoice.is_paid ? $t('words.yes') : $t('words.no') }} <span v-if="invoice.is_paid">({{ invoice.payment_method_formatted }})
                                    <span class="img {display:block} inline-flex">
                                        <svg v-if="invoice.payment_method === 1" width="40" height="40" class="mx-0.5">
                                            <image class=inline xlink:href="https://www.svgrepo.com/show/328112/visa.svg" src="https://www.svgrepo.com/show/328112/visa.svg" width="40" height="40"/>
                                        </svg>
                                        <svg v-if="invoice.payment_method === 1" width="20" height="40" class="mx-0.5">
                                            <image class=inline xlink:href="https://www.svgrepo.com/show/163750/mastercard.svg" src="https://www.svgrepo.com/show/163750/mastercard.svg" width="20" height="40"/>
                                        </svg>
                                        <svg v-if="invoice.payment_method === 1" width="40" height="40" class="mx-0.5">
                                            <image class=inline xlink:href="https://upload.wikimedia.org/wikipedia/commons/f/fb/Mada_Logo.svg" src="https://upload.wikimedia.org/wikipedia/commons/f/fb/Mada_Logo.svg" width="40" height="40"/>
                                        </svg>
                                        <svg v-if="invoice.payment_method === 1" width="40" height="40" class="mx-0.5">
                                            <image class=inline xlink:href="https://www.svgrepo.com/show/303191/apple-pay-logo.svg" src="https://www.svgrepo.com/show/303191/apple-pay-logo.svg" width="40" height="40"/>
                                        </svg>
                                    </span>
                                </span>
                                </div>
                                <div v-if="invoice.payment_method === 0" class="font-bold">{{ $t('words.sender-bank-name') }}</div>
                                <div v-if="invoice.payment_method === 0">{{ invoice.trainee_bank_payment_receipt.bank_from }}</div>
                                <div v-if="invoice.payment_method === 0" class="font-bold">{{ $t('words.receiver-bank-name') }}<br/></div>
                                <div v-if="invoice.payment_method === 0">{{ invoice.trainee_bank_payment_receipt.bank_to }}</div>
                                <hr class="my-2">
                                <hr class="my-2">
                                <div class="font-bold">{{ $t('words.chase') }}</div>
                                <div>{{ invoice.chase_status }}</div>
                                <div class="font-bold">{{ $t('words.chased-by') }}</div>
                                <div><span v-if="invoice.chased_by">{{ invoice.chased_by.name }}</span></div>
                                <hr class="my-2">
                                <hr class="my-2">
                                <div class="font-bold">{{ $t('words.verified') }}</div>
                                <div>
                                    <span v-if="invoice.is_verified"
                                          class="bg-green-500 text-white rounded px-2">
                                        {{ $t('words.yes') }}
                                    </span>
                                    <span v-else
                                          class="bg-red-600 text-white rounded px-2">
                                        {{ $t('words.no') }}
                                    </span>
                                </div>
                                <div v-if="invoice.verified_by" class="font-bold">{{ $t('words.approved-by') }}</div>
                                <div v-if="invoice.verified_by">
                                    <span>{{ invoice.verified_by.name }}</span>
                                </div>
                                <div v-if="invoice.rejection_reason_payment_receipt" class="bg-red-200 border-2 border-red-500 text-black p-3 mt-2 border-l-0">
                                    <div class="font-bold">{{ $t('words.reject-payment-receipt-reason') }}</div>
                                </div>
                                <div v-if="invoice.rejection_reason_payment_receipt" class="bg-red-200 border-2 border-red-500 text-black py-3 mt-2 border-r-0">
                                    <div>
                                        <span>{{ invoice.rejection_reason_payment_receipt }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="w-full md:w-4/12">
                    <div class="mx-5">
                        <h1 class="mb-8 font-bold text-2xl">{{ $t('words.documents') }}</h1>
                        <div class="white-bg rounded bg-gray-50 rounded shadow-lg p-5">
                            <ul class="ml-auto flex">
                                <template v-if="invoice.trainee_bank_payment_receipt">
                                    <li v-for="file in invoice.trainee_bank_payment_receipt.approvals" class="inline-block">
                                        <a :href="file.download_url" target="_blank" class="font-bold hover:text-blue-600" alt="invoice.">
                                            {{ $t('words.receipt-approval') }}:
                                            <svg v-if="file.mime_type.includes('pdf')" width="90" height="90" class="mx-0.5 mt-2">
                                                <image class=inline xlink:href="https://cdn-icons-png.flaticon.com/512/4726/4726010.png" src="https://cdn-icons-png.flaticon.com/512/4726/4726010.png" width="90" height="90"/>
                                            </svg>
                                            <svg v-else width="90" height="90" class="mx-0.5 mt-2">
                                                <image xlink:href="https://cdn-icons-png.flaticon.com/512/4725/4725998.png" src="https://cdn-icons-png.flaticon.com/512/4725/4725998.png"  width="90" height="90"/>
                                            </svg>
                                            <span class="text-sm text-gray-800 inline-flex mx-4 mt-4" dir="ltr">{{ file.created_at_timezone }}</span>
                                        </a>
                                    </li>
                                </template>

                                <template v-if="invoice.trainee_bank_payment_receipt">
                                    <li v-for="file in invoice.trainee_bank_payment_receipt.attachments" class="inline-block">
                                        <a :href="file.download_url" target="_blank" class="font-bold hover:text-blue-600">
                                            {{ $t('words.receipt') }}:
                                            <svg v-if="file.mime_type.includes('pdf')" width="90" height="90" class="mx-0.5 mt-2">
                                                <image class=inline xlink:href="https://cdn-icons-png.flaticon.com/512/4726/4726010.png" src="https://cdn-icons-png.flaticon.com/512/4726/4726010.png" width="90" height="90"/>
                                            </svg>
                                            <svg v-else width="90" height="90" class="mx-0.5 mt-2">
                                                <image xlink:href="https://cdn-icons-png.flaticon.com/512/4725/4725998.png" src="https://cdn-icons-png.flaticon.com/512/4725/4725998.png"  width="90" height="90"/>
                                            </svg>
                                            <span class="text-sm text-gray-800 inline-flex mx-4 mt-4" dir="ltr">{{ file.created_at_timezone }}</span>
                                        </a>
                                    </li>
                                </template>
                                <template v-else>
                                    <empty-slate />
                                </template>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex flex-col md:flex-row md:space-x-5">
                <div class="w-full md:w-8/12">
                    <h1 class="mb-8 font-bold text-2xl">{{ $t('words.items') }}</h1>

                    <div>
                        <table class="w-full whitespace-no-wrap bg-white rounded-lg my-5 p-5 bg-gray-50 rounded shadow-lg text-sm">
                            <tr class="text-left font-bold">
                                <th class="p-4">{{ $t('words.name') }}</th>
                                <th class="p-4">{{ $t('words.subtotal') }}</th>
                                <th class="p-4">{{ $t('words.tax') }}</th>
                                <th class="p-4">{{ $t('words.total') }}</th>
                            </tr>
                            <tr
                                v-for="item in invoice.items"
                                :key="item.id"
                                class="border-t hover:bg-gray-100 focus-within:bg-gray-100"
                            >
                                <td class="px-4 py-4">
                                    {{ item.name_ar }}
                                </td>

                                <td class="px-4 py-4">
                                    {{ item.sub_total }}
                                </td>

                                <td class="px-4 py-4">
                                    {{ item.tax }}
                                </td>

                                <td class="px-4 py-4">
                                    {{ item.grand_total }}
                                </td>
                            </tr>

                            <tr v-if="invoice.items.length === 0">
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
                <!--<div class="w-full md:w-4/12">-->
                <!--    <div class="mx-5">-->
                <!--        <h1 class="mb-8 font-bold text-2xl">{{ $t('words.comments') }}</h1>-->
                <!--        <div class="white-bg rounded shadow p-5">-->

                <!--        </div>-->
                <!--    </div>-->
                <!--</div>-->
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
import VueDropzone from 'vue2-dropzone'
import 'vue2-dropzone/dist/vue2Dropzone.min.css'
import SelectTraineeGroup from "@/Components/SelectTraineeGroup";
import ChangeTraineePassword from '@/Components/ChangeTraineePassword';
import AttendanceSheetManagementForTrainee from "@/Components/AttendanceSheetManagementForTrainee";
import 'selectize/dist/js/standalone/selectize.min';
import EmptySlate from "@/Components/EmptySlate";

export default {
    props: [
        'invoice',
    ],
    components: {
        AppLayout,
        AttendanceSheetManagementForTrainee,
        Breadcrumb,
        BreadcrumbContainer,
        ChangeTraineePassword,
        CompanyContractsPagination,
        JetActionMessage,
        JetButton,
        JetDialogModal,
        JetFormSection,
        JetInput,
        JetInputError,
        JetLabel,
        JetSectionBorder,
        SelectTraineeGroup,
        VueDropzone,
        EmptySlate,
    },
    mounted() {

    },
    methods: {
        deleteInvoice() {
            if (confirm(this.$t('words.are-you-sure'))) {
                this.$inertia.delete(route('back.finance.invoices.destroy', this.invoice.id));
            }
        },
        rejectPaymentReceipt() {
            let reason = prompt(this.$t('words.rejection-reason'));
            if (reason === null) {
                return;
            }
            this.$inertia.post(route('back.finance.invoices.reject-payment-receipt', this.invoice.id), {
                reason: reason,
            });
        },
        markAsUnpaid() {
            let reason = prompt(this.$t('words.rejection-reason'));
            if (reason === null) {
                return;
            }
            this.$inertia.post(route('back.finance.invoices.mark-as-unpaid-from-chaser', this.invoice.id), {
                chased_note: reason,
            });
        },
        markAsPaid() {
            if (confirm(this.$t('words.are-you-sure'))) {
                this.$inertia.post(route('back.finance.invoices.mark-as-paid-from-chaser', this.invoice.id));
            }
        },
    }
}
</script>

<style>

</style>
