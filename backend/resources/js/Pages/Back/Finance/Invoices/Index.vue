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
            <div class="bg-white rounded shadow overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <tr class="text-left font-bold">
                        <th class="text-sm px-6 pt-6 pb-4">{{ $t('words.invoice') }}</th>
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
                                {{ invoice.reference_number }}
                            </inertia-link>
                        </td>
                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('back.finance.invoices.show', invoice.id)">
                                {{ invoice.account_name }}
                            </inertia-link>
                        </td>
                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('back.finance.invoices.show', invoice.id)">
                                {{ invoice.status }}
                            </inertia-link>
                        </td>
                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('back.finance.invoices.show', invoice.id)">
                                {{ invoice.payment_method }}
                            </inertia-link>
                        </td>
                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('back.finance.invoices.show', invoice.id)">
                                {{ invoice.grand_total }}
                            </inertia-link>
                        </td>
                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('back.finance.invoices.show', invoice.id)">
                                {{ invoice.confirmed }}
                            </inertia-link>
                        </td>
                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('back.finance.invoices.show', invoice.id)">
                                {{ invoice.created_at }}
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
            </div>
            <pagination :links="invoices.links" />
        </div>
    </app-layout>
</template>

<script>
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

    export default {
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
        },
        props: {
            invoices: Object,
            filters: Object,
        },
        data() {
            return {
                form: {
                    // search: this.filters.search,
                    // trashed: this.filters.trashed,
                },
            }
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
        methods: {
            reset() {
                this.form = mapValues(this.form, () => null)
            },
        },
    }
</script>
