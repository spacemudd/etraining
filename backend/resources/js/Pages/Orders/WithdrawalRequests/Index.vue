<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'orders', link: route('orders.index')},
                    {title: 'withdrawal-requests', link: route('orders.withdrawal-requests.index')},
                ]"
            ></breadcrumb-container>


            <div class="flex justify-between">
                <h1 class="mb-8 font-bold text-3xl">{{ $t('words.withdrawal-requests') }}</h1>
                <div class="mb-6 flex justify-between items-center">

                </div>
            </div>
        </div>

        <div class="container px-6 mx-auto grid pt-6">
                <Table
                    class="mt-5 w-full whitespace-no-wrap"
                    :filters="queryBuilderProps.filters"
                    :search="queryBuilderProps.search"
                    :columns="queryBuilderProps.columns"
                    :on-update="setQueryBuilder"
                    :meta="withdrawal_requests"
                >
                    <template #head>
                        <tr>
                            <th class="rtl:text-right">{{ $t('words.application-number') }}</th>
                            <th class="rtl:text-right">{{ $t('words.trainee') }}</th>
                            <th class="rtl:text-right">{{ $t('words.reason') }} / {{ $t('words.files') }}</th>
                            <th class="rtl:text-right" style="width:1%;">{{ $t('words.approved-at') }}</th>
                            <th class="rtl:text-left" style="width:1%;">{{ $t('words.actions') }}</th>
                        </tr>
                    </template>
                    <template #body>
                        <tr v-for="withdrawal in withdrawal_requests.data" :key="withdrawal.id">
                            <td class="rtl:text-right text-black">
                                <span class="bg-yellow-200 text-black px-2">#{{ withdrawal.number }}</span>
                                <br/>
                                <span class="text-xs">{{ withdrawal.created_at | formatDate }}</span>
                            </td>
                            <td class="rtl:text-right text-black">
                                {{ withdrawal.company.name_ar }}<br/>
                                {{ withdrawal.trainee.name }}<br/>
                                <span dir="ltr">{{ withdrawal.trainee.email }}</span><br/>
                                <span dir="ltr">{{ withdrawal.trainee.identity_number }}</span><br/>
                                <a dir="ltr" class="mt-10 text-green-500 hover:text-green-800" :href="withdrawal.trainee.whatsapp_link" target="_blank">
                                    {{ withdrawal.trainee.phone }}
                                </a>
                            </td>
                            <td class="rtl:text-right text-black">
                                {{ withdrawal.reason ? '"'+withdrawal.reason+'"' : '-' }}
                                <hr class="mt-1">
                                <ul>
                                    <li v-for="(attachment, index) in withdrawal.media">
                                        <a :href="attachment.download_url" target="_blank">
                                            <div class="text-xs block rounded bg-blue-100 hover:bg-red-500 w-full p-3 my-2 flex justify-between align-middle items-center">
                                                <p>
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="inline w-3 h-3 mb-1">
  <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
</svg>

                                                    {{ $t('words.file') }} {{ ++index }}<br/>
                                                    {{ attachment.created_at | formatDate }}
                                                </p>
                                                <p class="rtl:text-left" dir="ltr">
                                                    {{ attachment.file_name }}<br/>
                                                    {{ attachment.human_readable_size }}
                                                </p>
                                            </div>
                                        </a>
                                    </li>
                                </ul>
                            </td>
                            <td class="rtl:text-right text-black">
                                <template v-if="withdrawal.approved_at">
                                    {{ withdrawal.approved_at | formatDate }}<br/>
                                    {{ withdrawal.approved_by.email }}
                                </template>
                            </td>
                            <td class="rtl:text-left text-black justify-end">
                                <template v-if="!withdrawal.approved_at">
                                    <button @click="approveRequest(withdrawal.id)" class="bg-green-500 hover:bg-green-600 active:bg-green-700 foucs:bg-green-700 inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest focus:outline-none focus:shadow-outline-gray transition ease-in-out duration-150 disabled:cursor-not-allowed mx-2">
                                        {{ $t('words.approve') }}
                                    </button>
                                    <button @click="deleteRequest(withdrawal.id)" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest focus:outline-none focus:shadow-outline-gray transition ease-in-out duration-150 disabled:cursor-not-allowed mx-2 bg-red-500 hover:bg-red-600 active:bg-red-700 foucs:bg-red-700">
                                        {{ $t('words.delete') }}
                                    </button>
                                </template>
                            </td>
                        </tr>
                    </template>
                </Table>
            </div>
    </app-layout>
</template>

<script>
    import Pagination from '@/Shared/Pagination'
    import AppLayout from '@/Layouts/AppLayout'
    import IconNavigate from 'vue-ionicons/dist/ios-arrow-dropright'
    import BreadcrumbContainer from "@/Components/BreadcrumbContainer";
    import { InteractsWithQueryBuilder } from '@protonemedia/inertiajs-tables-laravel-query-builder';
    import { Components } from "@protonemedia/inertiajs-tables-laravel-query-builder";
    import Table from '@/Components/Tailwind2/Table';

    export default {
        mixins: [InteractsWithQueryBuilder],
        components: {
            IconNavigate,
            AppLayout,
            Pagination,
            BreadcrumbContainer,
            Table,
        },
        props: [
            'withdrawal_requests'
        ],
        data() {
            return {

            }
        },
        methods: {
            approveRequest(id) {
                if (confirm(this.$t('words.are-you-sure'))) {
                    this.$inertia.post(route('orders.withdrawal-requests.approve', {id: id}));
                }
            },
            deleteRequest(id) {
                if (confirm(this.$t('words.are-you-sure'))) {
                    this.$inertia.delete(route('orders.withdrawal-requests.destroy', {id: id}));
                }
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
        }
    }
</script>
