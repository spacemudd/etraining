<template>
    <app-layout-complaints>
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
                        <th class="rtl:text-right font-weight-bold" @click.prevent="sortBy('created_at')">{{ $t('words.complaints') }}</th>
                    </tr>
                </template>

                <template #body>
                    <tr v-for="trainees_complaint in trainees_complaints.data" :key="trainees_complaint.id">
                        <td class="rtl:text-right text-black">
                                {{ trainees_complaint.number }}
                        </td>
                        <td class="rtl:text-right text-black">
                            {{ trainees_complaint.created_at }}
                        </td>
                    </tr>
                </template>
            </Table>
        </div>
    </app-layout-complaints>
</template>
<script>
import AppLayoutComplaints from "../../../../Layouts/AppLayoutComplaints";
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
        AppLayoutComplaints,
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
