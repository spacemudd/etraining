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
                        <h1 class="mb-8 font-bold text-2xl">{{ $t('words.invoice') }}</h1>

                        <div class="mb-6 flex justify-end items-center space-x-2">
                            <jet-button
                                :href="route('back.companies.create')"
                                type="button"
                                disabled
                            >
                                <span>{{ $t('words.print') }}</span>
                            </jet-button>
                            <jet-button
                                :href="route('back.companies.create')"
                                type="button"
                                disabled
                            >
                                <span>{{ $t('words.upload-payment-receipt') }}</span>
                            </jet-button>
                            <jet-button
                                :href="route('back.companies.create')"
                                type="button"
                                disabled
                            >
                                <span>{{ $t('words.mark-as-paid') }}</span>
                            </jet-button>
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row justify-start bg-gray-100 rounded">
                        <div class="w-full p-4">
                            <div class="grid grid-cols-2">
                                <div class="font-bold">{{ $t('words.date') }}</div>
                                <div>{{ invoice.created_at_date }}</div>
                                <div class="font-bold">{{ $t('words.no') }}.</div>
                                <div>{{ invoice.number_formatted }}</div>
                                <div class="font-bold">{{ $t('words.paid') }}</div>
                                <div>{{ invoice.is_paid ? $t('words.yes') : $t('words.no') }}</div>
                            </div>
                        </div>
                        <div class="w-full p-4">
                            <div class="grid grid-cols-2">
                                <div class="font-bold">{{ $t('words.client-name') }}</div>
                                <div>{{ invoice.company ? invoice.company.name_ar : 'Unknown' }}</div>
                                <div class="font-bold">{{ $t('words.total-amount') }}.</div>
                                <div>{{ invoice.total_amount }}</div>
                                <div class="font-bold">{{ $t('words.verified') }}</div>
                                <div>{{ invoice.is_paid ? $t('words.yes') : $t('words.no') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="w-full md:w-4/12">
                    <h1 class="mb-8 font-bold text-2xl">{{ $t('words.comments') }}</h1>
                </div>
            </div>

            <div class="mt-8 flex flex-col md:flex-row md:space-x-5">
                <div class="w-full md:w-8/12">
                    <h1 class="mb-8 font-bold text-2xl">{{ $t('words.items') }}</h1>

                    <div>
                        <table class="w-full whitespace-no-wrap bg-white rounded-lg my-5 p-5 shadow text-sm">
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
                                    {{ item.name }}
                                </td>

                                <td class="px-4 py-4">
                                    {{ item.sub_total }}
                                </td>

                                <td class="px-4 py-4">
                                    {{ item.tax }}
                                </td>

                                <td class="px-4 py-4">
                                    {{ item.total }}
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
                <div class="w-full md:w-4/12">
                    <h1 class="mb-8 font-bold text-2xl">{{ $t('words.documents') }}</h1>
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
import VueDropzone from 'vue2-dropzone'
import 'vue2-dropzone/dist/vue2Dropzone.min.css'
import SelectTraineeGroup from "@/Components/SelectTraineeGroup";
import ChangeTraineePassword from '@/Components/ChangeTraineePassword';
import AttendanceSheetManagementForTrainee from "@/Components/AttendanceSheetManagementForTrainee";
import 'selectize/dist/js/standalone/selectize.min';

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
    },
    mounted() {

    },
    methods: {}
}
</script>

<style>

</style>
