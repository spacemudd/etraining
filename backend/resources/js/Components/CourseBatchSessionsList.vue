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
    <table class="w-full whitespace-no-wrap">
        <thead>
        <tr class="text-left font-bold">
            <th>{{ $t('words.start-time') }}</th>
            <th>{{ $t('words.end-time') }}</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="session in sessions" class="focus-within:bg-gray-100">
            <td class="border-t py-3 text-left" dir="ltr">
                {{ session.starts_at_timezone | timestampHours }}<br/>
                <span class="text-xs">{{ session.starts_at_timezone | timestampDate }}</span>
            </td>
            <td class="border-t py-3 text-left" dir="ltr">
                {{ session.ends_at_timezone | timestampHours }}<br/>
                <span class="text-xs">{{ session.ends_at_timezone | timestampDate }}</span>
            </td>
            <td class="border-t py-3 text-left flex flex-col">
                <a v-if="can('download-attendance-sheet-for-course-batch')"
                              :href="route('teaching.course-batch-sessions.attendance.export', {course_batch_session_id: session.id})"
                              class="bg-gray-200 py-1 px-2 rounded text-black text-sm hover:bg-gray-300 w-full text-center">
                    <img src="/img/excel.svg" class="float inline-block ml-2" style="max-width:16px;">
                    {{ $t('words.download-attendance') }}
                </a>
                <inertia-link :href="route('teaching.course-batch-sessions.attendance.index', {course_batch_session_id: session.id})"
                              class="bg-blue-600 py-1 px-2 rounded text-white text-sm hover:bg-blue-800 mt-5 w-full text-center">
                    {{ $t('words.attendance') }}
                </inertia-link>

                <button class="bg-red-600 py-1 px-2 rounded text-white text-sm hover:bg-red-800 mt-5 w-full disabled:bg-gray-500"
                        v-if="session.can_be_deleted"
                        :class="{'btn-disabled': $wait.is('DELETING_SESSION_'+session.id)}"
                        :id="session.id"
                        :disabled="$wait.is('DELETING_SESSION_'+session.id)"
                        @click.prevent="deleteSession(session)">
                    {{ $t('words.cancel') }}
                </button>

            </td>
        </tr>
        </tbody>
    </table>
</template>

<script>
    export default {
        name: "CourseBatchSessionsList",
        props: ['sessions'],
        filters: {
            timestampDate(dateString) {
                if (!dateString) return '';
                return moment(dateString).format('YYYY-MM-DD');
            },
            timestampHours(dateString) {
                if (!dateString) return '';
                return moment(dateString).format('hh:mm A');
            },
        },
        methods: {
            can(permName) {
                let permissions = document.head.querySelector('meta[name="user-permissions"]');

                if (permissions) {
                    return permissions.content.indexOf(permName) !== -1;
                }

                return false;
            },
            deleteSession(session) {
                this.$wait.start('DELETING_SESSION_'+session.id);
                axios.delete(route('back.course-batch-sessions.destroy', {
                    course_id: session.course_id,
                    course_batch_id: session.course_batch_id,
                    course_batch_session: session.id
                })).catch(error => {
                    alert(error.response.data.message);
                }).then(response => {
                    this.$emit('session:deleted');
                }).finally(() => {
                    this.$wait.end('DELETING_SESSION_'+session.id);
                })
            },
        }
    }
</script>
