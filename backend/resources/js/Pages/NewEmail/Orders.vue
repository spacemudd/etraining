<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'orders', link: route('new_email.orders')},
                ]"
            ></breadcrumb-container>

            <div class="">
                <Table
                    class="mt-5 w-full whitespace-no-wrap"
                >
                    <template #head>
                        <tr :style="{background: ['#f7f7f7'], position: ['sticky'], top: [0]}">
                            <th :style="{background: ['#f7f7f7'], position: ['sticky'], right: [0]}">{{ $t('words.account-name') }}</th>
                            <th :style="{background: ['#f7f7f7'], position: ['sticky'], right: ['90px']}">{{ $t('words.invoice') }}</th>
                            <th>{{ $t('words.date-period') }}</th>
                            <th>{{ $t('words.company') }}</th>
                            <th>{{ $t('words.status') }}</th>
                            <th>{{ $t('words.payment-method') }}</th>
                            <th>{{ $t('words.amount') }}</th>
                            <th>{{ $t('words.submitted-receipt') }}</th>
                            <th>{{ $t('words.bank-name') }}</th>
                        </tr>
                    </template>
                    <template #body>
                        <tr v-for="mail in new_emails" :key="mail.id">
                            <td :style="{background: ['#f7f7f7'], position: ['sticky'], right: [0], top: ['35px']}">
                                {{ mail.number }}
                            </td>
                            <td class="rtl:text-right text-black">
                                {{  mail.applicant  }}
                            </td>
                            <td class="rtl:text-right text-black">
                                    {{ mail.personal_email }}
                            </td>
                            <td class="rtl:text-right text-black">
                                    <div>
                                        <span class="text-white bg-red-500 rounded-lg px-3 py-1 font-bold border-solid border-2 border-red-500">
                                            {{ mail.job_title }}
                                        </span>
                                    </div>
                            </td>
                            <td class="rtl:text-right text-black">
                                    <span class="img {display:block} inline-block inline-flex">
                                    {{ mail.phone }}
                                    </span>
                            </td>
                            <td class="rtl:text-right text-black">
                                    {{ mail.manager_name }}
                            </td>
                            <td class="rtl:text-right text-black">
                                    {{ mail.manager_email }}
                            </td>
                            <td class="rtl:text-right text-black">
                                <inertia-link :href="route('dashboard')">
                                    {{ $t('words.from') }} {{ mail.new_email }}
                                </inertia-link>
                            </td>
                            <td class="rtl:text-right text-black">
                                    <span
                                          class="bg-green-500 text-white rounded px-2">
                                        {{ $t('words.yes') }}
                                    </span>
                            </td>
                            <td class="rtl:text-right text-black">
<!--                                <button @click="deleteInvoice(mail.id)"-->
<!--                                        v-can="'can-delete-invoice-anytime'"-->
<!--                                        type="button"-->
<!--                                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest focus:outline-none focus:shadow-outline-gray transition ease-in-out duration-150 disabled:cursor-not-allowed mx-2 bg-red-500 hover:bg-red-600 active:bg-red-700 foucs:bg-red-700">-->
<!--                                    {{ $t('words.delete') }}-->
<!--                                </button>-->
                            </td>
                        </tr>
                    </template>
                </Table>

            </div>

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
    props: ['new_emails'],

    methods: {
    },
}


</script>
