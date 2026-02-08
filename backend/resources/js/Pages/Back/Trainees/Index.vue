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

                    <inertia-link class="btn-gray mx-3" :href="route('back.trainees.create')">
                        <span>{{ $t('words.new') }}</span>
                    </inertia-link>
                    <inertia-link class="btn-gray" :href="route('back.trainees.import')">
                        <span>{{ $t('words.new-bulk') }}</span>
                    </inertia-link>

                    <inertia-link v-can="'manage-trainee-groups'" :href="route('back.trainees.groups.index')" class="rounded items-center mr-3 justify-start float-left px-3 py-2.5 bg-yellow-200 hover:bg-yellow-300 text-left">
                        {{ $t('words.manage-trainee-groups') }}
                    </inertia-link>

                    <inertia-link :href="route('back.trainees.link-groups')" class="rounded items-center mr-3 justify-start float-left px-3 py-2.5 bg-blue-200 hover:bg-blue-300 text-left">
                        {{ $t('words.link-groups') }}
                    </inertia-link>

                    <!--<inertia-link :href="route('back.trainees.block-list.index')" class="rounded items-center mr-3 justify-start float-left px-3 py-2.5 bg-yellow-200 hover:bg-yellow-300 text-left">-->
                    <!--    {{ $t('words.blocked-list') }}-->
                    <!--</inertia-link>-->

                    <!--<inertia-link :href="route('back.trainees.index.archived')" class="rounded items-center mr-3 justify-start float-left px-3 py-2.5 bg-yellow-200 hover:bg-yellow-300 text-left">-->
                    <!--    {{ $t('words.archive') }}-->
                    <!--</inertia-link>-->

                    <!--<inertia-link :href="route('back.trainees.fixed-training-costs.index')" class="rounded items-center mr-3 justify-start float-left px-3 py-2.5 bg-yellow-200 hover:bg-yellow-300 text-left">-->
                    <!--    {{ $t('words.trainees-with-overridden-training-costs') }}-->
                    <!--</inertia-link>-->

                    <!--<dropdown-menu :isOpen="actionsDropDownView" class="dropdown-container">-->
                    <!--    <button class="btn-gray mx-3" slot="trigger">{{ $t('words.actions') }}</button>-->
                    <!--    <ul class="dropdown-items" slot="body">-->
                    <!--        <li>-->
                    <!--            <export-trainees-to-excel @modal:opened="actionsDropDownView=!actionsDropDownView">-->
                    <!--                <template slot="buttonContent">-->
                    <!--                    <button>--
                    <!--                        {{ $t('words.export') }}-->
                    <!--                    </button>-->
                    <!--                </template>-->
                    <!--            </export-trainees-to-excel>-->
                    <!--        </li>-->
                    <!--        <li>-->
                    <!--            <inertia-link :href="route('back.trainees.send-notification')">-->
                    <!--                {{ $t('words.send-messages-to-groups-of-trainees') }}-->
                    <!--            </inertia-link>-->
                    <!--        </li>-->
                    <!--    </ul>-->
                    <!--</dropdown-menu>-->

                </div>
            </div>
            <!-- Special view for sara@hadaf-hq.com -->
            <div v-if="isSaraView" class="bg-white rounded shadow overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <thead>
                        <tr class="text-left font-bold">
                            <th class="px-6 pt-6 pb-4">{{ $t('words.name') }}</th>
                            <th class="px-6 pt-6 pb-4">{{ $t('words.identity_number') }}</th>
                            <th class="px-6 pt-6 pb-4">{{ $t('words.company') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr 
                            v-for="trainee in trainees.data" 
                            :key="trainee.id" 
                            class="hover:bg-gray-100 focus-within:bg-gray-100 cursor-pointer"
                            @click="goToTrainee(trainee.id)"
                        >
                            <td class="border-t px-6 py-4">
                                {{ trainee.name }}
                            </td>
                            <td class="border-t px-6 py-4">
                                {{ trainee.identity_number || '-' }}
                            </td>
                            <td class="border-t px-6 py-4">
                                <span v-if="trainee.company">{{ trainee.company.name_ar }}</span>
                                <span v-else class="italic text-gray-500 text-xs">{{ $t('words.not-assigned-to-a-company') }}</span>
                            </td>
                        </tr>
                        <tr v-if="trainees.data.length === 0">
                            <td class="border-t px-6 py-4" colspan="3">
                                <empty-slate/>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <pagination :links="trainees.links" />
            </div>

            <!-- Normal view for other users -->
            <admin-searchbar v-if="!isSaraView" />
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
            AdminSearchbar,
        },
        props: {
            trainees: Object,
            filters: Object,
            isSaraView: {
                type: Boolean,
                default: false,
            },
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
            goToTrainee(traineeId) {
                this.$inertia.visit(this.route('back.trainees.show', traineeId));
            },
        },
    }
</script>
