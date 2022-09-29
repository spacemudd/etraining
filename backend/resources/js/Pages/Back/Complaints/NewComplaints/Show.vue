<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">

            <breadcrumb-container
                :crumbs="[
                        {title: 'dashboard', link: route('dashboard')},
                        {title: 'complaints', link: route('complaints.index')},
                        {title: 'new_complaints', link: route('complaints.NewComplaints.Show')},
                    ]"
            ></breadcrumb-container>

            <div class="grid grid-cols-3 gap-6">
                <div>

                </div>
                <div class="grid grid-cols-3 gap-6">
                    <div>
                        <inertia-link class="font-bold text-center mt-5 inline-flex items-center px-4 py-2 bg-green-400 hover:bg-red-700 active:bg-red-900 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest focus:outline-none focus:border-green-400 focus:shadow-outline-green transition ease-in-out duration-150 disabled:cursor-not-allowed mx-"
                                      :href="route('complaints.NewComplaints.Show')">
                            {{ $t('words.new_complaints') }}
                        </inertia-link>
                    </div>
                    <div>
                        <inertia-link class="font-bold text-center mt-5 inline-flex items-center px-1.5 py-2 bg-gray-300 hover:bg-red-700 active:bg-red-900 border border-transparent rounded-md font-semibold text-xs text-black uppercase ltr:tracking-widest focus:outline-none focus:border-gray-300 focus:shadow-outline-gray transition ease-in-out duration-150 disabled:cursor-not-allowed mx-"
                                      :href="route('complaints.InProgress.Show')">
                            {{ $t('words.in_progress_complaints') }}
                        </inertia-link>
                    </div>
                    <div>
                        <inertia-link class="font-bold text-center mt-5 inline-flex items-center px-4 py-2 bg-gray-300 hover:bg-green-700 active:bg-green-900 border border-transparent rounded-md font-semibold text-xs text-black uppercase ltr:tracking-widest focus:outline-none focus:border-gray-300 focus:shadow-outline-gray transition ease-in-out duration-150 disabled:cursor-not-allowed mx-"
                                      :href="route('complaints.DoneComplaints.Show')">
                            {{ $t('words.done_complaints') }}
                        </inertia-link>
                    </div>
                </div>
                <div>

                </div>
            </div>
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
                            <th class="rtl:text-right font-weight-bold" @click.prevent="sortBy('number')">{{ $t('words.complaint-number') }}</th>
                            <th class="rtl:text-right font-weight-bold" @click.prevent="sortBy('created_at')">{{ $t('words.data') }}</th>
                            <th class="rtl:text-right font-weight-bold">{{ $t('words.company') }}</th>
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
                            v-if="trainees_complaint.complaints_status === 0">
                            <td class="rtl:text-right text-black">
                                <inertia-link :href="route('complaints.Show', trainees_complaint.id)">
                                {{ trainees_complaint.complaints_number_formatted }}
                                </inertia-link>
                            </td>
                            <td class="rtl:text-right text-black">
                                <inertia-link :href="route('complaints.Show', trainees_complaint.id)">
                                <span class="font-bold">{{ $t('words.name') }}: </span>{{ trainees_complaint.trainee.name }}<br/>
                                <span class="font-bold">{{ $t('words.identity_number') }}: </span>{{ trainees_complaint.trainee.identity_number }}<br/>
                                <span class="font-bold">{{ $t('words.phone') }}: </span>{{ trainees_complaint.trainee.phone }}
                                </inertia-link>
                            </td>
                            <td class="rtl:text-right text-black">
                                <inertia-link :href="route('complaints.Show', trainees_complaint.id)">
                               <div v-if="trainees_complaint.company">{{ trainees_complaint.company.name_ar }}</div>
                                </inertia-link>
                            </td>
                            <td class="rtl:text-right text-black">
                                <inertia-link :href="route('complaints.Show', trainees_complaint.id)">
                                {{ trainees_complaint.contact_way }}
                                </inertia-link>
                            </td>
                            <td class="rtl:text-right text-black">
                                <inertia-link :href="route('complaints.Show', trainees_complaint.id)">
                                {{ trainees_complaint.complaints }}
                                </inertia-link>
                            </td>
                            <td class="rtl:text-right text-black">
                                <inertia-link :href="route('complaints.Show', trainees_complaint.id)">
                                {{ trainees_complaint.created_at | formatDate }}
                                </inertia-link>
                            </td>
                            <td class="rtl:text-right text-black">
                                <inertia-link :href="route('complaints.Show', trainees_complaint.id)">
                                {{ trainees_complaint.reply }}
                                </inertia-link>
                            </td>
                            <td class="rtl:text-right text-black">
                                <inertia-link :href="route('complaints.Show', trainees_complaint.id)">
                                {{ trainees_complaint.note }}
                                </inertia-link>
                            </td>
                            <td class="rtl:text-right text-black">
                                <inertia-link :href="route('complaints.Show', trainees_complaint.id)">
                                {{ trainees_complaint.results }}
                                </inertia-link>
                            </td>
                            <td class="rtl:text-right text-black">
                                <button @click="RollOut(trainees_complaint.id)"
                                        type="button"
                                        v-if="trainees_complaint.complaints_status === 0"
                                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest focus:outline-none focus:shadow-outline-gray transition ease-in-out duration-150 disabled:cursor-not-allowed mx-2 bg-gray-700 hover:bg-red-600 active:bg-red-700 foucs:bg-red-700">
                                    {{ $t('words.roll-out') }}
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
                this.$inertia.replace(this.route('complaints.NewComplaints.Show', Object.keys(query).length ? query : { remember: 'forget' }))
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
            this.$inertia.put(route('complaints.NewToInProgressStatus', trainees_complaint), {
                id: trainees_complaint,
                complaints_status: 1,
            })
        },

    }
}
</script>
