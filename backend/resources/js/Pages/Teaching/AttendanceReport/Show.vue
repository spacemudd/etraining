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

            <template v-if="!attendance_report.is_ready_for_review">
                ...
            </template>
            <template v-else>
                <div class="bg-white rounded shadow overflow-x-auto mt-5 p-5">

                    <div class="flex justify-between">
                        <div class="mx-10">
                            <table>
                                <colgroup>
                                    <col width="100px">
                                </colgroup>
                                <tbody>
                                <tr>
                                    <td>{{ $t('words.report-status') }}:</td>
                                    <td>{{ $t('words.'+attendance_report.status_name) }}</td>
                                </tr>
                                <tr>
                                    <td>{{ $t('words.total') }}:</td>
                                    <td>{{ attendances_count }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div>
                            <div class="col-span-12 sm:col-span-2 mt-5">
                                <input :placeholder="$t('words.search')"
                                       name="searchString"
                                       type="string"
                                       v-model="searchTraineeName"
                                       @input="searchTrainees"
                                       class="form-input rounded-md shadow-sm w-full">
                            </div>
                        </div>
                    </div>

                    <div class="p-5" v-if="attendances">
                        <table class="w-full whitespace-no-wrap">
                            <colgroup>
                                <col>
                                <col style="width:400px;">
                                <col style="width:480px;">
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
                                <td class="px-6 py-4">
                                    <span class="bg-red-600 text-white p-1 rounded" v-if="attendance.trainee.deleted_at">({{ $t('words.trainee-blacklisted') }})</span>
                                    {{ attendance.trainee.name }}
                                </td>
                                <td class="px-6 py-4">
                                    <button style="width:95px;height:32px;font-size:12px;"
                                            :class="{'bg-blue-600 border-blue-600 text-white': attendance.status_name === 'absent'}"
                                            class="border-2 inline-block border-red-500 rounded-lg text-center"
                                            @click.prevent="setAttendanceStatus(attendance, key, 'absent')">
                                        <div class="flex justify-center align-middle">
                                            <btn-loading-indicator v-if="$wait.is('ATTENDANCE_absent_'+attendance.id)" />
                                            {{ $t('words.absent') }}
                                        </div>
                                    </button>
                                    <button style="width:95px;height:32px;font-size:12px;"
                                            :class="{'bg-blue-600 border-blue-600 text-white': attendance.status_name === 'absent-with-excuse'}"
                                            class="border-2 inline-block border-red-500 rounded-lg"
                                            @click.prevent="setAttendanceStatus(attendance, key, 'absent-with-excuse')">
                                        <div class="flex justify-center align-middle">
                                            <btn-loading-indicator v-if="$wait.is('ATTENDANCE_absent-with-excuse_'+attendance.id)" />
                                            {{ $t('words.absent-with-excuse') }}
                                        </div>
                                    </button>
                                    <button style="width:95px;height:32px;font-size:12px;"
                                            :class="{'bg-blue-600 border-blue-600 text-white': attendance.status_name === 'present-but-late'}"
                                            class="border-2 inline-block border-red-500 rounded-lg"
                                            @click.prevent="setAttendanceStatus(attendance, key, 'present-but-late')">
                                        <div class="flex justify-center align-middle">
                                            <btn-loading-indicator v-if="$wait.is('ATTENDANCE_present-but-late_'+attendance.id)" />
                                            {{ $t('words.present-but-late') }}
                                        </div>
                                    </button>
                                    <button style="width:95px;height:32px;font-size:12px;"
                                            :class="{'bg-blue-600 border-blue-600 text-white': attendance.status_name === 'present'}"
                                            class="border-2 inline-block border-red-500 rounded-lg"
                                            @click.prevent="setAttendanceStatus(attendance, key, 'present')">
                                        <div class="flex justify-center align-middle">
                                            <btn-loading-indicator v-if="$wait.is('ATTENDANCE_present_'+attendance.id)" />
                                            {{ $t('words.present') }}
                                        </div>
                                    </button>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex" v-if="attendance.status_name==='absent-with-excuse'">
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
                        <pagination v-if="!searchTraineeName" :links="attendances.links" />
                    </div>

                    <div class="flex mt-5">
                        <inertia-link :href="route('teaching.courses.show', course_batch_session.course_id)" class="flex items-center justify-start rounded-md px-4 py-2 hover:bg-gray-300 text-right">
                            {{ $t('words.cancel') }}
                        </inertia-link>
                        <inertia-link
                            :href="route('teaching.attendance-reports.confirm', {attendance_report_id: this.attendance_report.id})"
                            :class="{'opacity-50': $wait.is('SUBMITTING_ATTENDANCE')}"
                            class="flex items-center justify-start rounded-md px-4 py-2 bg-yellow-200 hover:bg-yellow-300 text-right mx-5">
                            <btn-loading-indicator v-if="$wait.is('SUBMITTING_ATTENDANCE')" />
                            {{ $t('words.approve') }}
                        </inertia-link>
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
        'attendance_report_prop',
        'attendances_prop',
        'attendances_count',
    ],
    data() {
        return {
            loaded: false,
            form: this.$inertia.form({
                course_batch_session_id: null,
            }),
            attendance_report: this.attendance_report_prop,
            attendances: null,
            searchTraineeName: null,
        }
    },
    mounted() {
        this.attendance_report = this.attendance_report_prop;
        this.form.course_batch_session_id = this.course_batch_session.id;
        this.attendances = this.attendances_prop;

        if (!this.attendance_report.is_ready_for_review) {
            this.checkReportStatus();
        }
    },
    methods: {
        searchTrainees: _.debounce(
            function() {
                this.searchTraineesRequest();
            }, 200
        ),
        searchTraineesRequest() {
            // if (this.searchTraineeName) {
                axios.get(route('teaching.attendance-reports.attendances', {
                    attendance_report_id: this.attendance_report_prop.id,
                    searchTraineeName: this.searchTraineeName,
                })).then(response => {
                    this.attendances = response.data;
                })
            // } else {
            //     this.checkReportStatus();
            // }
        },
        checkReportStatus() {
            let vm = this;
            axios.get(route('teaching.course-batch-sessions.attendance-reports.show-report', {
                attendance_report_id: this.attendance_report_prop.id,
            }))
            .then(response => {
                if (!response.data.is_ready_for_review) {
                    setTimeout(function() {
                        vm.checkReportStatus();
                    }, 2000);
                } else {
                    this.attendance_report = response.data;
                    Inertia.reload({only: ['attendances']});
                }
            })
        },
        getAttendances() {
            axios.get(route('teaching.course-batches-sessions.attendances', {attendance_report_id: this.attendance_report_prop.id}))
            .then(response => {
                this.attendances = response.data;
            })
        },
        setAttendanceStatus(attendance, key, status) {
            this.$wait.start('ATTENDANCE_'+status+'_'+attendance.id);
            axios.put(route('teaching.attendance-report-records.update', {id: attendance.id}), {
                status: status,
            }).then(response => {
                this.$set(this.attendances.data, key, response.data);
                this.$wait.end('ATTENDANCE_'+status+'_'+attendance.id);
            });
        },
        saveAbsenceReason(attendance, key) {
            this.$wait.start('ATTENDANCE_ABSENCE_REASON_'+attendance.id);
            axios.put(route('teaching.attendance-report-records.update', {id: attendance.id}), {
                absence_reason: attendance.absence_reason,
                status: 'absent-with-excuse',
            }).then(response => {
                this.$set(this.attendances.data, key, response.data);
                this.$wait.end('ATTENDANCE_ABSENCE_REASON_'+attendance.id);
            });
        },
    },
}
</script>

<!-- <template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6" v-if="loaded">
            <breadcrumb-container
                :crumbs="[ 
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'courses', link: route('teaching.courses.index')},
                    {title_raw: course_batch_session.course.name_ar, link: route('teaching.courses.show', course_batch_session.course_id)},
                    {title_raw: course_batch_session.starts_at_timezone},
                    {title_raw: $t('words.attendance')},
                ]"
            ></breadcrumb-container>

            <template v-if="!attendance_report.is_ready_for_review">
                ...
            </template>
            <template v-else>
                <div class="bg-white rounded shadow overflow-x-auto mt-5 p-5">

                    <div class="flex justify-between">
                        <div class="mx-10">
                            <table>
                                <colgroup>
                                    <col width="100px">
                                </colgroup>
                                <tbody>
                                <tr>
                                    <td>{{ $t('words.report-status') }}:</td>
                                    <td>{{ $t('words.'+attendance_report.status_name) }}</td>
                                </tr>
                                <tr>
                                    <td>{{ $t('words.total') }}:</td>
                                    <td>{{ attendances_count }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div>
                            <div class="col-span-12 sm:col-span-2 mt-5">
                                <input :placeholder="$t('words.search')"
                                       name="searchString"
                                       type="string"
                                       v-model="searchTraineeName"
                                       @input="searchTrainees"
                                       class="form-input rounded-md shadow-sm w-full">
                            </div>
                        </div>
                    </div>

                    <div class="p-5" v-if="attendances">
                        <table class="w-full whitespace-no-wrap">
                            <colgroup>
                                <col>
                                <col style="width:400px;">
                                <col style="width:480px;">
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
                                <td class="px-6 py-4">
                                    <span class="bg-red-600 text-white p-1 rounded" v-if="attendance.trainee.deleted_at">({{ $t('words.trainee-blacklisted') }})</span>
                                    {{ attendance.trainee.name }}
                                </td>
                                <td class="px-6 py-4">
                                    <button style="width:95px;height:32px;font-size:12px;"
                                            :class="{'bg-blue-600 border-blue-600 text-white': attendance.status_name === 'absent'}"
                                            class="border-2 inline-block border-red-500 rounded-lg text-center"
                                            @click.prevent="setAttendanceStatus(attendance, key, 'absent')">
                                        <div class="flex justify-center align-middle">
                                            <btn-loading-indicator v-if="$wait.is('ATTENDANCE_absent_'+attendance.id)" />
                                            {{ $t('words.absent') }}
                                        </div>
                                    </button>
                                    <button style="width:95px;height:32px;font-size:12px;"
                                            :class="{'bg-blue-600 border-blue-600 text-white': attendance.status_name === 'absent-with-excuse'}"
                                            class="border-2 inline-block border-red-500 rounded-lg"
                                            @click.prevent="setAttendanceStatus(attendance, key, 'absent-with-excuse')">
                                        <div class="flex justify-center align-middle">
                                            <btn-loading-indicator v-if="$wait.is('ATTENDANCE_absent-with-excuse_'+attendance.id)" />
                                            {{ $t('words.absent-with-excuse') }}
                                        </div>
                                    </button>
                                    <button style="width:95px;height:32px;font-size:12px;"
                                            :class="{'bg-blue-600 border-blue-600 text-white': attendance.status_name === 'present-but-late'}"
                                            class="border-2 inline-block border-red-500 rounded-lg"
                                            @click.prevent="setAttendanceStatus(attendance, key, 'present-but-late')">
                                        <div class="flex justify-center align-middle">
                                            <btn-loading-indicator v-if="$wait.is('ATTENDANCE_present-but-late_'+attendance.id)" />
                                            {{ $t('words.present-but-late') }}
                                        </div>
                                    </button>
                                    <button style="width:95px;height:32px;font-size:12px;"
                                            :class="{'bg-blue-600 border-blue-600 text-white': attendance.status_name === 'present'}"
                                            class="border-2 inline-block border-red-500 rounded-lg"
                                            @click.prevent="setAttendanceStatus(attendance, key, 'present')">
                                        <div class="flex justify-center align-middle">
                                            <btn-loading-indicator v-if="$wait.is('ATTENDANCE_present_'+attendance.id)" />
                                            {{ $t('words.present') }}
                                        </div>
                                    </button>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex" v-if="attendance.status_name==='absent-with-excuse'">
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
                        <pagination v-if="!searchTraineeName" :links="attendances.links" />
                    </div>

                    <div class="flex mt-5">
                        <inertia-link :href="route('teaching.courses.show', course_batch_session.course_id)" class="flex items-center justify-start rounded-md px-4 py-2 hover:bg-gray-300 text-right">
                            {{ $t('words.cancel') }}
                        </inertia-link>
                        <inertia-link
                            :href="route('teaching.attendance-reports.confirm', {attendance_report_id: this.attendance_report.id})"
                            :class="{'opacity-50': $wait.is('SUBMITTING_ATTENDANCE')}"
                            class="flex items-center justify-start rounded-md px-4 py-2 bg-yellow-200 hover:bg-yellow-300 text-right mx-5">
                            <btn-loading-indicator v-if="$wait.is('SUBMITTING_ATTENDANCE')" />
                            {{ $t('words.approve') }}
                        </inertia-link>
                    </div>
                </div>
            </template>
        </div>
        <div v-else class="loading-spinner">Loading...</div>
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
        'attendance_report_prop',
        'attendances_prop',
        'attendances_count',
    ],
    data() {
        return {
            loaded: false,
            form: this.$inertia.form({
                course_batch_session_id: null,
            }),
            attendance_report: this.attendance_report_prop,
            attendances: null,
            searchTraineeName: null,
        }
    },
    mounted() {
        this.attendance_report = this.attendance_report_prop;
        this.form.course_batch_session_id = this.course_batch_session.id;
        this.attendances = this.attendances_prop;

        this.loaded = true; // Mark as loaded once data is assigned

        if (!this.attendance_report.is_ready_for_review) {
            this.checkReportStatus();
        }
    },
    methods: {
        searchTrainees: _.debounce(
            function() {
                this.searchTraineesRequest();
            }, 200
        ),
        searchTraineesRequest() {
            let queryString = '?';
            if (this.searchTraineeName) {
                queryString += 'trainee_name=' + this.searchTraineeName;
            }
            Inertia.get(route('teaching.attendance-reports.edit', { attendance_report_id: this.attendance_report.id }) + queryString, {}, {
                preserveState: true, replace: true, preserveScroll: true,
            });
        },
        checkReportStatus() {
            Inertia.post(route('teaching.attendance-reports.check-status', { attendance_report_id: this.attendance_report.id }), this.form, {
                preserveState: true,
                replace: true,
                preserveScroll: true,
                onFinish: () => {
                    this.loaded = true; // Data is loaded after finishing
                }
            });
        },
        saveAbsenceReason(attendance, key) {
            Inertia.post(route('teaching.attendance-reports.save-absence-reason', { attendance_id: attendance.id }), {
                absence_reason: attendance.absence_reason,
            }, {
                preserveState: true,
                replace: true,
                preserveScroll: true,
                onFinish: () => {
                    this.loaded = true; // Data is loaded after finishing
                }
            });
        },
        setAttendanceStatus(attendance, key, status) {
            this.loaded = false; // Set to false before making the request
            attendance.status_name = status;

            Inertia.post(route('teaching.attendance-reports.set-attendance-status', { attendance_id: attendance.id }), {
                status_name: attendance.status_name,
            }, {
                preserveState: true,
                replace: true,
                preserveScroll: true,
                onFinish: () => {
                    this.loaded = true; // Data is loaded after finishing
                }
            });
        }
    },
}
</script> -->

