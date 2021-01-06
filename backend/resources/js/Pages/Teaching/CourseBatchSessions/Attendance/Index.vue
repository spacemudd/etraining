<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'courses', link: route('teaching.courses.index')},
                    {title_raw: course_batch_session.course.name_ar},
                    {title_raw: course_batch_session.starts_at},
                    {title_raw: $t('words.attendance')},
                ]"
            ></breadcrumb-container>


            <template v-if="course_batch_session.attendances.length === 0">
                <div class="bg-white rounded shadow overflow-x-auto">

                    <div class="p-5">
                        <table class="w-full whitespace-no-wrap">
                            <colgroup>
                                <col style="width:50%;">
                                <col>
                            </colgroup>
                            <thead>
                                <tr class="text-left font-bold">
                                    <th class="px-6 pt-6 pb-4">{{ $t('words.name') }}</th>
                                    <th class="px-6 pt-6 pb-4">{{ $t('words.attendance') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-t" v-for="(trainee, key) in form.trainees">
                                    <td class="px-6 py-4">{{ trainee.name }}</td>
                                    <td class="px-6 py-4">
                                        <button style="width:100px;height:40px;" v-if="trainee.physical_attendance" class="bg-blue-600 p-2 rounded-lg text-white inline-block" @click.prevent="setTraineeAbsent(trainee, key)">{{ $t('words.present') }}</button>
                                        <button style="width:100px;height:40px;" v-else class="border-2 inline-block border-red-500 p-2 rounded-lg" @click.prevent="setTraineePresent(trainee, key)">{{ $t('words.absent') }}</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-white rounded shadow overflow-x-auto mt-5 p-5">
                <div class="flex">
                    <ion-icon name="alert-circle-outline" class="w-5 h-5" style="margin-top:3px;"></ion-icon>
                    <p class="mx-2">{{ $t('words.submit-attendance-sheet-information') }}.</p>
                </div>

                <div class="flex mt-5">
                    <inertia-link :href="route('teaching.courses.show', course_batch_session.course_id)" class="flex items-center justify-start rounded-md px-4 py-2 hover:bg-gray-300 text-right">
                        {{ $t('words.cancel') }}
                    </inertia-link>
                    <button @click.prevent="submitAttendanceSheet" class="flex items-center justify-start rounded-md px-4 py-2 bg-yellow-200 hover:bg-yellow-300 text-right mx-5">
                        {{ $t('words.submit-attendance-sheet') }}
                    </button>
                </div>
            </div>
            </template>
            <template v-else>
                <div class="bg-white rounded shadow overflow-x-auto">
                    <div class="p-5">
                        <table class="w-full whitespace-no-wrap">
                            <colgroup>
                                <col style="width:50%;">
                                <col>
                            </colgroup>
                            <thead>
                                <tr class="text-left font-bold">
                                    <th class="px-6 pt-6 pb-4">{{ $t('words.name') }}</th>
                                    <th class="px-6 pt-6 pb-4">{{ $t('words.attendance') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                            <tr class="border-t" v-for="attendance in course_batch_session.attendances">
                                <td class="px-6 py-4">{{ attendance.trainee.name }}</td>
                                <td class="px-6 py-4">
                                    <div style="width:100px;height:40px;" v-if="attendance.physical_attendance" class="text-center bg-blue-600 p-2 rounded-lg text-white inline-block">{{ $t('words.present') }}</div>
                                    <div style="width:100px;height:40px;" v-else class="text-center border-2 inline-block border-red-500 p-2 rounded-lg">{{ $t('words.absent') }}</div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </template>

        </div>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayoutInstructor'
    import IconNavigate from 'vue-ionicons/dist/ios-arrow-dropright'
    import BreadcrumbContainer from "@/Components/BreadcrumbContainer";

    export default {
        metaInfo: { title: 'Attendance' },
        components: {
            BreadcrumbContainer,
            IconNavigate,
            AppLayout,
        },
        props: ['course_batch_session'],
        mounted() {
            // this.form.trainees = this.course_batch_session.course_batch.trainee_group.trainees;
            this.course_batch_session.course_batch.trainee_group.trainees.forEach((trainee) => {
                trainee.physical_attendance = true;
                this.form.trainees.push(trainee);
            })
            this.form.course_batch_session_id = this.course_batch_session.id;
        },
        data() {
            return {
                form: this.$inertia.form({
                    course_batch_session_id: null,
                    trainees: [],
                }),
            }
        },
        methods: {
            setTraineePresent(trainee, key) {
                trainee.physical_attendance = true;
                this.$set(this.form.trainees, key, trainee);
            },
            setTraineeAbsent(trainee, key) {
                trainee.physical_attendance = false;
                this.$set(this.form.trainees, key, trainee);
            },
            submitAttendanceSheet() {
                this.form.post(
                    route('teaching.course-batch-sessions.attendance.store', {course_batch_session_id: this.course_batch_session_id}));
            },
        },
    }
</script>
