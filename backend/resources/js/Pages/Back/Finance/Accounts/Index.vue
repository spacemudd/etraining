<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'finance', link: route('back.finance')},
                    {title: 'client-accounts', link: route('back.finance.accounts.index')},
                ]"
            ></breadcrumb-container>
            <div class="grid md:grid-cols-4 grid-cols-1 gap-6">
                <inertia-link :href="route('back.finance.accounts.index')" class="col-span-1 bg-white shadow-lg rounded-lg p-5 transition-all duration-500 ease-in-out hover:bg-gray-200 text-center">
                    {{ $t('words.filter-late-clients') }}
                </inertia-link>
            </div>
            <h1 class="mb-8 font-bold text-3xl">
                <!--{{ $t('words.companies') }}-->
            </h1>
            <div class="mb-6 flex justify-between items-center">
                <!--<inertia-link class="btn-gray" :href="route('back.companies.create')">-->
                <!--    <span>{{ $t('words.new') }}</span>-->
                <!--</inertia-link>-->
            </div>
            <div class="bg-white rounded shadow overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <tr class="text-left font-bold">
                        <th class="px-6 pt-6 pb-4">{{ $t('words.account-name') }}</th>
                        <th class="px-6 pt-6 pb-4">{{ $t('words.trainees') }}</th>
                        <th class="px-6 pt-6 pb-4">{{ $t('words.status') }}</th>
                        <th class="px-6 pt-6 pb-4">{{ $t('words.outstanding-amount') }}</th>
                        <th class="px-6 pt-6 pb-4">{{ $t('words.created-at') }}</th>
                    </tr>
                    <tr v-for="account in accounts.data" :key="account.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('back.finance.accounts.show', account.id)">
                                {{ account.reference_number }}
                            </inertia-link>
                        </td>
                        <td class="border-t">
                            -
                        </td>
                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('back.finance.accounts.show', account.id)">
                                {{ account.status }}
                            </inertia-link>
                        </td>
                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('back.finance.accounts.show', account.id)">
                                {{ account.outstanding_amount }}
                            </inertia-link>
                        </td>
                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('back.finance.accounts.show', account.id)">
                                {{ account.created_at }}
                            </inertia-link>
                        </td>
                        <td class="border-t w-px">
                            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('back.finance.accounts.show', account.id)">
                                <ion-icon name="arrow-forward-outline" class="block w-6 h-6 fill-gray-400"></ion-icon>
                            </inertia-link>
                        </td>
                    </tr>
                    <tr v-if="accounts.data.length === 0">
                        <td class="border-t px-6 py-4" colspan="5">
                            <empty-slate/>
                        </td>
                    </tr>
                </table>
            </div>
            <pagination :links="accounts.links" />
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
        metaInfo: { title: 'Financial accounts' },
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
            accounts: Object,
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
                    this.$inertia.replace(this.route('back.finance.accounts.index', Object.keys(query).length ? query : { remember: 'forget' }))
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
