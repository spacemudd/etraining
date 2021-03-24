<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'courses', link: route('teaching.courses.index')},
                    {title_raw: course_batch_session.course.name_ar, link: route('teaching.courses.show', course_batch_session.course_id)},
                    {title_raw: course_batch_session.starts_at_timezone},
                    {title_raw: $t('words.attendance')},
                ]"
            ></breadcrumb-container>


            <template>
                <div class="bg-white rounded shadow overflow-x-auto">

                    <a :href="route('teaching.course-batch-sessions.attendance.export', {course_batch_session_id: course_batch_session.id})">
                    <button class="  mt-5 ml-5 mr-5 rounded-md px-4 py-2 bg-green-300 hover:bg-green-400 ">
                        <img src="/img/excel.svg" class="float"> {{ $t('words.excel') }}
                    </button>
                    </a>

                    <div class="p-5">
                        <table class="w-full whitespace-no-wrap">
                            <colgroup>
                                <col style="width:400px;">
                                <col>
                                <col style="width:300px;">
                            </colgroup>
                            <thead>
                                <tr class="text-left font-bold">
                                    <th class="px-6 pt-6 pb-4">{{ $t('words.name') }}</th>
                                    <th class="px-6 pt-6 pb-4">{{ $t('words.attendance') }}</th>
                                    <th class="px-6 pt-6 pb-4">{{ $t('words.absence-reason') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-t" v-for="(trainee, key) in form.trainees">
                                    <td class="px-6 py-4">{{ trainee.name }}</td>
                                    <td class="px-6 py-4">
                                        <button style="width:130px;height:40px;"
                                                :class="{'bg-blue-600 border-blue-600 text-white': trainee.status === 'absent'}"
                                                class="border-2 inline-block border-red-500 p-2 rounded-lg"
                                                @click.prevent="setTraineeAbsent(trainee, key)">{{ $t('words.absent') }}</button>
                                        <button style="width:130px;height:40px;"
                                                :class="{'bg-blue-600 border-blue-600 text-white': trainee.status === 'absent_forgiven'}"
                                                class="border-2 inline-block border-red-500 p-2 rounded-lg"
                                                @click.prevent="setTraineeAbsentWihExcuse(trainee, key)">{{ $t('words.absent-with-excuse') }}</button>
                                        <button style="width:130px;height:40px;"
                                                :class="{'bg-blue-600 border-blue-600 text-white': trainee.status === 'present_late'}"
                                                class="border-2 inline-block border-red-500 p-2 rounded-lg"
                                                @click.prevent="setTraineePresentLate(trainee, key)">{{ $t('words.present-but-late') }}</button>
                                        <button style="width:130px;height:40px;"
                                                :class="{'bg-blue-600 border-blue-600 text-white': trainee.status === 'present'}"
                                                class="border-2 inline-block border-red-500 p-2 rounded-lg"
                                                @click.prevent="setTraineePresent(trainee, key)">{{ $t('words.present') }}</button>
                                    </td>
                                    <td class="px-6 py-4">
                                        <input class="input"
                                               v-if="trainee.status==='absent_forgiven'"
                                               v-model="trainee.absence_reason"
                                               type="text"
                                               name="fileRequestTitleAr"
                                               :placeholder="$t('words.absence-reason')"
                                               required>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-white rounded shadow overflow-x-auto mt-5 p-5">
                <div class="flex mt-5">
                    <inertia-link :href="route('teaching.courses.show', course_batch_session.course_id)" class="flex items-center justify-start rounded-md px-4 py-2 hover:bg-gray-300 text-right">
                        {{ $t('words.cancel') }}
                    </inertia-link>
                    <button @click.prevent="submitAttendanceSheet"
                            :class="{'opacity-50': $wait.is('SUBMITTING_ATTENDANCE')}"
                            class="flex items-center justify-start rounded-md px-4 py-2 bg-yellow-200 hover:bg-yellow-300 text-right mx-5">
                        <btn-loading-indicator v-if="$wait.is('SUBMITTING_ATTENDANCE')" />
                        {{ $t('words.submit') }}
                    </button>
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
    import BtnLoadingIndicator from "@/Components/BtnLoadingIndicator";

    export default {
        metaInfo: { title: 'Attendance' },
        components: {
            BreadcrumbContainer,
            IconNavigate,
            AppLayout,
            BtnLoadingIndicator,
        },
        props: ['course_batch_session'],
        mounted() {
            this.$wait.end('SUBMITTING_ATTENDANCE');
            this.course_batch_session.course_batch.trainee_group.trainees.forEach((trainee) => {
                if (trainee.has_attended) {
                    trainee.status = 'present';
                } else {
                    trainee.status = 'absent';
                }
                trainee.absence_reason = '';
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
                trainee.status = 'present';
                this.$set(this.form.trainees, key, trainee);
            },
            setTraineePresentLate(trainee, key) {
                trainee.status = 'present_late';
                this.$set(this.form.trainees, key, trainee);
            },
            setTraineeAbsent(trainee, key) {
                trainee.status = 'absent';
                this.$set(this.form.trainees, key, trainee);
            },
            setTraineeAbsentWihExcuse(trainee, key) {
                trainee.status = 'absent_forgiven';
            },
            submitAttendanceSheet() {
                this.$wait.start('SUBMITTING_ATTENDANCE');
                this.form.post(
                    route('teaching.course-batch-sessions.attendance.store', {course_batch_session_id: this.course_batch_session_id})
                );
            },
        },
    }
</script>
