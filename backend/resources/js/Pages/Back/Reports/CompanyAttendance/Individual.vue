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

        <div class="container px-6 mx-auto grid grid-cols-12 gap-5">
            <div class="col-span-12">
                <div>
                    <input type="checkbox" id="with_attendance_times" v-model="withAttendanceTimes" />
                    <label for="with_attendance_times">{{ $t('words.with-attendance-times') }}</label>
                </div>
            </div>
            <div class="col-span-12">
                <a target="_blank"
                   class="btn btn-secondary"
                   :href="route('back.reports.company-attendance.individual.pdf', {id: record.report.id, trainee_id: record.trainee.id, with_attendance_times: withAttendanceTimes})">
                    PDF
                </a>
                <button class="btn btn-secondary"
                        @click="sendByEmail">
                    Email
                </button>
            </div>
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
    data() {
        return {
            withAttendanceTimes: false,
        }
    },
    methods: {
        sendByEmail() {
            if (confirm(this.$t('words.are-you-sure'))) {
                this.$inertia.post(route('back.reports.company-attendance.individual.email', {
                    id: this.record.report.id,
                    trainee_id: this.record.trainee.id,
                    with_attendance_times: this.withAttendanceTimes,
                }));
            }
        },
    },
}
</script>
