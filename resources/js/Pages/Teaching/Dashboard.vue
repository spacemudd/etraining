<template>
    <app-layout>
        <div class="container px-6 mx-auto grid">
            <!--<h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">-->
            <!--    {{ $t('words.dashboard') }}-->
            <!--</h2>-->
            <!-- Cards -->

            <!-- Quick actions actions -->
            <h2 class="my-6 font-semibold text-gray-700 dark:text-gray-200 border-b pb-1">
                {{ $t('words.courses') }}
            </h2>

            <div class="container px-6 mx-auto grid pt-6">
                <div v-for="course in courses.data" :key="course.id">
                    <div class="bg-white py-5">
                        <div class="w-30">
                            <div class="w-full h-full bg-red rounded-lg">img</div>
                        </div>

                        <div>
                            <p>{{ course.name_ar }}</p>
                            <p>{{ $t('words.provided-by') }}:</p>
                            <div>
                                <div v-for="batch in course.batches" :key="batch.id">
                                    <div v-for="session in batch.course_batch_sessions" :key="session.id">
                                        <inertia-link :href="`/back/courses/${session.course_id}/course-batches/${session.course_batch_id}/course-batch-sessions/${session.id}`"
                                                      class="bg-red-500 rounded p-2 text-white">{{ $t('words.start-broadcasting') }}
                                        </inertia-link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container px-6 mx-auto grid pt-6">
                <div class="bg-white rounded shadow overflow-x-auto">
                    <table class="w-full whitespace-no-wrap">
                        <tr class="text-left font-bold">
                            <th class="px-6 pt-6 pb-4">{{ $t('words.name') }}</th>
                            <th class="px-6 pt-6 pb-4">{{ $t('words.course-approval-code') }}</th>
                            <th class="px-6 pt-6 pb-4">{{ $t('words.instructor') }}</th>
                        </tr>
                        <tr v-for="course in courses.data" :key="course.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
                            <td class="border-t">
                                <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('teaching.courses.show', course.id)">
                                    {{ course.name_ar }}
                                </inertia-link>
                            </td>
                            <td class="border-t">
                                <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('teaching.courses.show', course.id)">
                                    {{ course.approval_code }}
                                </inertia-link>
                            </td>
                            <td class="border-t">
                                <inertia-link class="px-6 py-4 flex items-center" :href="route('teaching.courses.show', course.id)" tabindex="-1">
                                    <div v-if="course.instructor">
                                        {{ course.instructor.name }}
                                    </div>
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

        </div>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayoutInstructor'
    import Welcome from '@/Jetstream/Welcome'
    import LanguageSelector from "@/Shared/LanguageSelector";
    import HeaderCard from "@/Components/HeaderCard";
    import CoursesPagination from "@/Components/CoursesPagination";
    import Pagination from "@/Shared/Pagination";

    export default {
        props: ['courses'],
        components: {
            CoursesPagination,
            AppLayout,
            Welcome,
            LanguageSelector,
            HeaderCard,
            Pagination,
        },
    }
</script>
