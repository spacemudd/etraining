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
                ]"
            ></breadcrumb-container>
            <div class="flex justify-between">
                <h1 class="mb-8 font-bold text-3xl">{{ $t('words.trainees') }}</h1>
                <div class="mb-6 flex justify-between items-center">

                    <!--<search-filter v-model="form.search" class="w-full max-w-md mr-4" @reset="reset">-->
                    <!--    <label class="block text-gray-700">Trashed:</label>-->
                    <!--    <select v-model="form.trashed" class="mt-1 w-full form-select">-->
                    <!--        <option :value="null" />-->
                    <!--        <option value="with">With Trashed</option>-->
                    <!--        <option value="only">Only Trashed</option>-->
                    <!--    </select>-->
                    <!--</search-filter>-->
                    <inertia-link class="btn-gray mx-3" :href="route('back.trainees.create')">
                        <span>{{ $t('words.new') }}</span>
                    </inertia-link>
                    <inertia-link class="btn-gray" :href="route('back.trainees.import')">
                        <span>{{ $t('words.new-bulk') }}</span>
                    </inertia-link>

                    <inertia-link :href="route('back.trainees.block-list.index')" class="rounded items-center mr-3 justify-start float-left px-3 py-2.5 bg-yellow-200 hover:bg-yellow-300 text-left">
                        {{ $t('words.blocked-list') }}
                    </inertia-link>

                    <inertia-link :href="route('back.trainees.index.archived')" class="rounded items-center mr-3 justify-start float-left px-3 py-2.5 bg-yellow-200 hover:bg-yellow-300 text-left">
                        {{ $t('words.archive') }}
                    </inertia-link>

                    <dropdown-menu :isOpen="actionsDropDownView" class="dropdown-container">
                        <button class="btn-gray mx-3" slot="trigger">{{ $t('words.actions') }}</button>
                        <ul class="dropdown-items" slot="body">
                            <li>
                                <export-trainees-to-excel @modal:opened="actionsDropDownView=!actionsDropDownView">
                                    <template slot="buttonContent">
                                        <button>
                                            {{ $t('words.export') }}
                                        </button>
                                    </template>
                                </export-trainees-to-excel>
                            </li>
                            <li>
                                <inertia-link :href="route('back.trainees.send-notification')">
                                    {{ $t('words.send-messages-to-groups-of-trainees') }}
                                </inertia-link>
                            </li>
                            <!--<li v-for="i in 6" :key="i"><a href="">Item {{i}}</a></li>-->
                        </ul>
                    </dropdown-menu>
                </div>
            </div>
            <div class="bg-white rounded shadow overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <tr class="text-left font-bold">
                        <th class="px-6 pt-6 pb-4">{{ $t('words.name') }}</th>
                        <th class="px-6 pt-6 pb-4">{{ $t('words.phone') }}</th>
                        <th class="px-6 pt-6 pb-4">{{ $t('words.company') }}</th>
                    </tr>
                    <tr v-for="trainees in trainees.data" :key="trainees.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
                        <td class="border-t">
                            <div class="px-6 py-4 flex items-center focus:text-indigo-500">
                                <inertia-link :href="route('back.trainees.show', trainees.id)">
                                    {{ trainees.name }}
                                    <br/>
                                    <span v-if="trainees.is_pending_uploading_files" class="text-sm inline-block mt-2 p-1 px-2 bg-blue-300 rounded-lg">
                                            {{ $t('words.incomplete-application') }}
                                        </span>

                                    <span v-if="trainees.is_pending_approval" class="text-sm inline-block mt-2 p-1 px-2 bg-yellow-200 rounded-lg">
                                            {{ $t('words.nominated-instructor') }}
                                        </span>

                                    <span v-if="trainees.is_approved" class="text-sm inline-block mt-2 p-1 px-2 bg-green-300 rounded-lg">
                                            {{ $t('words.approved') }}
                                        </span>
                                </inertia-link>
                            </div>
                        </td>
                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center" :href="route('back.trainees.show', trainees.id)" tabindex="-1">
                                <div v-if="trainees.phone">
                                    {{ trainees.phone }}
                                </div>
                            </inertia-link>
                        </td>
                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center" :href="route('back.trainees.show', trainees.id)" tabindex="-1">
                                <span v-if="trainees.company">{{ trainees.company.name_ar }}</span>
                                <span v-else class="italic text-gray-500 text-xs">{{ $t('words.not-assigned-to-a-company') }}</span>
                            </inertia-link>
                            <p class="px-6 flex items-center text-xs text-gray-500"
                               style=""
                               v-if="trainees.trainee_group">
                                {{ trainees.trainee_group.name }}
                            </p>
                        </td>
                        <td class="border-t w-px">
                            <inertia-link class="px-4 flex items-center" :href="route('back.trainees.show', trainees.id)" tabindex="-1">
                                <ion-icon name="arrow-forward-outline" class="block w-6 h-6 fill-gray-400"></ion-icon>
                            </inertia-link>
                        </td>
                    </tr>
                    <tr v-if="trainees.data.length === 0">
                        <td class="border-t px-6 py-4" colspan="4">
                            <empty-slate/>
                        </td>
                    </tr>
                </table>
            </div>
            <pagination :links="trainees.links" />
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

    export default {
        metaInfo: { title: 'Trainees' },
        // layout: Layout,
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
        },
        props: {
            trainees: Object,
            filters: Object,
        },
        data() {
            return {
                actionsDropDownView: false,
                form: {
                    // search: this.filters.search,
                    // trashed: this.filters.trashed,
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
        methods: {
            reset() {
                this.form = mapValues(this.form, () => null)
            },
        },
    }
</script>
