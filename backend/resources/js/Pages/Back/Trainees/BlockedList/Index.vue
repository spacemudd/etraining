<style lang="css" xmlns="http://www.w3.org/1999/html">
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
                    {title: 'blocked-list'},
                ]"
            ></breadcrumb-container>
            <div class="flex justify-between">
                <h1 class="mb-8 font-bold text-3xl">{{ $t('words.blocked-list') }}</h1>
                <div class="mb-6 flex justify-between items-center">
                    <inertia-link class="btn-gray mx-3" :href="route('back.trainees.block-list.create')">
                        <span>{{ $t('words.new') }}</span>
                    </inertia-link>
                    <inertia-link class="btn-gray" :href="route('back.trainees.block-list.import')">
                        <span>{{ $t('words.new-bulk') }}</span>
                    </inertia-link>

                    <dropdown-menu :isOpen="actionsDropDownView" class="dropdown-container">
                        <button class="btn-gray mx-3" slot="trigger">{{ $t('words.actions') }}</button>
                        <ul class="dropdown-items" slot="body">
                            <li>
                                <a target="_blank" :href="route('back.trainees.block-list.download')">
                                    {{ $t('words.download') }}
                                </a>
                            </li>
                        </ul>
                    </dropdown-menu>
                </div>
            </div>
            <div class="bg-white rounded shadow overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <tr class="text-left font-bold">
                        <th class="px-6 pt-6 pb-4">{{ $t('words.name') }}</th>
                        <th class="px-6 pt-6 pb-4">{{ $t('words.phone') }}</th>
                        <th class="px-6 pt-6 pb-4">{{ $t('words.reason') }}</th>
                    </tr>
                    <tr v-for="trainees in blocked_list.data" :key="trainees.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
                        <td class="border-t">
                            <div class="px-6 py-4 flex items-center focus:text-indigo-500">
                                <div v-if="trainees.trainee_id">
                                    <inertia-link :href="route('back.trainees.show.blocked', trainees.trainee_id)">
                                        {{ trainees.name }}
                                        <br/>
                                        <span v-if="trainees.identity_number" class="text-sm inline-block text-gray-800">
                                            {{ trainees.identity_number }}
                                        </span>
                                        <br/>
                                        <span v-if="trainees.email" class="text-sm inline-block text-gray-800">
                                            {{ trainees.email }}
                                        </span>
                                    </inertia-link>
                                </div>
                                <div v-else>
                                    {{ trainees.name }}
                                    <br/>
                                    <span v-if="trainees.identity_number" class="text-sm inline-block text-gray-800">
                                        {{ trainees.identity_number }}
                                    </span>
                                    <br/>
                                    <span v-if="trainees.email" class="text-sm inline-block text-gray-800">
                                        {{ trainees.email }}
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td class="border-t">
                            <div class="px-6 py-4 flex items-center">
                                <!--<inertia-link :href="route('back.trainees.show', trainees.id)" tabindex="-1">-->
                                <div>
                                    {{ trainees.phone }}
                                    <br/>
                                    <span v-if="trainees.phone_additional" class="text-sm inline-block text-gray-800">
                                        {{ trainees.phone_additional }}
                                    </span>
                                </div>
                                <!--</inertia-link>-->
                            </div>
                        </td>
                        <td class="border-t">
                            <div class="px-6 py-4 flex items-center">
                                <div v-if="trainees.reason">
                                    {{ trainees.reason }}
                                </div>
                            </div>
                        </td>
                        <td class="border-t w-px">
                            <button @click="deleteBlockRecord(trainees.id)" class="px-4 flex items-center cursor-pointer">
                                <!--<ion-icon name="arrow-forward-outline" class="block w-6 h-6 fill-gray-400"></ion-icon>-->
                                <ion-icon name="trash-bin-outline" class="block w-6 h-6 fill-red-400"></ion-icon>
                            </button>
                        </td>
                    </tr>
                    <tr v-if="blocked_list.data.length === 0">
                        <td class="border-t px-6 py-4" colspan="4">
                            <empty-slate/>
                        </td>
                    </tr>
                </table>
            </div>
            <pagination :links="blocked_list.links" />
        </div>
    </app-layout>
</template>

<script>
    import mapValues from 'lodash/mapValues'
    import Pagination from '@/Shared/Pagination'
    import pickBy from 'lodash/pickBy'
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
        components: {
            EmptySlate,
            BreadcrumbContainer,
            IconNavigate,
            AppLayout,
            Pagination,
            ExportTraineesToExcel,
            DropdownMenu,
        },
        props: {
            trainees: Object,
            filters: Object,
            blocked_list: Object,
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
            deleteBlockRecord(block_list_id) {
                if (confirm(this.$t('words.are-you-sure'))) {
                    axios.delete(route('back.trainees.block-list.delete', {id: block_list_id}))
                        .then(response => {
                            this.$inertia.get(route('back.trainees.block-list.index'));
                        })
                }
            }
        },
    }
</script>
