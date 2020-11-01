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
        </tr>
        </thead>
        <tbody>
        <tr v-for="session in sessions" class="hover:bg-gray-100 focus-within:bg-gray-100">
            <td class="border-t py-3 text-left" dir="ltr">
                {{ session.starts_at | timestampHours }}<br/>
                <span class="text-xs">{{ session.starts_at | timestampDate }}</span>
            </td>
            <td class="border-t py-3 text-left" dir="ltr">
                {{ session.ends_at | timestampHours }}<br/>
                <span class="text-xs">{{ session.ends_at | timestampDate }}</span>
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
    }
</script>
