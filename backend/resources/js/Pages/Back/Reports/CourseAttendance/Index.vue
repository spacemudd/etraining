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
                <select v-model="form.course_id">
                    <option v-for="course in courses" :key="course.id" :value="course.id">
                        {{ course.name_ar }} <template v-if="course.instructor">- {{ course.instructor.name }}</template>
                    </option>
                </select>
                <input type="date" v-model="form.date_from">
                <input type="date" v-model="form.date_to">
                <button type="submit">Submit</button>
            </form>

        </div>
    </app-layout>
</template>

<script>
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
            BreadcrumbContainer,
        },
        computed: {
            token() {
                return document.head.querySelector('meta[name="csrf-token"]').content;
            }
        },
        data() {
            return {
                form: this.$inertia.form({
                    course_id: null,
                    date_from: new Date(),
                    date_to: new Date(),
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
