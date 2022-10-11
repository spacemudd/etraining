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
                    {title: 'reports', link: route('back.reports.index')},
                    {title: 'company-attendance', link: route('back.reports.company-attendance.index')},
                ]"
            ></breadcrumb-container>
        </div>

        <div class="container px-8 mx-auto grid">
            <div class="flex justify-end">
                <inertia-link class="btn-primary" :href="route('back.reports.company-attendance.create')">
                    {{ $t('words.new-report') }}
                </inertia-link>
            </div>
        </div>

        <div class="container px-6 mx-auto grid grid-cols-1">
            <Table
                    class="mt-5 w-full whitespace-no-wrap"
                    :filters="queryBuilderProps.filters"
                    :search="queryBuilderProps.search"
                    :columns="queryBuilderProps.columns"
                    :on-update="setQueryBuilder"
                    :meta="reports">
                    <template #head>
                        <tr>
                            <th class="text-left">{{ $t('words.report') }}</th>
                            <th class="text-left">{{ $t('words.company') }}</th>
                            <th class="text-left">{{ $t('words.trainees') }}</th>
                            <th class="text-left">{{ $t('words.period') }}</th>
                            <td class="text-left">{{ $t('words.status') }}</td>
                            <td class="text-left">{{ $t('words.last-updated') }}</td>
                            <td class="text-left"></td>
                        </tr>
                    </template>
                    <template #body>
                        <tr v-for="report in reports.data" :key="report.id">
                            <td>{{ report.number }}</td>
                            <td><template v-if="report.company">{{ report.company.resource_label }}</template></td>
                            <td>{{ report.trainees_count }}</td>
                            <td>{{ report.period }}</td>
                            <td v-if="report.approved_by"><span class="bg-green-400">{{ $t('words.approved') }}</span></td>
                            <td v-else><span class="bg-yellow-300 p-1 rounded text-black">{{ $t('words.review') }}</span></td>
                            <td>{{ report.updated_at_human }}</td>
                            <td>
                                <inertia-link class="px-4 flex items-center" :href="route('back.reports.company-attendance.show', report.id)">
                                    <ion-icon name="arrow-forward-outline" class="block w-6 h-6 fill-gray-400"></ion-icon>
                                </inertia-link>
                            </td>
                        </tr>
                    </template>
                </Table>
        </div>
    </app-layout>
</template>

<script>
    import JetLabel from '@/Jetstream/Label';
    import AppLayout from '@/Layouts/AppLayout'
    import IconNavigate from 'vue-ionicons/dist/ios-arrow-dropright'
    import BreadcrumbContainer from "@/Components/BreadcrumbContainer";
    import BtnLoadingIndicator from "../../../../Components/BtnLoadingIndicator";
    import { InteractsWithQueryBuilder } from '@protonemedia/inertiajs-tables-laravel-query-builder';
    import { Components } from "@protonemedia/inertiajs-tables-laravel-query-builder";
    import Table from '@/Components/Tailwind2/Table';

    export default {
        mixins: [InteractsWithQueryBuilder],
        props: [
            'reports',
            'filters',
        ],
        metaInfo() {
            return {
                title: this.$t('words.company-attendance'),
            }
        },
        components: {
            IconNavigate,
            AppLayout,
            JetLabel,
            BreadcrumbContainer,
            BtnLoadingIndicator,
            Table,
        },
        computed: {

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
        data() {
            return {

            }
        },
        methods: {

        },
    }
</script>
