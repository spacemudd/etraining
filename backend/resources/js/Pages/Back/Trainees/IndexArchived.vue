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
                    {title: 'archive', link: route('back.trainees.index.archived')},
                ]"
            ></breadcrumb-container>
            <div class="flex justify-between">
                <h1 class="mb-8 font-bold text-3xl">{{ $t('words.trainees') }}</h1>
                <div class="mb-6 flex justify-between items-center">

                    <inertia-link :href="route('back.trainees.index')" class="rounded items-center justify-start mr-3 float-left px-3 py-2.5 bg-green-200 hover:bg-green-300 text-left">
                        <span> {{ $t('words.active') }} </span>
                    </inertia-link>

                    <auto-export-custom-trainees-to-excel trainees-type='archived' class="rounded items-center justify-start mr-3 float-left px-3 py-2.5 btn-gray text-left" @modal:opened="actionsDropDownView=!actionsDropDownView">
                                    <template slot="buttonContent">
                                        <button>
                                            {{ $t('words.excel') }}
                                        </button>
                                    </template>
                    </auto-export-custom-trainees-to-excel>
                    <!--<search-filter v-model="form.search" class="w-full max-w-md mr-4" @reset="reset">-->
                    <!--    <label class="block text-gray-700">Trashed:</label>-->
                    <!--    <select v-model="form.trashed" class="mt-1 w-full form-select">-->
                    <!--        <option :value="null" />-->
                    <!--        <option value="with">With Trashed</option>-->
                    <!--        <option value="only">Only Trashed</option>-->
                    <!--    </select>-->
                    <!--</search-filter>-->
                </div>
            </div>
            <div class="bg-white rounded shadow overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <tr class="text-left font-bold">
                        <th class="px-6 pt-6 pb-4">{{ $t('words.name') }}</th>
                        <th class="px-6 pt-6 pb-4">{{ $t('words.phone') }}</th>
                        <th class="px-6 pt-6 pb-4">{{ $t('words.company') }}</th>
                        <th class="px-6 pt-6 pb-4">{{ $t('words.delete-remark') }}</th>
                    </tr>
                    <tr v-for="trainees in blocked_trainees.data" :key="trainees.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
                        <td class="border-t">
                            <div class="px-6 py-4 flex items-center focus:text-indigo-500">
                                <inertia-link :href="route('back.trainees.show.blocked', trainees.id)">
                                    {{ trainees.name }}
                                    <br/>

                                    <span class="text-sm inline-block mt-2 p-1 px-2 bg-red-300 rounded-lg">
                                            {{ $t('words.blocked') }}
                                    </span>


                                </inertia-link>
                            </div>
                        </td>
                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center" :href="route('back.trainees.show.blocked', trainees.id)" tabindex="-1">
                                <div v-if="trainees.phone">
                                    {{ trainees.phone }}
                                </div>
                            </inertia-link>
                        </td>
                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center" :href="route('back.trainees.show.blocked', trainees.id)" tabindex="-1">
                                <span v-if="trainees.company">{{ trainees.company.name_ar ? trainees.company.name_ar : trainees.company.name_en }}</span>
                            </inertia-link>
                        </td>
                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center" :href="route('back.trainees.show.blocked', trainees.id)" tabindex="-1">
                                <span v-if="trainees.deleted_remark">{{ trainees.deleted_remark }}</span>
                            </inertia-link>
                        </td>
                        <td class="border-t w-px">
                            <inertia-link class="px-4 flex items-center" :href="route('back.trainees.show.blocked', trainees.id)" tabindex="-1">
                                <ion-icon name="arrow-forward-outline" class="block w-6 h-6 fill-gray-400"></ion-icon>
                            </inertia-link>
                        </td>
                    </tr>
                    <tr v-if="blocked_trainees.data.length === 0">
                        <td class="border-t px-6 py-4" colspan="4">
                            <empty-slate/>
                        </td>
                    </tr>
                </table>
            </div>
            <pagination :links="blocked_trainees.links" />
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
    import AutoExportCustomTraineesToExcel from '@/Components/AutoExportCustomTraineesToExcel';
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
            AutoExportCustomTraineesToExcel,
            DropdownMenu,
        },
        props: {
            blocked_trainees: Object,
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
