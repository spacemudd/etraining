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
                            <th class="rtl:text-right font-weight-bold" @click.prevent="sortBy('number')">{{ $t('words.complaints-number') }}</th>
                            <th class="rtl:text-right font-weight-bold" @click.prevent="sortBy('created_at')">{{ $t('words.name') }}</th>
                            <th class="rtl:text-right font-weight-bold">{{ $t('words.identity_number') }}</th>
                            <th class="rtl:text-right font-weight-bold">{{ $t('words.company') }}</th>
                            <th class="rtl:text-right font-weight-bold">{{ $t('words.phone') }}</th>
                            <th class="rtl:text-right font-weight-bold">{{ $t('words.contact-way') }}</th>
                            <th class="rtl:text-right font-weight-bold">{{ $t('words.complaints') }}</th>
                            <th class="rtl:text-right font-weight-bold">{{ $t('words.order-date') }}</th>
                            <th class="rtl:text-right font-weight-bold">{{ $t('words.reply') }}</th>
                            <th class="rtl:text-right font-weight-bold">{{ $t('words.note') }}</th>
                            <th class="rtl:text-right font-weight-bold">{{ $t('words.results') }}</th>
                            <th class="rtl:text-right font-weight-bold">{{ $t('words.actions') }}</th>
                        </tr>
                    </template>

                    <template #body>
                        <tr v-for="trainees_complaint in trainees_complaints.data" :key="trainees_complaint.id"
                            v-if="trainees_complaint.complaints_status === 2">
                            <td class="rtl:text-right text-black">
                                {{ trainees_complaint.complaints_number_formatted }}
                            </td>
                            <td class="rtl:text-right text-black">
                                {{ trainees_complaint.trainee.name }}
                            </td>
                            <td class="rtl:text-right text-black">
                                {{ trainees_complaint.trainee.identity_number }}
                            </td>
                            <td class="rtl:text-right text-black">
                                {{ trainees_complaint.company.name_ar }}
                            </td>
                            <td class="rtl:text-right text-black">
                                {{ trainees_complaint.trainee.phone }}
                            </td>
                            <td class="rtl:text-right text-black">
                                {{ trainees_complaint.contact_way }}
                            </td>
                            <td class="rtl:text-right text-black">
                                {{ trainees_complaint.complaints }}
                            </td>
                            <td class="rtl:text-right text-black">
                                {{ trainees_complaint.created_at | formatDate }}
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
                            <td class="rtl:text-right text-black">
                                <button @click="RollOut(trainees_complaint.id)"
                                        type="button"
                                        v-if="trainees_complaint.complaints_status === 2"
                                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest focus:outline-none focus:shadow-outline-gray transition ease-in-out duration-150 disabled:cursor-not-allowed mx-2 bg-red-500 hover:bg-red-600 active:bg-red-700 foucs:bg-red-700">
                                    {{ $t('words.return-complaints') }}
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
import AppLayout from "../../../../Layouts/AppLayout";
import { InteractsWithQueryBuilder } from '@protonemedia/inertiajs-tables-laravel-query-builder';
import { Components } from "@protonemedia/inertiajs-tables-laravel-query-builder";
import Table from '@/Components/Tailwind2/Table';

import BreadcrumbContainer from "@/Components/BreadcrumbContainer";
import mapValues from "lodash/mapValues";
import Pagination from '@/Shared/Pagination'
import pickBy from 'lodash/pickBy'
// import SearchFilter from '@/Shared/SearchFilter'
import throttle from 'lodash/throttle'
import IconNavigate from 'vue-ionicons/dist/ios-arrow-dropright'
import EmptySlate from "@/Components/EmptySlate";
import {Inertia} from "@inertiajs/inertia";

export default {
    mixins: [InteractsWithQueryBuilder],
    metaInfo: { title: 'Complaints trainees_complaints' },

    components: {
        AppLayout,
        Table,
        BreadcrumbContainer,
        Pagination,
        EmptySlate,
    },

    props: {
        trainees_complaints: Object,
        filters: Object,
    },
    watch: {
        form: {
            handler: throttle(function() {
                let query = pickBy(this.form)
                this.$inertia.replace(this.route('complaints.DoneComplaints.Show', Object.keys(query).length ? query : { remember: 'forget' }))
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
        RollOut(trainees_complaint) {
            this.$inertia.put(route('complaints.DoneToInProgressStatus', trainees_complaint), {
                id: trainees_complaint,
                complaints_status: 1,
            })
        }
    }
}

</script>
