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

                </div>
            </div>
            <div class="bg-white rounded shadow overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <tr class="text-left font-bold">
                        <th class="px-6 pt-6 pb-4">{{ $t('words.status') }}</th>
                        <th class="px-6 pt-6 pb-4">{{ $t('words.name') }}</th>
                        <th class="px-6 pt-6 pb-4">{{ $t('words.course-approval-code') }}</th>
                        <th class="px-6 pt-6 pb-4">{{ $t('words.instructor') }}</th>
                        <th class="px-6 pt-6 pb-4">{{ $t('words.review-by-instructor') }}</th>
                    </tr>
                    <tr v-for="course in courses.data" :key="course.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
                        <td class="border-t pr-4 py-6">
                            <div v-if="course.is_pending_approval"
                                 class="text-sm inline-block mt-2 p-1 px-2 bg-red-300 rounded-lg">
                                {{ $t('words.pending-approval') }}
                            </div>

                            <span v-else-if="course.is_approved" class="text-sm inline-block mt-2 p-1 px-2 bg-green-300 rounded-lg">
                                        {{ $t('words.approved') }}
                            </span>

                            <span v-if="course.closest_course_batch == 'empty'" class="text-sm inline-block mt-2 p-1 px-2 bg-red-400 rounded-lg">
                                        {{ $t('words.not-set') }}
                            </span>

                            <span v-else-if="course.closest_course_batch == 'Today'" class="text-sm inline-block mt-2 p-1 px-2 bg-yellow-400 rounded-lg">
                                        {{ $t('words.today') }}
                            </span>

                            <span v-else-if="course.closest_course_batch < currentDate && course.closest_course_batch != 'empty'" class="text-sm inline-block mt-2 p-1 px-2 bg-blue-300 rounded-lg">
                                        {{ course.closest_course_batch }}
                            </span>

                            <span v-else-if="course.closest_course_batch > currentDate" class="text-sm inline-block mt-2 p-1 px-2 bg-green-500 rounded-lg">
                                        {{ course.closest_course_batch }}
                            </span>
                        </td>
                    <td class="border-t">
                        <div class="px-6 py-4 flex items-center focus:text-indigo-500">
                            <inertia-link :href="route('teaching.courses.show', course.id)" class="text-blue-600 hover:underline">
                                {{ course.name_ar }}
                            </inertia-link>
                        </div>
                    </td>
                        
                        <td class="border-t">
                            <div class="px-6 py-4 flex items-center focus:text-indigo-500">
                                {{ course.approval_code }}</div>
                            </td>
                        <td class="border-t">
                            <div v-if="course.instructor">
                                {{ course.instructor.name }}
                            </div>
                        </td>
                        <td class="border-t">
                            <div v-if="course.instructor">
                                <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest focus:outline-none focus:shadow-outline-gray transition ease-in-out duration-150 disabled:cursor-not-allowed mx-2 bg-gray-500 hover:bg-gray-600 active:bg-gray-700 foucs:bg-gray-700">
                                    <a class="a-9" href="https://ptc-ksa.net/qsm_quiz/%d9%82%d9%8a%d8%a7%d8%b3-%d8%b1%d8%b6%d8%a7-%d8%a7%d9%84%d9%85%d8%af%d8%b1%d8%a8/" >{{ $t('words.course-review') }}</a>
                                </button>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
            <pagination :links="courses.links" />
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
        // Icon,
        Pagination,
        // SearchFilter,
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
            currentDate: '',
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
    mounted() {

        const today = new Date();

        function formatDate(date, format) {

            var newFormat = format.replace('mm', date.getMonth() + 1)
                .replace('yy', date.getFullYear())
                .replace('dd', date.getDate());

            return newFormat;
        }

        this.currentDate = formatDate(today, 'yy-mm-dd');
    }
}
</script>
