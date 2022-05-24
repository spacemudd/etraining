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

            <div class="flex" v-if="canSelectAll">
                <button class="btn btn-gray mt-5 disabled:bg-gray-100 disabled:cursor-not-allowed" :disabled="$wait.is('APPROVING_INVOICES')" @click="approveInvoices">({{ selected_invoices.length }}) {{ $t('words.financial-department-approval') }}</button>
                <button class="mt-2 btn border-2 mt-5 mx-5 px-5"
                        @click="toggleAll">
                    {{ $t('words.select-all') }}
                </button>
            </div>


            <div class="overflow-x-auto">
                <Table
                    class="mt-5 w-full whitespace-no-wrap"
                    :filters="queryBuilderProps.filters"
                    :search="queryBuilderProps.search"
                    :columns="queryBuilderProps.columns"
                    :on-update="setQueryBuilder"
                    :meta="invoices"
                >
                    <template #head>
                        <tr>
                            <th class="rtl:text-right font-weight-bold" @click.prevent="sortBy('number')">{{ $t('words.invoice') }}</th>
                            <th class="rtl:text-right font-weight-bold">{{ $t('words.company') }}</th>
                            <th class="rtl:text-right font-weight-bold">{{ $t('words.account-name') }}</th>
                            <th class="rtl:text-right font-weight-bold" @click.prevent="sortBy('status')">{{ $t('words.status') }}</th>
                            <th class="rtl:text-right font-weight-bold" @click.prevent="sortBy('payment_method')">{{ $t('words.payment-method') }}</th>
                            <th class="rtl:text-right font-weight-bold" @click.prevent="sortBy('grand_total')">{{ $t('words.amount') }}</th>
                            <th class="rtl:text-right font-weight-bold">{{ $t('words.collected') }}</th>
                            <th class="rtl:text-right font-weight-bold">{{ $t('words.confirmed') }}</th>
                            <th class="rtl:text-right font-weight-bold" @click.prevent="sortBy('created_at')">{{ $t('words.date') }}</th>
                        </tr>
                    </template>

                    <template #body>
                        <tr v-for="invoice in invoices.data" :key="invoice.id">
                            <td class="rtl:text-right text-black">
                                <input type="checkbox"
                                       v-if="canSelectAll"
                                       :checked="selected_invoices.includes(invoice.id)"
                                       @click="toggleSelectedInvoice(invoice)">
                                <inertia-link :href="route('back.finance.invoices.show', invoice.id)">
                                    {{ invoice.number_formatted }}
                                </inertia-link>
                            </td>
                            <td class="rtl:text-right text-black">
                                <inertia-link :href="route('back.companies.show', invoice.company_id)">
                                    {{ invoice.company.name_ar }}
                                </inertia-link>
                            </td>
                            <td class="rtl:text-right text-black">
                                <inertia-link :href="route('back.trainees.show', invoice.trainee_id)">
                                    {{ invoice.trainee.name }}
                                </inertia-link>
                            </td>
                            <td class="rtl:text-right text-black">
                                <inertia-link :href="route('back.finance.invoices.show', invoice.id)">
                                    {{ invoice.status_formatted }}
                                </inertia-link>
                            </td>
                            <td class="rtl:text-right text-black">
                                <inertia-link :href="route('back.finance.invoices.show', invoice.id)">
                                    {{ invoice.payment_method_formatted }}
                                </inertia-link>
                            </td>
                            <td class="rtl:text-right text-black">
                                <inertia-link :href="route('back.finance.invoices.show', invoice.id)">
                                    {{ invoice.grand_total }}
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
                        </tr>
                    </template>
                </Table>


                <!--
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
