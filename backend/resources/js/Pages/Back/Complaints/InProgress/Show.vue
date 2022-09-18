<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">

            <breadcrumb-container
                :crumbs="[
                        {title: 'dashboard', link: route('dashboard')},
                    ]"
            ></breadcrumb-container>

            <div class="overflow-x-auto">
                <Table
                    class="mt-5 w-full whitespace-no-wrap"
                    :filters="queryBuilderProps.filters"
                    :search="queryBuilderProps.search"
                    :columns="queryBuilderProps.columns"
                    :on-update="setQueryBuilder"
                    :meta="trainees_complaints"
                >
                    <template #head>
                        <tr>
                            <th class="rtl:text-right font-weight-bold" @click.prevent="sortBy('number')">{{ $t('words.complaints') }}</th>
                            <th class="rtl:text-right font-weight-bold" @click.prevent="sortBy('created_at')">{{ $t('words.name') }}</th>
                            <th class="rtl:text-right font-weight-bold">{{ $t('words.identity_number') }}</th>
                            <th class="rtl:text-right font-weight-bold">{{ $t('words.company') }}</th>
                            <th class="rtl:text-right font-weight-bold">{{ $t('words.phone') }}</th>
                            <th class="rtl:text-right font-weight-bold">{{ $t('words.contact_way') }}</th>
                            <th class="rtl:text-right font-weight-bold">{{ $t('words.complaints') }}</th>
                            <th class="rtl:text-right font-weight-bold">{{ $t('words.order-date') }}</th>
                            <th class="rtl:text-right font-weight-bold">{{ $t('words.actions') }}</th>
                            <th class="rtl:text-right font-weight-bold">{{ $t('words.reply') }}</th>
                            <th class="rtl:text-right font-weight-bold">{{ $t('words.note') }}</th>
                            <th class="rtl:text-right font-weight-bold">{{ $t('words.results') }}</th>
                        </tr>
                    </template>

                    <template #body>
                        <tr v-for="trainees_complaint in trainees_complaints.data" :key="trainees_complaint.id"
                            v-if="trainees_complaint.complaints_status === 1">
                            <td class="rtl:text-right text-black">
                                {{ trainees_complaint.number }}
                            </td>
                            <td class="rtl:text-right text-black">
                                {{ trainees_complaint.name }}
                            </td>
                            <td class="rtl:text-right text-black">
                                {{ trainees_complaint.identity_number }}
                            </td>
                            <td class="rtl:text-right text-black">
                                {{ trainees_complaint.company }}
                            </td>
                            <td class="rtl:text-right text-black">
                                {{ trainees_complaint.phone }}
                            </td>
                            <td class="rtl:text-right text-black">
                                {{ trainees_complaint.contact_way }}
                            </td>
                            <td class="rtl:text-right text-black">
                                {{ trainees_complaint.complaints }}
                            </td>
                            <td class="rtl:text-right text-black">
                                {{ trainees_complaint.created_at }}
                            </td>
                            <td class="rtl:text-right text-black">
                                {{ trainees_complaint.actions }}
                            </td>
                            <td class="rtl:text-right text-black">
                                {{ trainees_complaint.reply }}
                            </td>
                            <td class="rtl:text-right text-black">
                                {{ trainees_complaint.note }}
                            </td>
                            <td class="rtl:text-right text-black">
                                {{ trainees_complaint.result }}
                            </td>
                        </tr>
                    </template>
                </Table>
            </div>
        </div>
    </app-layout>
</template>
<script>
import AppLayout from "../../../../Layouts/AppLayout";
import { InteractsWithQueryBuilder } from '@protonemedia/inertiajs-tables-laravel-query-builder';
import { Components } from "@protonemedia/inertiajs-tables-laravel-query-builder";
import Table from '@/Components/Tailwind2/Table';

import BreadcrumbContainer from "@/Components/BreadcrumbContainer";

export default {
    mixins: [InteractsWithQueryBuilder],
    props: {
        trainees_complaints: Object,
        filters: Object,
    },
    components: {
        AppLayout,
        Table,
        BreadcrumbContainer,
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


    }
}

</script>
