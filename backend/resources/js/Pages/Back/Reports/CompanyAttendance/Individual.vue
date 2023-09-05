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
                    {title_raw: record.report.number, link: route('back.reports.company-attendance.show', record.report.id)},
                    {title_raw: record.trainee.name},
                ]"
            ></breadcrumb-container>
        </div>

        <div class="container px-6 mx-auto grid grid-cols-12">
            <a target="_blank" :href="route('back.reports.company-attendance.individual.pdf', {id: record.report.id, trainee_id: record.trainee.id})">pdf</a>
            <a :href="route('back.reports.company-attendance.individual.email', {id: record.report.id, trainee_id: record.trainee.id})">email</a>
        </div>

    </app-layout>
</template>

<script>
    import JetLabel from '@/Jetstream/Label';
    import JetInput from '@/Jetstream/Input';
    import JetInputError from '@/Jetstream/InputError';
    import JetFormSection from '@/Jetstream/FormSection';
    import JetActionMessage from '@/Jetstream/ActionMessage';
    import JetButton from '@/Jetstream/Button';
    import AppLayout from '@/Layouts/AppLayout'
    import IconNavigate from 'vue-ionicons/dist/ios-arrow-dropright'
    import BreadcrumbContainer from "@/Components/BreadcrumbContainer";
    import BtnLoadingIndicator from "../../../../Components/BtnLoadingIndicator";
    import SelectCompany from "../../../../Components/SelectCompany";
    import DateRangePicker from 'vue2-daterange-picker'
    import 'vue2-daterange-picker/dist/vue2-daterange-picker.css'
    import CompanyAttendanceState from "./company-attendance-state";

    export default {
        metaInfo() {
            return {
                title: this.$t('words.company-attendance'),
            }
        },
        components: {
            CompanyAttendanceState,
            SelectCompany,
            IconNavigate,
            AppLayout,
            JetLabel,
            JetInput,
            JetInputError,
            JetFormSection,
            JetActionMessage,
            JetButton,
            BreadcrumbContainer,
            BtnLoadingIndicator,
            DateRangePicker,
        },
        props: [
            'record',
        ],
        mounted() {

        },
        data() {
            var date = new Date(), y = date.getFullYear(), m = date.getMonth();
            var startDate = new Date(y, m, 1);
            var endDate = new Date(y, m + 1, 0);
            return {
                createAttendanceReportForm: this.$inertia.form({
                    company_id: null,
                    period: {startDate, endDate},
                }, {
                    bag: 'createAttendanceReport',
                    resetOnSuccess: true,
                }),
            }
        },
        methods: {
            send() {
                if (confirm(this.$t('words.are-you-sure'))) {
                    this.$inertia.post(route('back.reports.company-attendance.send', this.report.id));
                }
            },
            clone() {
                if (confirm(this.$t('words.are-you-sure'))) {
                    this.$inertia.post(route('back.reports.company-attendance.clone', this.report.id));
                }
            },
            approveReport() {
                if (confirm(this.$t('words.are-you-sure'))) {
                    this.$inertia.post(route('back.reports.company-attendance.approve', this.report.id));
                }
            },
            saveEmails() {
                this.$inertia.put(route('back.reports.company-attendance.update', this.report.id), {
                    to_emails: this.report.to_emails,
                    cc_emails: this.report.cc_emails,
                });
            },
            deleteReport() {
                if (confirm(this.$t('words.are-you-sure'))) {
                    this.$inertia.delete(route('back.reports.company-attendance.destroy', this.report.id));
                }
            },
        },
    }
</script>
