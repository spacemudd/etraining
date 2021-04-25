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

            <template v-if="!attendance_snapshots_report.is_ready_for_review">
                loading
            </template>
            <template v-else>
                <div class="bg-white rounded shadow overflow-x-auto mt-5 p-5">

                    <p>{{ $t('words.status') }}: {{ attendance_snapshots_report.status_name }}</p>

                    <template v-if="attendances">
                        <p>{{ attendances.data.length }}</p>
                    </template>

                    <div class="p-5" v-if="attendances">
                        <table class="w-full whitespace-no-wrap">
                            <colgroup>
                                <col style="width:400px;">
                                <col>
                                <col style="width:250px;">
                            </colgroup>
                            <thead>
                            <tr class="text-left font-bold">
                                <th class="px-6 pt-6 pb-4">{{ $t('words.name') }}</th>
                                <th class="px-6 pt-6 pb-4">{{ $t('words.attendance') }}</th>
                                <th class="px-6 pt-6 pb-4">{{ $t('words.absence-reason') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr class="border-t" v-for="(attendance, key) in attendances.data" :key="key">
                                <td class="px-6 py-4">{{ attendance.trainee.name }}</td>
                                <td class="px-6 py-4">
                                    <button style="width:95px;height:32px;font-size:12px;"
                                            :class="{'bg-blue-600 border-blue-600 text-white': attendance.attendance_status === 'absent'}"
                                            class="border-2 inline-block border-red-500 rounded-lg text-center"
                                            @click.prevent="setTraineeAbsent(attendance, key)">
                                        <div class="flex justify-center align-middle">
                                            <btn-loading-indicator v-if="$wait.is('ATTENDANCE_ABSENT_'+attendance.id)" />
                                            {{ $t('words.absent') }}
                                        </div>
                                    </button>
                                    <button style="width:95px;height:32px;font-size:12px;"
                                            :class="{'bg-blue-600 border-blue-600 text-white': attendance.attendance_status === 'absent_forgiven'}"
                                            class="border-2 inline-block border-red-500 rounded-lg"
                                            @click.prevent="setTraineeAbsentWihExcuse(attendance, key)">
                                        <div class="flex justify-center align-middle">
                                            <btn-loading-indicator v-if="$wait.is('ATTENDANCE_ABSENT_WITH_EXCUSE_'+attendance.id)" />
                                            {{ $t('words.absent-with-excuse') }}
                                        </div>
                                    </button>
                                    <button style="width:95px;height:32px;font-size:12px;"
                                            :class="{'bg-blue-600 border-blue-600 text-white': attendance.attendance_status === 'present_late'}"
                                            class="border-2 inline-block border-red-500 rounded-lg"
                                            @click.prevent="setTraineePresentLate(attendance, key)">
                                        <div class="flex justify-center align-middle">
                                            <btn-loading-indicator v-if="$wait.is('ATTENDANCE_PRESENT_LATE_'+attendance.id)" />
                                            {{ $t('words.present-but-late') }}
                                        </div>
                                    </button>
                                    <button style="width:95px;height:32px;font-size:12px;"
                                            :class="{'bg-blue-600 border-blue-600 text-white': attendance.attendance_status === 'present'}"
                                            class="border-2 inline-block border-red-500 rounded-lg"
                                            @click.prevent="setTraineePresent(attendance, key)">
                                        <div class="flex justify-center align-middle">
                                            <btn-loading-indicator v-if="$wait.is('ATTENDANCE_PRESENT_'+attendance.id)" />
                                            {{ $t('words.present') }}
                                        </div>
                                    </button>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex" v-if="attendance.attendance_status==='absent_forgiven'">
                                        <input class="input"
                                               v-model="attendance.absence_reason"
                                               type="text"
                                               name="fileRequestTitleAr"
                                               :placeholder="$t('words.absence-reason')"
                                               required>
                                        <button @click.prevent="saveAbsenceReason(attendance, key)"
                                                :class="{'opacity-50': $wait.is('SUBMITTING_ATTENDANCE')}"
                                                class="flex items-center justify-start rounded-md px-4 py-2 bg-yellow-200 hover:bg-yellow-300 text-right mx-5">
                                            <btn-loading-indicator v-if="$wait.is('ATTENDANCE_ABSENCE_REASON_'+attendance.id)" />
                                            {{ $t('words.save') }}
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <pagination :links="attendances.links" />
                    </div>

                    <div class="flex mt-5">
                        <inertia-link :href="route('teaching.courses.show', course_batch_session.course_id)" class="flex items-center justify-start rounded-md px-4 py-2 hover:bg-gray-300 text-right">
                            {{ $t('words.cancel') }}
                        </inertia-link>
                        <button @click.prevent="submitAttendanceSheet"
                                :class="{'opacity-50': $wait.is('SUBMITTING_ATTENDANCE')}"
                                class="flex items-center justify-start rounded-md px-4 py-2 bg-yellow-200 hover:bg-yellow-300 text-right mx-5">
                            <btn-loading-indicator v-if="$wait.is('SUBMITTING_ATTENDANCE')" />
                            {{ $t('words.approve') }}
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
import Pagination from '@/Shared/Pagination'
import {Inertia} from '@inertiajs/inertia';

export default {
    metaInfo: { title: 'Attendance' },
    components: {
        BreadcrumbContainer,
        IconNavigate,
        AppLayout,
        BtnLoadingIndicator,
        Pagination,
    },
    props: [
        'course_batch_session',
        'attendance_snapshots_report_prop',
        'attendances_prop',
    ],
    data() {
        return {
            loaded: false,
            form: this.$inertia.form({
                course_batch_session_id: null,
            }),
            attendance_snapshots_report: this.attendance_snapshots_report_prop,
            attendances: null,
        }
    },
    mounted() {
        this.attendance_snapshots_report = this.attendance_snapshots_report_prop;
        this.form.course_batch_session_id = this.course_batch_session.id;
        this.attendances = this.attendances_prop;

        if (!this.attendance_snapshots_report.is_ready_for_review) {
            this.checkReportStatus();
        }

    },
    methods: {
        checkReportStatus() {
            let vm = this;
            axios.get(route('teaching.course-batch-sessions.attendance-snapshots-reports.show-report', {attendance_report_id: this.attendance_snapshots_report_prop.id}))
            .then(response => {
                if (!response.data.is_ready_for_review) {
                    setTimeout(function() {
                        vm.checkReportStatus();
                    }, 2000);
                } else {
                    this.attendance_snapshots_report = response.data;
                    Inertia.reload({only: ['attendances']});
                }
            })
        },
        getAttendances() {
            axios.get(route('teaching.course-batches-sessions.attendances', {attendance_report_id: this.attendance_snapshots_report_prop.id}))
            .then(response => {
                this.attendances = response.data;
            })
        },
        setTraineePresent(attendance, key) {
            this.$wait.start('ATTENDANCE_PRESENT_'+attendance.id);
            axios.put(route('teaching.course-batch-sessions.attendances.update', {course_batch_session_id: attendance.course_batch_session_id, id: attendance.id}), {
                status: 'present',
            }).then(response => {
                this.$set(this.attendances.data, key, response.data);
                this.$wait.end('ATTENDANCE_PRESENT_'+attendance.id);
            });
        },
        setTraineePresentLate(attendance, key) {
            this.$wait.start('ATTENDANCE_PRESENT_LATE_'+attendance.id);
            axios.put(route('teaching.course-batch-sessions.attendances.update', {course_batch_session_id: attendance.course_batch_session_id, id: attendance.id}), {
                status: 'present_late',
            }).then(response => {
                this.$set(this.attendances.data, key, response.data);
                this.$wait.end('ATTENDANCE_PRESENT_LATE_'+attendance.id);
            });
        },
        setTraineeAbsent(attendance, key) {
            this.$wait.start('ATTENDANCE_ABSENT_'+attendance.id);
            axios.put(route('teaching.course-batch-sessions.attendances.update', {course_batch_session_id: attendance.course_batch_session_id, id: attendance.id}), {
                status: 'absent',
            }).then(response => {
                this.$set(this.attendances.data, key, response.data);
                this.$wait.end('ATTENDANCE_ABSENT_'+attendance.id);
            });
        },
        setTraineeAbsentWihExcuse(attendance, key) {
            this.$wait.start('ATTENDANCE_ABSENT_WITH_EXCUSE_'+attendance.id);
            axios.put(route('teaching.course-batch-sessions.attendances.update', {course_batch_session_id: attendance.course_batch_session_id, id: attendance.id}), {
                status: 'absent_forgiven',
            }).then(response => {
                this.$set(this.attendances.data, key, response.data);
                this.$wait.end('ATTENDANCE_ABSENT_WITH_EXCUSE_'+attendance.id);
            });
        },
        saveAbsenceReason(attendance, key) {
            this.$wait.start('ATTENDANCE_ABSENCE_REASON_'+attendance.id);
            axios.put(route('teaching.course-batch-sessions.attendances.update', {course_batch_session_id: attendance.course_batch_session_id, id: attendance.id}), {
                absence_reason: attendance.absence_reason,
                status: 'absent_forgiven',
            }).then(response => {
                this.$set(this.attendances.data, key, response.data);
                this.$wait.end('ATTENDANCE_ABSENCE_REASON_'+attendance.id);
            });
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
