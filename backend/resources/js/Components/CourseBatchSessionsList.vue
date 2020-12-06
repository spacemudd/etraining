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
                {{ session.starts_at | timestampHours }}<br/>
                <span class="text-xs">{{ session.starts_at | timestampDate }}</span>
            </td>
            <td class="border-t py-3 text-left" dir="ltr">
                {{ session.ends_at | timestampHours }}<br/>
                <span class="text-xs">{{ session.ends_at | timestampDate }}</span>
            </td>
            <td class="border-t py-3 text-left">
                <button class="bg-red-600 py-1 px-2 rounded text-white text-sm hover:bg-red-800"
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
                return moment(dateString).local().format('YYYY-MM-DD');
            },
            timestampHours(dateString) {
                if (!dateString) return '';
                return moment(dateString).local().format('hh:mm A');
            },
        },
        methods: {
            deleteSession(session) {
                this.$wait.start('DELETING_SESSION_'+session.id);
                axios.delete(route('back.course-batch-sessions.destroy', {
                    course_id: session.course_id,
                    course_batch_id: session.course_batch_id,
                    course_batch_session: session.id
                }))
                    .then(response => {
                        this.$emit('session:deleted');
                    }).finally(() => {
                    this.$wait.end('DELETING_SESSION_'+session.id);
                })
            },
        }
    }
</script>
