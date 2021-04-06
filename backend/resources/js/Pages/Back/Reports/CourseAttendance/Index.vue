<!--
  - Copyright (c) 2020 - Clarastars, LLC  - All Rights Reserved.
  -
  - Unauthorized copying of this file via any medium is strictly prohibited.
  - This file is a proprietary of Clarastars LLC and is confidential / educational purpose only.
  -
  - https://clarastars.com - info@clarastars.com
  - @author Shafiq al-Shaar <shafiqalshaar@gmail.com>
  -->

<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'reports', link: route('back.reports.index')},
                    {title: 'course-attendances', link: route('back.reports.course-attendances.index')},
                ]"
            ></breadcrumb-container>

            <form method="post" :action="route('back.reports.course-attendances.generate')" target="_blank">
                <input type="hidden" name="_token" :value="token">

                <div class="grid grid-cols-6 gap-6">
                    <div class="col-span-3 sm:col-span-2 mt-5">
                        <jet-label class="mb-2" for="course_id" :value="$t('words.course')" />
                        <div class="relative">
                            <select class="block appearance-none w-full border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                    v-model="form.course_id"
                                    name="course_id"
                                    required
                                    id="course_id">
                                <option v-for="course in courses" :key="course.id" :value="course.id">
                                    {{ course.name_ar }} <template v-if="course.instructor">- {{ course.instructor.name }}</template>
                                </option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-span-3 sm:col-span-2 mt-5">
                    <div class="col-span-3 sm:col-span-2 mt-5">
                        <input name="date_from" type="date" v-model="form.date_from" class="form-input rounded-md shadow-sm" required>
                    </div>
                </div>


                <input name="date_to" type="date" v-model="form.date_to" required>
                <button type="submit">{{ $t('words.submit') }}</button>
            </form>

        </div>
    </app-layout>
</template>

<script>
    import JetLabel from '@/Jetstream/Label';
    import AppLayout from '@/Layouts/AppLayout'
    import IconNavigate from 'vue-ionicons/dist/ios-arrow-dropright'
    import BreadcrumbContainer from "@/Components/BreadcrumbContainer";

    export default {
        props: [
            'courses',
        ],
        metaInfo() {
            return {
                title: this.$t('words.course-attendances')
            }
        },
        components: {
            IconNavigate,
            AppLayout,
            JetLabel,
            BreadcrumbContainer,
        },
        computed: {
            token() {
                return document.head.querySelector('meta[name="csrf-token"]').content;
            }
        },
        mounted() {
            if (this.courses.length) {
                this.form.course_id = this.courses[0].id;
            }
        },
        data() {
            return {
                form: this.$inertia.form({
                    course_id: null,
                    date_from: new Date().toISOString().substring(0, 10),
                    date_to: new Date().toISOString().substring(0, 10),
                }, {
                    bag: 'createAttendanceReport',
                })
            }
        },
        methods: {
            sendForm() {
                this.form.post(route('back.reports.course-attendances.generate'));
            },
        },
    }
</script>
