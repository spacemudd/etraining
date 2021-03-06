<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'courses', link: route('teaching.courses.index')},
                ]"
            ></breadcrumb-container>
            <div class="flex justify-between">
                <h1 class="mb-8 font-bold text-3xl">{{ $t('words.courses') }}</h1>
                <div class="mb-6 flex justify-between items-center">
                    <!--<search-filter v-model="form.search" class="w-full max-w-md mr-4" @reset="reset">-->
                    <!--    <label class="block text-gray-700">Trashed:</label>-->
                    <!--    <select v-model="form.trashed" class="mt-1 w-full form-select">-->
                    <!--        <option :value="null" />-->
                    <!--        <option value="with">With Trashed</option>-->
                    <!--        <option value="only">Only Trashed</option>-->
                    <!--    </select>-->
                    <!--</search-filter>-->
                    <inertia-link class="btn-gray" :href="route('teaching.courses.create')">
                        <span>{{ $t('words.new') }}</span>
                    </inertia-link>
                </div>
            </div>
            <div class="bg-white rounded shadow overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <tr class="text-left font-bold">
                        <th class="px-6 pt-6 pb-4">{{ $t('words.name') }}</th>
                        <th class="px-6 pt-6 pb-4">{{ $t('words.course-approval-code') }}</th>
                        <th class="px-6 pt-6 pb-4">{{ $t('words.recommended-trainees-count') }}</th>
                    </tr>
                    <tr v-for="course in courses.data" :key="course.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
                        <td class="border-t">
                            <div class="px-6 py-4 flex items-center focus:text-indigo-500">
                                <inertia-link :href="route('teaching.courses.show', course.id)">
                                    {{ course.name_ar }}
                                    <br/>
                                    <div v-if="course.is_pending_approval"
                                         class="text-sm inline-block mt-2 p-1 px-2 bg-red-300 rounded-lg">
                                        {{ $t('words.pending-approval') }}
                                    </div>
                                    <span v-if="course.is_approved" class="text-sm inline-block mt-2 p-1 px-2 bg-green-300 rounded-lg">
                                        {{ $t('words.approved') }}
                                    </span>
                                </inertia-link>
                            </div>
                        </td>
                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('teaching.courses.show', course.id)">
                                {{ course.approval_code }}
                            </inertia-link>
                        </td>
                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center" :href="route('teaching.courses.show', course.id)" tabindex="-1">
                                {{ course.classroom_count }}
                            </inertia-link>
                        </td>
                        <td class="border-t w-px">
                            <inertia-link class="px-4 flex items-center" :href="route('teaching.courses.show', course.id)" tabindex="-1">
                                <ion-icon name="arrow-forward-outline" class="block w-6 h-6 fill-gray-400"></ion-icon>
                            </inertia-link>
                        </td>
                    </tr>
                    <tr v-if="courses.data.length === 0">
                        <td class="border-t px-6 py-4" colspan="4">
                            <empty-slate>
                                <template #actions>
                                    <inertia-link class="btn-gray mt-2 block" :href="route('teaching.courses.create')">
                                        <span>{{ $t('words.new') }}</span>
                                    </inertia-link>
                                </template>
                            </empty-slate>
                        </td>
                    </tr>
                </table>
            </div>
            <pagination :links="courses.links" />
        </div>
    </app-layout>
</template>

<script>
    import mapValues from 'lodash/mapValues'
    import Pagination from '@/Shared/Pagination'
    import pickBy from 'lodash/pickBy'
    import throttle from 'lodash/throttle'
    import AppLayout from '@/Layouts/AppLayoutInstructor'
    import IconNavigate from 'vue-ionicons/dist/ios-arrow-dropright'
    import BreadcrumbContainer from "@/Components/BreadcrumbContainer";
    import EmptySlate from "@/Components/EmptySlate";

    export default {
        metaInfo: { title: 'Courses' },
        // layout: Layout,
        components: {
            EmptySlate,
            BreadcrumbContainer,
            IconNavigate,
            AppLayout,
            Pagination,
        },
        props: {
            courses: Object,
            filters: Object,
        },
        data() {
            return {
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
                    this.$inertia.replace(this.route('courses', Object.keys(query).length ? query : { remember: 'forget' }))
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
