<template>
    <app-layout>

        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'candidates', link: route('back.candidates.index')},
                ]"
            ></breadcrumb-container>
            <div class="flex justify-between">
                <h1 class="mb-8 font-bold text-3xl">{{ $t('words.candidates') }}</h1>
                <div class="mb-6 flex justify-between items-center">

                    <inertia-link class="btn-gray mx-3" :href="route('back.candidates.excel')">
                        <span>{{ $t('words.excel') }}</span>
                    </inertia-link>

                </div>
            </div>
            <div class="bg-white rounded shadow overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <tr class="text-left font-bold">
                        <th class="px-6 pt-6 pb-4">{{ $t('words.name') }}</th>
                        <th class="px-6 pt-6 pb-4">{{ $t('words.phone') }}</th>
                        <th class="px-6 pt-6 pb-4">{{ $t('words.company') }}</th>
                    </tr>
                    <tr v-for="trainees in candidates.data" :key="candidates.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
                        <td class="border-t">
                            <div class="px-6 py-4 flex items-center focus:text-indigo-500">
                                <inertia-link :href="route('back.trainees.show', candidates.id)">
                                    {{ candidates.name }}
                                    <br/>
                                    <span v-if="candidates.is_pending_uploading_files" class="text-sm inline-block mt-2 p-1 px-2 bg-blue-300 rounded-lg">
                                            {{ $t('words.incomplete-application') }}
                                        </span>

                                    <span v-if="candidates.is_pending_approval" class="text-sm inline-block mt-2 p-1 px-2 bg-yellow-200 rounded-lg">
                                            {{ $t('words.nominated-instructor') }}
                                        </span>

                                    <span v-if="candidates.is_approved" class="text-sm inline-block mt-2 p-1 px-2 bg-green-300 rounded-lg">
                                            {{ $t('words.approved') }}
                                        </span>
                                </inertia-link>
                            </div>
                        </td>
                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center" :href="route('back.trainees.show', candidates.id)" tabindex="-1">
                                <div v-if="candidates.phone">
                                    {{ candidates.phone }}
                                </div>
                            </inertia-link>
                        </td>
                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center" :href="route('back.trainees.show', candidates.id)" tabindex="-1">
                                <span v-if="candidates.company">{{ candidates.company.name_ar }}</span>
                                <span v-else class="italic text-gray-500 text-xs">{{ $t('words.not-assigned-to-a-company') }}</span>
                            </inertia-link>
                        </td>
                        <td class="border-t w-px">
                            <inertia-link class="px-4 flex items-center" :href="route('back.trainees.show', candidates.id)" tabindex="-1">
                                <ion-icon name="arrow-forward-outline" class="block w-6 h-6 fill-gray-400"></ion-icon>
                            </inertia-link>
                        </td>
                    </tr>
                    <tr v-if="candidates.data.length === 0">
                        <td class="border-t px-6 py-4" colspan="4">
                            <empty-slate/>
                        </td>
                    </tr>
                </table>
            </div>
            <pagination :links="candidates.links" />
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

    export default {
        metaInfo: { title: 'Candidates' },
        // layout: Layout,
        components: {
            EmptySlate,
            BreadcrumbContainer,
            IconNavigate,
            AppLayout,
            // Icon,
            Pagination,
            // SearchFilter,
        },
        props: {
            candidates: Object,
        },
        data() {
            return {

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
