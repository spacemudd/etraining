<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'finance', link: route('back.finance')},
                    {title: 'invoices', link: route('back.finance.invoices.index')},
                ]"
            ></breadcrumb-container>
            <div class="flex flex-col md:flex-row md:space-x-5 place-content-center">
                <div class="place-items-start ml-20" style="margin-left: 100px">
                    <inertia-link class="font-bold text-center mx-3 mt-5 inline-flex items-center px-4 py-2 bg-gray-300 hover:bg-red-700 active:bg-red-900 border border-transparent rounded-md font-semibold text-xs text-black uppercase ltr:tracking-widest focus:outline-none focus:border-gray-300 focus:shadow-outline-gray transition ease-in-out duration-150 disabled:cursor-not-allowed mx-"
                                  :href="route('back.finance.invoices.short')">
                        {{ $t('words.simple-table') }}
                    </inertia-link>
                    <inertia-link class="font-bold text-center mx-3 mt-5 inline-flex items-center px-4 py-2 bg-green-400 hover:bg-red-700 active:bg-red-900 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest focus:outline-none focus:border-green-400 focus:shadow-outline-green transition ease-in-out duration-150 disabled:cursor-not-allowed mx-"
                                  :href="route('back.finance.invoices.index')">
                        {{ $t('words.complete-table') }}
                    </inertia-link>
                </div>
            </div>
            <div class="flex" v-if="canSelectAll">
                <button class="btn btn-gray mt-5 disabled:bg-gray-100 disabled:cursor-not-allowed" :disabled="$wait.is('APPROVING_INVOICES')" @click="approveInvoices">({{ selected_invoices.length }}) {{ $t('words.financial-department-approval') }}</button>
                <button class="mt-2 btn border-2 mt-5 mx-5 px-5"
                        @click="toggleAll">
                    {{ $t('words.select-all') }}
                </button>
            </div>


            <div class="">
                <Table
                    class="mt-5 w-full whitespace-no-wrap"
                    :filters="queryBuilderProps.filters"
                    :search="queryBuilderProps.search"
                    :columns="queryBuilderProps.columns"
                    :on-update="setQueryBuilder"
                    :meta="invoices"
                >
                    <template #head>
                        <tr :style="{background: ['#f7f7f7'], position: ['sticky'], top: [0]}">
                            <th :style="{background: ['#f7f7f7'], position: ['sticky'], right: [0]}">{{ $t('words.account-name') }}</th>
                            <th :style="{background: ['#f7f7f7'], position: ['sticky'], right: ['90px']}" @click.prevent="sortBy('number')">{{ $t('words.invoice') }}</th>
                            <th @click.prevent="sortBy('from_date')">{{ $t('words.date-period') }}</th>
                            <th>{{ $t('words.company') }}</th>
                            <th @click.prevent="sortBy('status')">{{ $t('words.status') }}</th>
                            <th>{{ $t('words.payment-method') }}</th>
                            <th>{{ $t('words.amount') }}</th>
                            <th>{{ $t('words.submitted-receipt') }}</th>
                            <th>{{ $t('words.bank-name') }}</th>
                            <th>{{ $t('words.collected') }}</th>
                            <th>{{ $t('words.confirmed') }}</th>
                            <th @click.prevent="sortBy('created_at')">{{ $t('words.date') }}</th>
                            <th>{{ $t('words.actions') }}</th>
                        </tr>
                    </template>
                    <!--                    :style="{position: ['sticky'], right: [0], background: ['#efefef'], border: []}"-->
                    <!--                    class="sticky top-0 bg-white"-->
                    <template #body>
                        <tr v-for="invoice in invoices.data" :key="invoice.id">
<!--                            <td v-if="users.trainee.deleted_at" :style="{background: ['#f7f7f7'], position: ['sticky'], right: [0], top: ['35px']}">-->
<!--                                <inertia-link :href="route('back.trainees.show', invoice.trainee_id)">-->
<!--                                    {{ invoice.trainee.name }}-->
<!--                                </inertia-link>-->
<!--                            </td>-->
                            <td :style="{background: ['#f7f7f7'], position: ['sticky'], right: [0], top: ['35px']}">
                                <inertia-link class="hover:text-blue-600" :href="invoice.trainee.show_url">{{ invoice.trainee ? invoice.trainee.name : '-' }}</inertia-link>
                            </td>
                            <td :style="{background: ['#f7f7f7'], position: ['sticky'], right: ['90px'], top: ['35px']}">
                                <input type="checkbox"
                                       v-if="canSelectAll"
                                       :checked="selected_invoices.includes(invoice.id)"
                                       @click="toggleSelectedInvoice(invoice)">
                                <inertia-link :href="route('back.finance.invoices.show', invoice.id)">
                                    {{ invoice.number_formatted }}
                                </inertia-link>
                            </td>
                            <td class="rtl:text-right text-black">
                                <inertia-link :href="route('back.finance.invoices.show', invoice.id)">
                                    {{  invoice.from_date | formatDate  }}
                                </inertia-link>
                            </td>
                            <td class="rtl:text-right text-black">
                                <inertia-link :href="route('back.companies.show', invoice.company_id)">
                                    {{ invoice.company.name_ar }}
                                </inertia-link>
                            </td>
                            <td class="rtl:text-right text-black">
                                <inertia-link :href="route('back.finance.invoices.show', invoice.id)">
                                    <div v-if="invoice.status === 0">
                                        <span class="text-white bg-red-500 rounded-lg px-3 py-1 font-bold border-solid border-2 border-red-500">
                                            {{ invoice.status_formatted }}
                                        </span>
                                    </div>
                                    <div v-if="invoice.status === 1">
                                        <span class="text-white bg-green-400 rounded-lg px-3 py-1 font-bold border-solid border-2 border-green-400">
                                            {{ invoice.status_formatted }}
                                        </span>
                                    </div>
                                    <div v-if="invoice.status === 2">
                                        <span class="text-white bg-yellow-400 rounded-lg px-3 py-1 font-bold border-solid border-2 border-yellow-400">
                                            {{ invoice.status_formatted }}
                                        </span>
                                    </div>
                                    <div v-if="invoice.status === 3">
                                        <span class="text-white bg-orange-500 rounded-lg px-3 py-1 font-bold border-solid border-2 border-orange-500">
                                            {{ invoice.status_formatted }}
                                        </span>
                                    </div>
                                    <div v-if="invoice.status === 4">
                                        <span class="text-white bg-yellow-400 rounded-lg px-4 py-1 font-bold border-solid border-2 border-yellow-400">
                                            {{ invoice.status_formatted }}
                                        </span>
                                    </div>
                                </inertia-link>
                            </td>
                            <td class="rtl:text-right text-black">
                                <inertia-link :href="route('back.finance.invoices.show', invoice.id)">
                                    <span v-if="invoice.payment_method === 1"
                                          class="img {display:block} inline-block inline-flex">
                                    {{ invoice.payment_method_formatted }}
                                        <svg v-if="invoice.payment_method === 1" width="60" height="26">
                                            <image xlink:href="https://www.svgrepo.com/show/210224/credit-card.svg" src="https://www.svgrepo.com/show/210224/credit-card.svg" width="60" height="26"/>
                                        </svg>
                                    </span>
                                    <span v-else>
                                        {{ invoice.payment_method_formatted }}
                                    </span>
                                </inertia-link>
                            </td>
                            <td class="rtl:text-right text-black">
                                <inertia-link :href="route('back.finance.invoices.show', invoice.id)">
                                    {{ invoice.grand_total }}
                                </inertia-link>
                            </td>
                            <td class="rtl:text-right text-black">
                                <inertia-link v-if="invoice.trainee_bank_payment_receipt"
                                              :href="route('back.finance.invoices.show', invoice.id)">
                                    {{ invoice.trainee_bank_payment_receipt.sender_name }}
                                    <br/>
                                    {{ invoice.trainee_bank_payment_receipt.created_at }}
                                </inertia-link>
                            </td>
                            <td class="rtl:text-right text-black">
                                <inertia-link v-if="invoice.trainee_bank_payment_receipt"
                                              :href="route('back.finance.invoices.show', invoice.id)">
                                    {{ $t('words.from') }} {{ invoice.trainee_bank_payment_receipt.bank_from }}
                                    <br/>
                                    {{ $t('words.to') }} {{ invoice.trainee_bank_payment_receipt.bank_to }}
                                </inertia-link>
                            </td>
                            <td class="rtl:text-right text-black">
                                <inertia-link :href="route('back.finance.invoices.show', invoice.id)">
                                    <span v-if="invoice.chase_boolean"
                                          class="bg-green-500 text-white rounded px-2">
                                        {{ $t('words.yes') }}
                                    </span>
                                    <span v-else
                                          class="bg-red-600 text-white rounded px-2">
                                        {{ $t('words.no') }}
                                    </span>
                                </inertia-link>
                            </td>
                            <td class="rtl:text-right text-black">
                                <inertia-link :href="route('back.finance.invoices.show', invoice.id)">
                                    <span v-if="invoice.is_verified"
                                          class="bg-green-500 text-white rounded px-2">
                                        {{ $t('words.yes') }}
                                    </span>
                                    <span v-else
                                          class="bg-red-600 text-white rounded px-2">
                                        {{ $t('words.no') }}
                                    </span>
                                </inertia-link>
                            </td>
                            <td class="rtl:text-right text-black">
                                <inertia-link :href="route('back.finance.invoices.show', invoice.id)">
                                    {{ invoice.created_at_date }}
                                </inertia-link>
                            </td>
                            <td class="rtl:text-right text-black">
                                <button @click="deleteInvoice(invoice.id)"
                                        v-can="'can-delete-invoice-anytime'"
                                        type="button"
                                        v-if="invoice.status <= 4 && invoice.payment_method != 1"
                                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest focus:outline-none focus:shadow-outline-gray transition ease-in-out duration-150 disabled:cursor-not-allowed mx-2 bg-red-500 hover:bg-red-600 active:bg-red-700 foucs:bg-red-700">
                                    {{ $t('words.delete') }}
                                </button>
                            </td>
                        </tr>
                    </template>
                </Table>


                <!--
                <div v-if="invoice.status === 0">
                                        <span class="border-4 bg-red-600 px-2.5 mx-2 border-red-600 rounded-full"></span>
                                        <span class="text-red-600 font-bold">
                                            {{ invoice.status_formatted }}
                                        </span>
                                    </div>
                                    <div v-if="invoice.status === 1">
                                        <span class="border-4 bg-green-500 px-2.5 mx-2 border-green-500 rounded-full"></span>
                                        <span class="text-green-500 font-bold">
                                            {{ invoice.status_formatted }}
                                        </span>
                                    </div>
                                    <div v-if="invoice.status === 2">
                                        <span class="border-4 bg-yellow-400 px-2.5 mx-2 border-yellow-400 rounded-full"></span>
                                        <span class="text-yellow-400 font-bold">
                                            {{ invoice.status_formatted }}
                                        </span>
                                    </div>
                                    <div v-if="invoice.status === 3">
                                        <span class="border-4 bg-orange-400 px-2.5 mx-2 border-orange-400 rounded-full"></span>
                                        <span class="text-orange-400 font-bold">
                                            {{ invoice.status_formatted }}
                                        </span>
                                    </div>
                                    <div v-if="invoice.status === 4">
                                        <span class="border-4 bg-yellow-400 px-2.5 mx-2 border-yellow-400 rounded-full"></span>
                                        <span class="text-yellow-400 font-bold">
                                            {{ invoice.status_formatted }}
                                        </span>
                                    </div>



                <table class="w-full whitespace-no-wrap mt-10">
                    <tr class="text-left font-bold">
                        <th class="text-sm px-6 pt-6 pb-4">{{ $t('words.invoice') }}</th>
                        <th class="text-sm px-6 pt-6 pb-4">{{ $t('words.company') }}</th>
                        <th class="text-sm px-6 pt-6 pb-4">{{ $t('words.account-name') }}</th>
                        <th class="text-sm px-6 pt-6 pb-4">{{ $t('words.status') }}</th>
                        <th class="text-sm px-6 pt-6 pb-4">{{ $t('words.payment-method') }}</th>
                        <th class="text-sm px-6 pt-6 pb-4">{{ $t('words.amount') }}</th>
                        <th class="text-sm px-6 pt-6 pb-4">{{ $t('words.confirmed') }}</th>
                        <th class="text-sm px-6 pt-6 pb-4">{{ $t('words.date') }}</th>
                    </tr>
                    <tr v-for="invoice in invoices.data" :key="invoice.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('back.finance.invoices.show', invoice.id)">
                                {{ invoice.number_formatted }}
                            </inertia-link>
                        </td>
                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('back.finance.invoices.show', invoice.id)">
                                {{ invoice.trainee.company.name_ar }}
                            </inertia-link>
                        </td>
                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('back.finance.invoices.show', invoice.id)">
                                {{ invoice.trainee.name }}
                            </inertia-link>
                        </td>
                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('back.finance.invoices.show', invoice.id)">
                                {{ invoice.status_formatted }}
                            </inertia-link>
                        </td>
                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('back.finance.invoices.show', invoice.id)">
                                {{ invoice.payment_method_formatted }}
                            </inertia-link>
                        </td>
                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('back.finance.invoices.show', invoice.id)">
                                {{ invoice.grand_total }}
                            </inertia-link>
                        </td>
                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('back.finance.invoices.show', invoice.id)">
                                <span v-if="invoice.is_verified"
                                      class="bg-green-500 text-white rounded px-2">
                                        {{ $t('words.yes') }}
                                    </span>
                                <span v-else
                                      class="bg-red-600 text-white rounded px-2">
                                        {{ $t('words.no') }}
                                    </span>
                            </inertia-link>
                        </td>
                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('back.finance.invoices.show', invoice.id)">
                                {{ invoice.created_at_date }}
                            </inertia-link>
                        </td>
                        <td class="border-t w-px">
                            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('back.finance.invoices.show', invoice.id)">
                                <ion-icon name="arrow-forward-outline" class="block w-6 h-6 fill-gray-400"></ion-icon>
                            </inertia-link>
                        </td>
                    </tr>
                    <tr v-if="invoices.data.length === 0">
                        <td class="border-t px-6 py-4" colspan="7">
                            <empty-slate/>
                        </td>
                    </tr>
                </table>
                -->

            </div>

            <!--<pagination :links="invoices.links" />-->
        </div>
    </app-layout>
</template>

<script>

import { InteractsWithQueryBuilder } from '@protonemedia/inertiajs-tables-laravel-query-builder';
import { Components } from "@protonemedia/inertiajs-tables-laravel-query-builder";
import Table from '@/Components/Tailwind2/Table';

// import Icon from '@/Shared/Icon'
// import Layout from '@/Shared/Layout'
import mapValues from 'lodash/mapValues'
import Pagination from '@/Shared/Pagination'
import pickBy from 'lodash/pickBy'
// import SearchFilter from '@/Shared/SearchFilter'
import throttle from 'lodash/throttle'
import AppLayout from '@/Layouts/AppLayout'
import IconNavigate from 'vue-ionicons/dist/ios-arrow-dropright'
import EmptySlate from "@/Components/EmptySlate";
import BreadcrumbContainer from "@/Components/BreadcrumbContainer";
import {Inertia} from "@inertiajs/inertia";

export default {
    mixins: [InteractsWithQueryBuilder],
    metaInfo: { title: 'Financial invoices' },
    // layout: Layout,
    components: {
        BreadcrumbContainer,
        IconNavigate,
        AppLayout,
        // Icon,
        Pagination,
        // SearchFilter,
        EmptySlate,
        Table,
    },
    props: {
        users: Object,
        invoices: Object,
        filters: Object,
    },
    data() {
        return {
            selected_invoices: [],
            form: {
                // search: this.filters.search,
                // trashed: this.filters.trashed,
            },
        }
    },
    computed: {
        canSelectAll() {
            if (this.queryBuilderProps.filters) {
                if (this.queryBuilderProps.filters.status) {
                    return this.queryBuilderProps.filters.status.value === '4';
                } else {
                    return false;
                }
            } else {
                return false;
            }
        },
        hasSearchRows() {
            return Object.keys(this.invoices.search || {}).length > 0;
        },
    },
    watch: {
        form: {
            handler: throttle(function() {
                let query = pickBy(this.form)
                this.$inertia.replace(this.route('back.finance.invoices.index', Object.keys(query).length ? query : { remember: 'forget' }))
            }, 150),
            deep: true,
        },
    },
    mounted() {
        let vm = this;
        Components.Pagination.setTranslations({
            no_results_found: vm.$t('words.no-records-have-been-found'),
            previous: vm.$t('pagination.previous'),
            next: vm.$t('pagination.next'),
            to: vm.$t('pagination.to'),
            of: vm.$t('pagination.of'),
            results: vm.$t('pagination.results'),
        });
    },
    methods: {
        deleteInvoice(invoiceId) {
            if (confirm(this.$t('words.are-you-sure'))) {
                this.$inertia.delete(route('back.finance.invoices.destroy', {invoice: invoiceId}));
            }
        },
        approveInvoices() {
            if (confirm(this.$t('words.are-you-sure'))) {
                if (this.selected_invoices.length) {
                    this.$wait.start('APPROVING_INVOICES');
                    axios.post(route('back.finance.invoices.bulk-approve-finance-department'), {
                        invoices: this.selected_invoices,
                    }).then(response => {
                        this.$wait.end('APPROVING_INVOICES');
                        this.selected_invoices = [];
                        Inertia.reload();
                    })
                }
            }
        },
        toggleAll() {
            if (this.selected_invoices.length === this.invoices.data.length) {
                this.selected_invoices = [];
            } else {
                this.selected_invoices = [];
                this.invoices.data.forEach((invoice) => {
                    this.selected_invoices.push(invoice.id);
                })
            }
        },
        toggleSelectedInvoice(invoice) {
            if (this.selected_invoices.includes(invoice.id)) {
                let index = this.selected_invoices.indexOf(invoice.id);
                if (index !== -1) {
                    this.selected_invoices.splice(index, 1);
                }
            } else {
                this.selected_invoices.push(invoice.id);
            }
        },
        reset() {
            this.form = mapValues(this.form, () => null)
        },
    },
}


</script>
