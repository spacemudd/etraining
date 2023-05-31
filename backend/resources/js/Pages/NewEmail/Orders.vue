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
                    :filters="queryBuilderProps.filters"
                    :search="queryBuilderProps.search"
                    :columns="queryBuilderProps.columns"
                    :on-update="setQueryBuilder"
                    :meta="new_emails">
                >
                    <template #head class="rtl:text-right text-black">
                        <tr  class="rtl:text-right text-black">
                            <th class="rtl:text-right text-black">{{ $t('words.status') }}</th>
                            <th class="rtl:text-right text-black">{{ $t('words.order_number') }}</th>
                            <th class="rtl:text-right text-black">{{ $t('words.applicant') }}</th>
                            <th class="rtl:text-right text-black">{{ $t('words.manager_name') }}</th>
                            <th class="rtl:text-right text-black">{{ $t('words.new_email') }}</th>
                            <th class="rtl:text-right text-black">{{ $t('words.actions') }}</th>
                        </tr>
                    </template>
                    <template #body>
                        <tr v-for="mail in new_emails.data" :key="mail.id"><td class="rtl:text-right text-black">
                            <div v-if="mail.status === 0">
                                        <span class="text-black bg-yellow-200 rounded-lg px-3 py-1 font-bold border-solid border-2 border-yellow-200">
                                            {{ mail.status_formatted }}
                                        </span>
                            </div>
                            <div v-if="mail.status === 1">
                                        <span class="text-black bg-green-200 rounded-lg px-3 py-1 font-bold border-solid border-2 border-green-200">
                                            {{ mail.status_formatted }}
                                        </span>
                            </div>
                            <div v-if="mail.status === 2">
                                        <span class="text-black bg-red-200 rounded-lg px-3 py-1 font-bold border-solid border-2 border-red-200">
                                            {{ mail.status_formatted }}
                                        </span>
                            </div>
                        </td>
                            <td :style="{background: ['#f7f7f7'], right: [0], top: ['35px']}">
                                {{ mail.number }}
                            </td>
                            <td class="rtl:text-right text-black">
                                {{ mail.applicant }}
                                <br>
                                {{ mail.job_title }}
                                <br>
                                {{ mail.phone }}
                                <br>
                                {{ mail.personal_email }}
                            </td>
                            <td class="rtl:text-right text-black">
                                {{ mail.manager_name }}
                                <br>
                                {{ mail.manager_email }}
                            </td>
                            <td class="rtl:text-right text-black">
                                {{ mail.new_email }}
                            </td>

                            <td class="rtl:text-right text-black">
                                <button @click="approveMail(mail.id)"
                                        v-if="mail.status === 0"
                                        type="button"
                                        class="inline-flex items-center font-bold px-4 py-2 border border-transparent rounded-md font-semibold text-s text-white uppercase ltr:tracking-widest focus:outline-none focus:shadow-outline-gray transition ease-in-out duration-150 disabled:cursor-not-allowed mx-2 bg-green-500 hover:bg-green-600 active:bg-red-700 foucs:bg-green-700">
                                    {{ $t('words.approve') }}
                                    {{ mail.id }}
                                </button>
                                <button @click="rejectMail(mail.id)"
                                        v-if="mail.status === 0"
                                        type="button"
                                        class="inline-flex items-center font-bold px-4 py-2 border border-transparent rounded-md font-semibold text-s text-white uppercase ltr:tracking-widest focus:outline-none focus:shadow-outline-gray transition ease-in-out duration-150 disabled:cursor-not-allowed mx-2 bg-red-500 hover:bg-red-600 active:bg-red-700 foucs:bg-red-700">
                                    {{ $t('words.reject') }}
                                </button>
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
    mixins: [InteractsWithQueryBuilder],
    props: ['new_emails', 'filters'],
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
    methods: {
        approveMail(mailId) {
            if (confirm(this.$t('words.are-you-sure'))) {
                this.$inertia.post(route('new_email.approve-mail', {id: mailId}));
            }
        },
        rejectMail(mailId) {
            if (confirm(this.$t('words.are-you-sure'))) {
                this.$inertia.put(route('new_email.reject', {new_emails: mailId}));
            }
        }
    },
}


</script>
