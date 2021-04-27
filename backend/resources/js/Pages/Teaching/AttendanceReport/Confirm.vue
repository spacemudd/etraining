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
                    {title_raw: $t('words.confirm')},
                ]"
            ></breadcrumb-container>

            <template>
                <div class="bg-white rounded shadow overflow-x-auto">
                    <div class="p-5">
                        <div>
                            <table class="table">
                            	<tbody>
                            			<tr>
                            				<td>{{ $t('words.sending-absent-warnings-to-users') }}:</td>
                                            <td>{{ sending_absent_warnings_to_count }}</td>
                            			</tr>
                                        <tr>
                                            <td>{{ $t('words.sending-late-warnings-to-users') }}:</td>
                                            <td>{{ sending_late_warnings_to_count }}</td>
                                        </tr>
                            	</tbody>
                            </table>
                        </div>
                        <table class="w-full whitespace-no-wrap" v-if="sending_absent_warnings_to_list.length || sending_late_warnings_to_list.length">
                            <colgroup>
                                <col style="width:400px;">
                                <col style="width:400px;">
                                <col>
                            </colgroup>
                            <thead>
                                <tr class="text-left font-bold">
                                    <th class="px-6 pt-6 pb-4">{{ $t('words.name') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-t" v-for="(warning, key) in sending_absent_warnings_to_list">
                                    <td class="px-6 py-4"><span class="py-1 px-2 bg-red-600 text-white rounded">{{ $t('words.absent') }}</span> {{ warning.trainee.name }}</td>
                                </tr>
                                <tr class="border-t" v-for="(warning, key) in sending_late_warnings_to_list">
                                    <td class="px-6 py-4"><span class="py-1 px-2 bg-red-400 text-white rounded">{{ $t('words.present-but-late') }}</span> {{ warning.trainee.name }}</td>
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
                    <inertia-link :href="route('teaching.course-batch-sessions.attendance.index', {course_batch_session_id: course_batch_session.id})"
                                  class="flex items-center justify-start rounded-md px-4 py-2 hover:bg-gray-300 text-right">
                        {{ $t('words.cancel') }}
                    </inertia-link>
                    <button @click.prevent="approveAttendanceSheet"
                            :class="{'opacity-50': $wait.is('CONFIRM_ATTENDANCE')}"
                            class="flex items-center justify-start rounded-md px-4 py-2 bg-yellow-200 hover:bg-yellow-300 text-right mx-5">
                        <btn-loading-indicator v-if="$wait.is('CONFIRM_ATTENDANCE')" />
                        {{ $t('words.confirm-attendance-sheet') }}
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
        props: [
            'report',
            'course_batch_session',
            'sending_absent_warnings_to_count',
            'sending_absent_warnings_to_list',
            'sending_late_warnings_to_count',
            'sending_late_warnings_to_list',
        ],
        mounted() {
            this.$wait.end('CONFIRM_ATTENDANCE');
            // this.form.course_batch_session_id = this.course_batch_session.id;
        },
        data() {
            return {
                form: this.$inertia.form({
                    course_batch_session_id: null,
                }),
            }
        },
        methods: {
            approveAttendanceSheet() {
                this.$wait.start('CONFIRM_ATTENDANCE');
                this.form.post(
                    route('teaching.attendance-reports.approve', {attendance_report_id: this.report.id})
                );
            },
        },
    }
</script>
