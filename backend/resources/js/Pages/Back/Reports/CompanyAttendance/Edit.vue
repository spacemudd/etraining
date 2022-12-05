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
                    {title_raw: report.id},
                ]"
            ></breadcrumb-container>
        </div>

        <div class="container px-6 mx-auto">
            <jet-form-section @submitted="updateReport">
            <template #title>
                {{ $t('words.submit-company-attendance-report') }}
            </template>

            <template #description>
                {{ $t('words.prepare-the-monthly-attendance-report-to-send-to-the-company-from-the-system') }}
            </template>

            <template #form>
                <div class="col-span-6 sm:col-span-4">
                    <jet-label for="company_id" :value="$t('words.company')" />
                    <select-company class="mt-2"
                                    :selected-item="report.company"
                          v-model="updateAttendanceReportForm.company_id"
                          :required="true"
                    />
                    <jet-input-error class="mt-2" />
                </div>

                <div class="col-span-3 sm:col-span-2">
                    <jet-label for="company_id" :value="$t('words.time-period')" />
                    <date-range-picker
                        ref="picker"
                        :locale-data="{ firstDay: 1, format: 'dd-mm-yyyy' }"
                        :singleDatePicker="false"
                        :timePicker="false"
                        :showWeekNumbers="true"
                        :showDropdowns="true"
                        :autoApply="false"
                        v-model="updateAttendanceReportForm.period"
                    >
                        <template v-slot:input="picker" style="width:100%;">
                            {{ picker.startDate | date }} - {{ picker.endDate | date }}
                        </template>
                   </date-range-picker>
                </div>
            </template>

            <template #actions>
                <jet-action-message :on="updateAttendanceReportForm.recentlySuccessful" class="mr-3">
                    {{ $t('words.created-successfully') }}
                </jet-action-message>

                <jet-button :class="{ 'opacity-25': updateAttendanceReportForm.processing }" :disabled="updateAttendanceReportForm.processing">
                    {{ $t('words.save') }}
                </jet-button>
            </template>
        </jet-form-section>
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

    export default {
        metaInfo() {
            return {
                title: this.$t('words.company-attendance'),
            }
        },
        components: {
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
            'report',
        ],
        mounted() {
            this.updateAttendanceReportForm.company_id = this.report.company_id;
            this.updateAttendanceReportForm.period = {
                startDate: this.report.date_from,
                endDate: this.report.date_to,
            }
        },
      filters: {
        date (val) {
          return val ? val.toLocaleString('en-GB', {year: 'numeric', month: '2-digit', day: '2-digit'}) : ''
        }
      },
        data() {
            var date = new Date(), y = date.getFullYear(), m = date.getMonth();
            var startDate = new Date(y, m, 1);
            var endDate = new Date(y, m + 1, 0);
            return {
                updateAttendanceReportForm: this.$inertia.form({
                    company_id: null,
                    period: {startDate, endDate},
                }, {
                    bag: 'createAttendanceReport',
                    resetOnSuccess: true,
                }),
            }
        },
        methods: {
            updateReport() {
                this.updateAttendanceReportForm.put(route('back.reports.company-attendance.update', this.report.id));
            },
        },
    }
</script>
