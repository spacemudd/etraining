<style lang="css">
.v-dropdown-menu__container {
    margin-top: 5px;
    border-radius: 5px;
    border-bottom: 1px solid red;
}
.dropdown-items {
    border-radius: 5px;
    padding: 10px;
}
.dropdown-items li {
    margin: 10px 0;
    border-bottom: 1px solid #e9e9e9;
    padding-bottom: 10px;
}
</style>

<template>
    <app-layout>

        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'trainees', link: route('back.trainees.index')},
                    {title: 'manage-absence-notes'},
                ]"
            ></breadcrumb-container>
            <!-- <div class="flex justify-between">
                <h1 class="mb-8 font-bold text-3xl">{{ $t('words.manage-absence-notes') }}</h1>
            </div> -->
            <div class="bg-white rounded shadow overflow-x-auto">
            <Table
                    class="mt-5 w-full whitespace-no-wrap"
                    :filters="queryBuilderProps.filters"
                    :search="queryBuilderProps.search"
                    :columns="queryBuilderProps.columns"
                    :on-update="setQueryBuilder"
                    :meta="absence_notes">
                    <template #head>
                        <tr>
                            <th class="text-left">{{ $t('words.name') }}</th>
                            <th class="text-left">{{ $t('words.phone') }}</th>
                            <th class="text-left" style="max-width:200px;">{{ $t('words.company') }}</th>
                            <th class="text-left">{{ $t('words.files') }}</th>
                            <td class="text-left">{{ $t('words.actions') }}</td>
                        </tr>
                    </template>
                    <template #body>
                        <tr v-for="note in absence_notes.data" :key="note.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
                            <td class="border-t">
                                <div class="px-6 py-4 flex items-center focus:text-indigo-500">
                                    <inertia-link :href="route('back.trainees.show', note.trainee.id)">
                                        {{ note.trainee.name }}
                                        <br/>
                                    </inertia-link>
                                </div>
                            </td>
                            <td class="border-t">
                                <inertia-link class="px-6 py-4 flex items-center" :href="route('back.trainees.show', note.trainee.id)" tabindex="-1">
                                    <div v-if="note.trainee.phone">
                                        {{ note.trainee.phone }}
                                    </div>
                                </inertia-link>
                            </td>
                            <td class="border-t">
                                <inertia-link class="px-6 py-4 flex items-center" :href="route('back.companies.show', note.trainee.id)" tabindex="-1">
                                    <span v-if="note.trainee.company">{{ note.trainee.company.name_ar }}</span>
                                    <span v-else class="italic text-gray-500 text-xs">{{ $t('words.not-assigned-to-a-company') }}</span>
                                </inertia-link>
                            </td>
                            <td class="border-t">
                                <div v-if="note.files.length">
                                    <a  class="text-sm text-blue-500" :href="route('back.media.download', {media_id: note.files[0].id})" target="_blank">
                                        {{ $t('words.download') }}
                                    </a>
                                </div>
                            </td>
                            <td class="border-t w-px">
                                <div class="flex flex-col gap-2 justify-center text-center p-5">
                                    <div v-if="!note.rejected_at && !note.approved_at">
                                        <button class="btn btn-primary text-center" @click="approve(note.id)">{{ $t('words.approve') }}</button>
                                        <!-- <button class="btn btn-primary text-center" @click="reject(note.id)">{{ $t('words.reject') }}</button> -->
                                         <button class="btn btn-primary text-center" @click="showRejectInput(note.id)">{{ $t('words.reject') }}</button>
                                         <div v-if="note.id === rejectInput.noteId" class="mt-2">
                                           <input type="text" v-model="rejectInput.reason" :readonly="rejectInput.submitted" placeholder="أدخل سبب الرفض" class="border rounded px-2 py-1 w-full">
                                           <button v-if="!rejectInput.submitted" class="btn btn-primary mt-2" @click="reject(note.id)">{{ $t('words.send') }}</button>
                                         </div>
                                    </div>
                                    <div v-else>
                                        <div v-if="note.rejected_at">
                                            {{ $t('words.rejected') }}
                                            <span class="text-xs" dir="ltr">{{ note.rejected_at_timezone }}</span>
                                             <div v-if="note.rejected_reason" class="mt-2 text-red-500">
                                                 <strong>{{ $t('words.reject-reason') }}:</strong> {{ note.rejected_reason }}
                                             </div>
                                        </div>
                                        <div v-if="note.approved_at">
                                            {{ $t('words.approved') }}
                                            <br/>
                                            <span class="text-xs" dir="ltr">{{ note.approved_at_timezone }}</span>
                                        </div>
                                    </div>

                                </div>
                            </td>
                        </tr>
                        <tr v-if="absence_notes.data.length === 0">
                        <td class="border-t px-6 py-4" colspan="4">
                            <empty-slate/>
                        </td>
                    </tr>
                    </template>
           </Table>
            </div>
            <pagination :links="absence_notes.links" />
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
import ExportTraineesToExcel from '@/Components/ExportTraineesToExcel';
import DropdownMenu from 'v-dropdown-menu'
import 'v-dropdown-menu/dist/v-dropdown-menu.css' // Base style, required.
import AdminSearchbar from '@/Components/AdminSearchbar';
import { InteractsWithQueryBuilder } from '@protonemedia/inertiajs-tables-laravel-query-builder';
import Table from '@/Components/Tailwind2/Table';
import { Components } from "@protonemedia/inertiajs-tables-laravel-query-builder";

export default {
    metaInfo: { title: 'Manage absence notes' },
    // layout: Layout,
    mixins: [InteractsWithQueryBuilder],
    components: {
        EmptySlate,
        BreadcrumbContainer,
        IconNavigate,
        AppLayout,
        // Icon,
        Pagination,
        // SearchFilter,
        ExportTraineesToExcel,
        DropdownMenu,
        AdminSearchbar,
        Table,
        Components,
    },
    props: {
        absence_notes: Object,
        filters: Object,
    },
    data() {
        return {
            actionsDropDownView: false,
            form: {
                // search: this.filters.search,
                // trashed: this.filters.trashed,
            },
            rejectInput: {
                noteId: null,
                reason: '',
                submitted: false
            },
        }
    },
    watch: {
        form: {
            handler: throttle(function() {
                let query = pickBy(this.form)
                this.$inertia.replace(this.route('trainees', Object.keys(query).length ? query : { remember: 'forget' }))
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
        //method to show input when rejected
        showRejectInput(noteId) {
            this.rejectInput.noteId = noteId;
            this.rejectInput.reason = '';
            this.rejectInput.submitted = false;
        },
        approve(id) {
            if (confirm(this.$t('words.are-you-sure'))) {
                this.$inertia.post(route('back.trainees.absence-notes.approve', {id: id}));
            }
        },
        
        reject(noteId) {
            if (confirm(this.$t('words.are-you-sure'))) {
                this.$inertia.post(route('back.trainees.absence-notes.reject', { id: noteId }), {
                    reason: this.rejectInput.reason
                }).then(() => {
                    this.rejectInput.submitted = true;
                });
            }
        },
    },
}
</script>
