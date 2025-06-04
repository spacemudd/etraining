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
                ]"
            ></breadcrumb-container>

            <div class="grid md:grid-cols-4 grid-cols-1 gap-6">

                <inertia-link :href="route('back.reports.course-attendances.index')"
                              class="col-span-1 bg-white shadow-lg rounded-lg p-5 transition-all duration-500 ease-in-out hover:bg-gray-200 text-center">
                    {{ $t('words.course-attendances') }}
                </inertia-link>

                <inertia-link :href="route('back.reports.contracts.index')"
                              class="col-span-1 bg-white shadow-lg rounded-lg p-5 transition-all duration-500 ease-in-out hover:bg-gray-200 text-center">
                    {{ $t('words.contracts') }}
                </inertia-link>

                <inertia-link v-can="'submit-company-attendance-report'"
                              :href="route('back.reports.company-attendance.index')"
                              class="col-span-1 bg-white shadow-lg rounded-lg p-5 transition-all duration-500 ease-in-out hover:bg-gray-200 text-center">
                    {{ $t('words.attendance-sheet-for-companies-report') }}
                </inertia-link>


                <inertia-link :href="route('back.reports.trainees-witout-invoices.index')"
                              class="col-span-1 bg-white shadow-lg rounded-lg p-5 transition-all duration-500 ease-in-out hover:bg-gray-200 text-center">
                    {{ $t('words.trainees-without-invoice') }}
                </inertia-link>


                <inertia-link :href="route('back.reports.company-certificates.index')"
                              class="col-span-1 bg-white shadow-lg rounded-lg p-5 transition-all duration-500 ease-in-out hover:bg-gray-200 text-center">
                    {{ $t('words.companies-certificates') }}
                </inertia-link>

                <inertia-link :href="route('back.reports.bulk-course-attendances.index')"
                              class="col-span-1 bg-white shadow-lg rounded-lg p-5 transition-all duration-500 ease-in-out hover:bg-gray-200 text-center">
                    {{ $t('words.attendance-bulk-courses') }}
                </inertia-link>

            

                  <inertia-link :href="route('attendance-due-dates.index')"
                              class="col-span-1 bg-white shadow-lg rounded-lg p-5 transition-all duration-500 ease-in-out hover:bg-gray-200 text-center">
                    {{ $t('words.attendance-due-dates') }}
                </inertia-link>

                <inertia-link v-if="$page.props.user.email === 'sara@hadaf-hq.com'"
                              :href="route('back.reports.deleted-trainees.index')"
                              class="col-span-1 bg-white shadow-lg rounded-lg p-5 transition-all duration-500 ease-in-out hover:bg-gray-200 text-center">
                    {{ $t('words.deleted-trainees-report') }}
                </inertia-link>

            </div>

        </div>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import IconNavigate from 'vue-ionicons/dist/ios-arrow-dropright'
import BreadcrumbContainer from "@/Components/BreadcrumbContainer";

export default {
    metaInfo() {
        return {
            title: this.$t('words.reports')
        }
    },
    components: {
        IconNavigate,
        AppLayout,
        BreadcrumbContainer,
    },
    computed: {
    },
    data() {
        return {
            //
        }
    },
    methods: {
        //
    },
}
</script>
