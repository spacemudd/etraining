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
                    {title: 'company-attendance', link: route('back.reports.company-attendance.index')},
                    {title: 'send-report', link: route('back.reports.company-attendance.send-report')},
                ]"
            ></breadcrumb-container>
        </div>
        <div class="container px-6 mx-auto grid grid-cols-12">
            <div class="col-span-3">
                <form :action="route('back.reports.company-attendance.send-report.download')" method="post" target="_blank">
                    <input type="hidden" name="_token" :value="token">
                    <div class="mt-3">
                        <jet-label for="date" :value="$t('words.date')"/>
                        <div class="flex justify-center">
                            <jet-input type="month"
                                       :value="currentDate"
                                       min="2019-01"
                                       class="mt-1 block w-full"
                                       name="date"
                                       required="true" />
                        </div>
                    </div>
                    <button class="btn btn-primary mt-3" type="submit">{{ $t('words.download') }}</button>
                </form>
            </div>
        </div>
    </app-layout>
</template>

<script>
    import JetLabel from '@/Jetstream/Label';
    import AppLayout from '@/Layouts/AppLayout'
    import IconNavigate from 'vue-ionicons/dist/ios-arrow-dropright'
    import BreadcrumbContainer from "@/Components/BreadcrumbContainer";
    import JetInput from '@/Jetstream/Input'

    export default {
        props: [
            'reports',
            'filters',
        ],
        metaInfo() {
            return {
                title: this.$t('words.company-attendance'),
            }
        },
        components: {
            IconNavigate,
            AppLayout,
            JetLabel,
            BreadcrumbContainer,
            JetInput,
        },
        computed: {
            token() {
                return document.head.querySelector('meta[name="csrf-token"]').content;
            },
            currentDate() {
                return new Date().toISOString().substr(0, 7);
            },
        },
        mounted() {

        },
        data() {
            return {

            }
        },
        methods: {

        },
    }
</script>
