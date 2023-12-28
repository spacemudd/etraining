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
                    {title_raw: report.number},
                ]"
            ></breadcrumb-container>
        </div>

        <div class="container px-6 mx-auto grid grid-cols-12">
            <div class="flex justify-end col-span-12 gap-4">
                <button v-if="!report.approved_at"
                        @click="deleteReport"
                        class="inline-flex items-center px-4 py-2 bg-red-300 border border-transparent rounded-md font-semibold text-xs text-black uppercase tracking-normal transition ease-in-out duration-150">
                    {{ $t('words.delete') }}
                </button>
                <inertia-link :href="route('back.reports.company-attendance.edit', report.id)"
                              class="inline-flex items-center px-4 py-2 bg-blue-300 border border-transparent rounded-md font-semibold text-xs text-black uppercase tracking-normal transition ease-in-out duration-150">
                    {{ $t('words.edit') }}
                </inertia-link>
                <button @click="clone"
                        class="btn btn-secondary">
                    {{ $t('words.clone') }}
                </button>
                <button @click="send"
                        v-if="report.approved_at"
                        class="btn btn-secondary">
                    {{ $t('words.send') }}
                </button>
                <a :href="route('back.reports.company-attendance.preview', report.id)"
                   target="_blank"
                   class="btn-secondary">{{ $t('words.preview') }}</a>
                <a :href="route('back.reports.company-attendance.excel', report.id)"
                   target="_blank"
                   class="btn btn-secondary flex justify-center gap-2">
                    <span>{{ $t('words.export') }}</span>
                    <img src="/img/excel.svg" style="max-width:16px;">
                </a>
                <button v-if="!report.approved_at" @click="approveReport" class="btn-primary">{{ $t('words.approve') }}</button>
            </div>
        </div>

        <div class="container px-6 mx-auto grid grid-cols-12 gap-5 mt-10">
            <div class='col-span-6 lg:col-span-3'>
                <p>{{ $t('words.report') }} #</p>
                <h1 class="text-2xl">{{ report.number }}</h1>
                <h2 v-if="report.approved_at">
                    <span class="text-2xl bg-green-300 text-black px-2 rounded">{{ $t('words.approved') }}</span>
                    <br/>
                    <span class="bg-gray-200 px-2 rounded text-sm">{{ report.approved_at_human }}</span>
                    <br/>
                    <span class="bg-gray-200 px-2 rounded text-sm">{{ report.approved_by.email }}</span>
                </h2>
                <h2 v-else><span class="text-2xl bg-yellow-300 text-black px-2 rounded">{{ $t('words.review') }}</span></h2>
                <h3 v-if="report.with_attendance_times" class="text-xs mt-2">
                    <span class="text-red-600">**</span> {{ $t('words.includes-attendance-times') }}
                </h3>
            </div>
            <div class='col-span-6 lg:col-span-3'>
                <p>{{ $t('words.period') }}</p>
                <h2 class="text-2xl">{{ report.date_from }}</h2>
                <h2 class="text-2xl">{{ report.date_to }}</h2>
            </div>
            <div class='col-span-12 lg:col-span-6'>
                <p>{{ $t('words.company') }}</p>
                <p class="text-2xl" v-if="report.company">{{ report.company.resource_label }}</p>
            </div>
        </div>

        <!-- Email information for the company -->
        <div class="mt-10 container px-6 mx-auto grid grid-cols-12 gap-4">
            <div class="col-span-12 lg:col-span-6">
                <jet-label for="to_emails" :value="$t('words.to')" />
                <jet-input dir="ltr"
                           id="to_emails"
                           type="email"
                           :disabled="emailsToLoading"
                           class="mt-1 block w-full"
                           @keyup.enter.native="saveEmailTo"
                           v-model="email_to" />
                <table class="w-full text-xs mt-2">
                	<tbody>
                			<tr v-for="record in report.emails_to">
                                <td class="border border-2" style="width:20px;">
                                    <button class="m-auto mt-1" @click.prevent="removeEmail(record.id)">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="hover:text-red-500 w-3 h-3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                        </svg>
                                    </button>
                                </td>
                				<td class="border border-2 px-2">
                                    <span :class="{'text-red-800 font-heavy': record.failed_at}">{{ record.email }}</span>
                                    <div class="bg-green-600 text-white px-2">
                                        <span dir="ltr">{{ record.delivered_at_timezone }}</span>
                                    </div>
                                    <div class="bg-red-500 text-white px-2">
                                        {{ record.failed_reason }}
                                    </div>
                                </td>
                			</tr>
                	</tbody>
                </table>
            </div>
            <div class="col-span-12 lg:col-span-6">
                <jet-label for="cc_emails" :value="$t('words.cc-emails')" />
                <jet-input dir="ltr"
                           id="cc_emails"
                           type="email"
                           class="mt-1 block w-full"
                           :disabled="emailsCcLoading"
                           @keyup.enter.native="saveEmailCc"
                           v-model="email_cc" />
                <table class="w-full text-xs mt-2">
                	<tbody>
                			<tr v-for="record in report.emails_cc">
                                <td class="border border-2" style="width:20px;">
                                    <button class="m-auto mt-1" @click.prevent="removeEmail(record.id)">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="hover:text-red-500 w-3 h-3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                        </svg>
                                    </button>
                                </td>
                				<td class="border border-2 px-2">
                                    <span :class="{'text-red-800 font-heavy': record.failed_at}">{{ record.email }}</span>
                                    <div class="bg-green-600 text-white px-2">
                                        <span dir="ltr">{{ record.delivered_at_timezone }}</span>
                                    </div>
                                    <div class="bg-red-500 text-white px-2" dir="ltr">
                                        {{ record.failed_reason }}
                                    </div>
                                </td>
                			</tr>
                	</tbody>
                </table>
            </div>
        </div>

        <!-- Trainees in the report -->
        <div class="mt-10 container px-6 mx-auto">
            <hr>
            <div class="flex justify-between">
                <h2 class="mt-5 px-2 font-bold">{{ $t('words.trainees') }} ({{ report.trainees.length }})</h2>
                <h2 class="mt-5 px-2 font-bold">{{ $t('words.selected') }} ({{ selectedCount }}) <inertia-link :href="route('back.reports.company-attendance.toggle-select', report.id)">اختيار الكل</inertia-link></h2>
            </div>

            <div class="overflow-x-auto relative">
                <table class="mt-2 w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-200 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="py-3 px-6">{{ $t('words.name') }}</th>
                        <th scope="col" class="py-3 px-6">{{ $t('words.identity_number') }}</th>
                        <th scope="col" class="py-3 px-6">{{ $t('words.action') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-if="report.trainees.length === 0" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                        <td class="py-4 px-6 text-center" colspan="3">{{ $t('words.no-records-have-been-found') }}</td>
                    </tr>
                    <tr v-for="trainee in report.trainees" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                        <th scope="row" class="py-4 px-6 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            <label @click.prevent="toggleTrainee(trainee)">
                                <input class="ml-5" type="checkbox" name="checkbox" :checked="Number(trainee.pivot.active)">
                                {{ trainee.name }}<br/>
                                {{ trainee.clean_phone }}
                            </label>
                        </th>
                        <td class="py-4 px-6">
                            <a class="text-blue-500" :href="route('back.trainees.show', trainee.id)" target="_blank">{{ trainee.clean_identity_number }}</a>
                        </td>
                        <td class="py-4 px-6">
                            <company-attendance-state :report="report" :trainee="trainee"></company-attendance-state>
                        </td>
                    </tr>
                    </tbody>
                    <tfoot>
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <td colspan="3" class="text-xs text-gray-500 dark:text-gray-400">
                                <add-new-trainee-to-report
                                    class="my-2"
                                    :report="report">
                                </add-new-trainee-to-report>
                            </td>
                        </tr>
                    </tfoot>
                </table>
                <p class="text-xs mt-2">* {{ $t('words.to-update-the-table-after-adding-trainees-please-recreate-the-report') }}</p>
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
import AddNewTraineeToReport from "./AddNewTraineeToReport";

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
        AddNewTraineeToReport,
    },
    props: [
        'report',
    ],
    mounted() {

    },
    computed: {
        selectedCount() {
            return this.report.trainees.filter((trainee) => {
                return Boolean(Number(trainee.pivot.active));
            }).length;
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
            emailsToLoading: false,
            emailsCcLoading: false,
            email_cc: '',
            email_to: '',
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
        saveEmailTo() {
            this.emailsToLoading = true;
            let url = route('back.reports.company-attendance.add-email', {report_id: this.report.id});
            let emails = this.email_to;

            if (this.email_to.includes(',')) {
                url = route('back.reports.company-attendance.add-email-in-bulk', {report_id: this.report.id});
                emails = this.email_to.split(', ').map((email) => email.trim()).filter((email) => email.length > 0);
            }

            this.$inertia.post(url, {
                email: emails,
                type: 'to',
            }, {
                preserveScroll: true,
                onSuccess: () => {
                    this.email_to = '';
                    this.emailsToLoading = false;
                },
                onError: (res) => {
                    if (res.email) {
                        alert(res.email);
                    } else {
                        alert('يجب ان يكون البريد صحيح');
                    }
                    this.emailsToLoading = false;
                }
            });
        },
        saveEmailCc() {
            this.emailsCcLoading = true;
            let url = route('back.reports.company-attendance.add-email', {report_id: this.report.id});
            let emails = this.email_cc;

            if (this.email_to.includes(',')) {
                url = route('back.reports.company-attendance.add-email-in-bulk', {report_id: this.report.id});
                emails = this.email_cc.split(', ').map((email) => email.trim()).filter((email) => email.length > 0);
            }

            this.$inertia.post(url, {
                email: emails,
                type: 'cc',
            }, {
                preserveScroll: true,
                onSuccess: () => {
                    this.emailsCcLoading = false;
                    this.email_cc = '';
                },
                onError: (res) => {
                    this.emailsCcLoading = false;
                    if (res.email) {
                        alert(res.email);
                    } else {
                        alert('يجب ان يكون البريد صحيح');
                    }
                }
            });
        },
        removeEmail(id) {
          this.$inertia.delete(route('back.reports.company-attendance.remove-email', {report_id: this.report.id, id: id}))
        },
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
        /**
         * Toggle adding/removing a trainee from the report.
         *
         * @param trainee_id
         */
        toggleTrainee(trainee) {
            if(Number(trainee.pivot.active)) {
                this.detachTrainee(this.report.id, trainee.id);
            } else {
                this.attachTrainee(this.report.id, trainee.id);
            }
        },
        attachTrainee(report_id, trainee_id) {
            this.$inertia.post(route('back.reports.company-attendance.attach', this.report.id), {
                trainee_id: trainee_id,
            });
        },
        detachTrainee(report_id, trainee_id) {
            this.$inertia.post(route('back.reports.company-attendance.detach', this.report.id), {
                trainee_id: trainee_id,
            });
        },
    },
}
</script>
