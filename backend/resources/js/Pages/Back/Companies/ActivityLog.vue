<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'companies', link: route('back.companies.index')},
                    {title_raw: company.name_ar},
                    {title: 'activity-log'},
                ]"
            ></breadcrumb-container>

            <div class="flex">
                <div class="flex ml-2">
                    <h3>{{ $t('words.from-date') }}</h3>
                    <input class="bg-gray-200 mx-1 px-2 rounded-sm" type="date" v-model="reportDateFrom">
                </div>
                <div class="flex mx-2">
                    <h3>{{ $t('words.to-date') }}</h3>
                    <input class="bg-gray-200 mx-1 px-2 rounded-sm" type="date" v-model="reportDateTo">
                </div>
                <a class="mx-2 border px-2 rounded"
                   :href="route('back.companies.trainees.activity-log.excel', {
                            company_id: company.id,
                            company: company.id,
                            from_date: reportDateFrom,
                            to_date: reportDateTo})"
                   target="_blank">
                    {{ $t('words.print') }}
                </a>
            </div>

            <div class="overflow-x-auto">
                <Table
                    class="mt-5 w-full whitespace-no-wrap"
                    :filters="queryBuilderProps.filters"
                    :search="queryBuilderProps.search"
                    :columns="queryBuilderProps.columns"
                    :on-update="setQueryBuilder"
                    :meta="activityLog"
                >
                    <template #head>
                        <tr>
                            <th class="rtl:text-right font-weight-bold" @click.prevent="sortBy('in_date')">{{ $t('words.joined-date') }}</th>
                            <th class="rtl:text-right font-weight-bold">{{ $t('words.trainee') }}</th>
                            <th class="rtl:text-right font-weight-bold">{{ $t('words.phone') }}</th>
                            <th class="rtl:text-right font-weight-bold">{{ $t('words.identity_number') }}</th>
                            <th class="rtl:text-right font-weight-bold" @click.prevent="sortBy('out_date')">{{ $t('words.leave-date') }}</th>
                        </tr>
                    </template>

                    <template #body>
                        <tr v-for="activity in activityLog.data" :key="activity.id" :class="{'bg-red-100': activity.out_date}">
                            <td class="rtl:text-right text-black">
                                <inertia-link :href="route('back.trainees.show', activity.trainee_id)">
                                    {{ activity.in_date_ksa }}
                                </inertia-link>
                            </td>
                            <td class="rtl:text-right text-black">
                                <inertia-link :href="route('back.trainees.show', activity.trainee_id)">
                                    {{ activity.trainee_name }}
                                </inertia-link>
                            </td>
                            <td class="rtl:text-right text-black">
                                <inertia-link :href="route('back.trainees.show', activity.trainee_id)">
                                    {{ activity.trainee_phone_number }}
                                </inertia-link>
                            </td>
                            <td class="rtl:text-right text-black">
                                <inertia-link :href="route('back.trainees.show', activity.trainee_id)">
                                    {{ activity.trainee_identity_number }}
                                </inertia-link>
                            </td>
                            <td class="rtl:text-right text-black">
                                <inertia-link :href="route('back.trainees.show', activity.trainee_id)">
                                    {{ activity.out_date_ksa  }}
                                </inertia-link>
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
import mapValues from 'lodash/mapValues'
import Pagination from '@/Shared/Pagination'
import pickBy from 'lodash/pickBy'
import throttle from 'lodash/throttle'

import AppLayout from '@/Layouts/AppLayout'
import JetSectionBorder from '@/Jetstream/SectionBorder'
import Breadcrumb from "@/Components/Breadcrumb";
import JetDialogModal from '@/Jetstream/DialogModal'
import JetInput from '@/Jetstream/Input'
import JetInputError from '@/Jetstream/InputError'
import JetActionMessage from '@/Jetstream/ActionMessage';
import JetButton from '@/Jetstream/Button';
import JetFormSection from '@/Jetstream/FormSection';
import JetLabel from '@/Jetstream/Label';
import CompanyContractsPagination from "@/Components/CompanyContractsPagination";
import BreadcrumbContainer from "@/Components/BreadcrumbContainer";
import EmptySlate from "@/Components/EmptySlate";

export default {
    props: [
        'sessions',
        'company',
        'instructors',
        'invoices',
        'trainees_trashed_count',
        'filters',
        'activityLog',
    ],

    mixins: [InteractsWithQueryBuilder],
    components: {
        Table,
        Pagination,
        AppLayout,
        JetSectionBorder,
        Breadcrumb,
        JetDialogModal,
        JetInput,
        JetInputError,
        JetActionMessage,
        JetButton,
        JetFormSection,
        JetLabel,
        CompanyContractsPagination,
        BreadcrumbContainer,
        EmptySlate,
    },
    data() {
        return {
            reportDateFrom: null,
            reportDateTo: null,
            form: {
                //
            }
        }
    },
    computed: {
        hasSearchRows() {
            return Object.keys(this.activityLog.search || {}).length > 0;
        },
    },
    watch: {
        form: {
            handler: throttle(function() {
                let query = pickBy(this.form)
                this.$inertia.replace(this.route('back.companies.trainees.activity-log', Object.keys(query).length ? query : { remember: 'forget' }))
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
        reset() {
            this.form = mapValues(this.form, () => null)
        },
    }
}
</script>
