<!--
  - Copyright (c) 2020 - Clarastars, LLC  - All Rights Reserved.
  -
  - Unauthorized copying of this file via any medium is strictly prohibited.
  - This file is a proprietary of Clarastars LLC and is confidential / educational purpose only.
  -
  - https://clarastars.com - info@clarastars.com
  - @author Shafiq al-Shaar <shafiqalshaar@gmail.com>
  -->

<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'finance', link: route('back.finance')},
                    {title: 'payments', link: route('back.finance.payments.index')},
                ]"
            ></breadcrumb-container>
            <div class="flex justify-end">
                <!--<div class="mb-6 flex justify-between items-center">-->
                <!--    <inertia-link class="btn-gray" :href="route('back.finance.invoicing.create')">-->
                <!--        <span>{{ $t('words.new') }}</span>-->
                <!--    </inertia-link>-->
                <!--</div>-->
            </div>

            <div class="bg-white rounded shadow overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <colgroup>
                        <col style="width:100px;">
                        <col style="width:200px;">
                    </colgroup>
                    <tr class="text-left font-bold">
                        <th class="px-6 pt-6 pb-4">{{ $t('words.time') }}</th>
                        <th class="px-6 pt-6 pb-4">{{ $t('words.status') }}</th>
                        <th class="px-6 pt-6 pb-4">{{ $t('words.invoice') }}</th>
                    </tr>
                    <tr v-for="payment in payments.data" :key="payment.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
                        <td class="border-t">
                            <div class="px-6 py-4 flex focus:text-indigo-500">
                                <inertia-link :href="route('back.finance.payments.show', payment.id)">
                                    {{ payment.created_at_display }}
                                </inertia-link>
                            </div>
                        </td>
                        <td class="border-t">
                            <div class="px-6 py-4 flex items-center focus:text-indigo-500">
                                <inertia-link :href="route('back.finance.payments.show', payment.id)">
                                    <span v-html="payment.status_css"></span>
                                </inertia-link>
                            </div>
                        </td>
                        <td class="border-t">
                            <div class="px-6 py-4 flex items-center focus:text-indigo-500">
                                <inertia-link :href="route('back.finance.payments.show', payment.id)">
                                    {{ payment.sale_invoice.number }}
                                </inertia-link>
                            </div>
                        </td>
                        <td class="border-t w-px">
                            <inertia-link class="px-4 flex items-center" :href="route('back.finance.payments.show', payment.id)" tabindex="-1">
                                <ion-icon name="arrow-forward-outline" class="block w-6 h-6 fill-gray-400"></ion-icon>
                            </inertia-link>
                        </td>
                    </tr>
                    <tr v-if="payments.data.length === 0">
                        <td class="border-t px-6 py-4" colspan="4">
                            <empty-slate>
                            </empty-slate>
                        </td>
                    </tr>
                </table>
            </div>
            <pagination :links="payments.links" />
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
    import BreadcrumbContainer from "@/Components/BreadcrumbContainer";
    import EmptySlate from "@/Components/EmptySlate";
    import BtnLoadingIndicator from "@/Components/BtnLoadingIndicator";

    export default {
        metaInfo: { title: 'Payments' },
        // layout: Layout,
        components: {
            BtnLoadingIndicator,
            EmptySlate,
            IconNavigate,
            AppLayout,
            // Icon,
            Pagination,
            // SearchFilter,
            BreadcrumbContainer,
        },
        props: {
            payments: Object,
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
                    this.$inertia.replace(this.route('companies', Object.keys(query).length ? query : { remember: 'forget' }))
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
