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
    <app-layout-trainee>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'attendance', link: route('trainees.attendance-sheet.index')},
                ]"
            ></breadcrumb-container>

            <div class="bg-white rounded shadow overflow-x-auto my-5 p-5">
                <table class="w-full whitespace-no-wrap">
                    <colgroup>
                        <col style="width:200px;">
                    </colgroup>
                <thead>
                <tr>
                	<th class="text-right">{{ $t('words.date') }}</th>
                    <th class="text-right">{{ $t('words.course') }}</th>
                    <th>{{ $t('words.status') }}</th>
                </tr>
                </thead>
                	<tbody>
                			<tr v-for="record in records" class="hover:bg-gray-100 focus-within:bg-gray-100">
                				<td class="border-t text-right" dir="ltr">{{ record.course_batch_session.starts_at_timezone }}</td>
                                <td class="border-t">{{ record.course_batch_session.course.name_ar }}</td>
                                <td class="border-t text-center">{{ $t('words.'+record.status_name) }}</td>
                			</tr>
                	</tbody>
                </table>
            </div>
        </div>
    </app-layout-trainee>
</template>

<script>
import AppLayoutTrainee from '@/Layouts/AppLayoutTrainee'
import BreadcrumbContainer from "@/Components/BreadcrumbContainer";
export default {
    components: {
        AppLayoutTrainee,
        BreadcrumbContainer,
    },
    props: [
        'records',
    ],
    data() {
        return {
            //
        }
    },
    mounted() {
        //
    },
    methods: {
        //
    }
}
</script>
