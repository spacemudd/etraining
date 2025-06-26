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
                    {title: 'trainees-report'},
                ]"
            ></breadcrumb-container>

            <div class="w-full overflow-hidden rounded-lg shadow-xs mb-8">
                <div class="w-full overflow-x-auto bg-white p-4">
                    <template v-if="report_status === 'new'">
                        <form @submit.prevent="generateReport" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <!-- Age Under -->
                            <div>
                                <label class="block text-sm">
                                    <span class="text-gray-700">{{ $t('words.age-under') }}</span>
                                    <input
                                        type="number"
                                        v-model="form.age_under"
                                        min="1"
                                        max="100"
                                        class="block w-full mt-1 text-sm border-gray-300 rounded-md focus:border-purple-400 focus:outline-none focus:shadow-outline-purple form-input"
                                        placeholder="30"
                                    />
                                </label>
                            </div>

                            <!-- Has Invoices -->
                            <div>
                                <label class="block text-sm">
                                    <span class="text-gray-700">{{ $t('words.has-invoices') }}</span>
                                    <select
                                        v-model="form.has_invoices"
                                        class="block w-full mt-1 text-sm border-gray-300 rounded-md focus:border-purple-400 focus:outline-none focus:shadow-outline-purple form-select"
                                    >
                                        <option value="">{{ $t('words.please-select') }}</option>
                                        <option value="yes">{{ $t('words.yes') }}</option>
                                        <option value="no">{{ $t('words.no') }}</option>
                                    </select>
                                </label>
                            </div>

                            <!-- Currently Assigned to Company -->
                            <div>
                                <label class="block text-sm">
                                    <span class="text-gray-700">{{ $t('words.currently-assigned-to-company') }}</span>
                                    <select
                                        v-model="form.assigned_to_company"
                                        class="block w-full mt-1 text-sm border-gray-300 rounded-md focus:border-purple-400 focus:outline-none focus:shadow-outline-purple form-select"
                                    >
                                        <option value="">{{ $t('words.please-select') }}</option>
                                        <option value="yes">{{ $t('words.yes') }}</option>
                                        <option value="no">{{ $t('words.no') }}</option>
                                    </select>
                                </label>
                            </div>

                            <!-- Current Status -->
                            <div>
                                <label class="block text-sm">
                                    <span class="text-gray-700">{{ $t('words.current-status') }}</span>
                                    <select
                                        v-model="form.status"
                                        class="block w-full mt-1 text-sm border-gray-300 rounded-md focus:border-purple-400 focus:outline-none focus:shadow-outline-purple form-select"
                                    >
                                        <option value="">{{ $t('words.please-select') }}</option>
                                        <option value="0">{{ $t('words.incomplete-application') }}</option>
                                        <option value="1">{{ $t('words.nominated-instructor') }}</option>
                                        <option value="2">{{ $t('words.approved') }}</option>
                                    </select>
                                </label>
                            </div>

                            <!-- Owns Phone Number -->
                            <div>
                                <label class="block text-sm">
                                    <span class="text-gray-700">{{ $t('words.owns-phone-number') }}</span>
                                    <select
                                        v-model="form.phone_is_owned"
                                        class="block w-full mt-1 text-sm border-gray-300 rounded-md focus:border-purple-400 focus:outline-none focus:shadow-outline-purple form-select"
                                    >
                                        <option value="">{{ $t('words.please-select') }}</option>
                                        <option value="true">{{ $t('words.yes') }}</option>
                                        <option value="false">{{ $t('words.no') }}</option>
                                    </select>
                                </label>
                            </div>

                            <!-- Educational Level -->
                            <div>
                                <label class="block text-sm">
                                    <span class="text-gray-700">{{ $t('words.educational_level') }}</span>
                                    <select
                                        v-model="form.educational_level_id"
                                        class="block w-full mt-1 text-sm border-gray-300 rounded-md focus:border-purple-400 focus:outline-none focus:shadow-outline-purple form-select"
                                    >
                                        <option value="">{{ $t('words.please-select') }}</option>
                                        <option v-for="level in educational_levels" :key="level.id" :value="level.id">
                                            {{ $page.props.locale === 'ar' ? level.name_ar : level.name_en }}
                                        </option>
                                    </select>
                                </label>
                            </div>

                            <!-- Deleted Mark -->
                            <div>
                                <label class="block text-sm">
                                    <span class="text-gray-700">{{ $t('words.deleted-mark') }}</span>
                                    <select
                                        v-model="form.deleted_mark"
                                        class="block w-full mt-1 text-sm border-gray-300 rounded-md focus:border-purple-400 focus:outline-none focus:shadow-outline-purple form-select"
                                    >
                                        <option value="">{{ $t('words.please-select') }}</option>
                                        <option v-for="mark in deleted_marks" :key="mark" :value="mark">
                                            {{ mark }}
                                        </option>
                                    </select>
                                </label>
                            </div>

                            <!-- Generate Button -->
                            <div class="col-span-1 md:col-span-2 lg:col-span-3">
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple disabled:opacity-50"
                                >
                                    {{ form.processing ? $t('words.please-wait-report-generating') : $t('words.generate-report') }}
                                </button>
                            </div>
                        </form>
                    </template>

                    <template v-if="report_status === 'processing'">
                        <div class="text-center py-8">
                            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600"></div>
                            <p class="mt-4 text-gray-600">{{ $t('words.please-wait-report-generating') }}</p>
                            <div v-if="job_tracker && job_tracker.started_at" class="mt-4">
                                <div class="bg-gray-200 rounded-full h-2 w-full max-w-md mx-auto">
                                    <div class="bg-purple-600 h-2 rounded-full transition-all duration-300" :style="`width: ${realProgress}%`"></div>
                                </div>
                                <p class="text-sm text-gray-500 mt-2">
                                    {{ realProgress }}% {{ $t('words.completed') }}
                                    <span v-if="job_tracker.total_records" class="text-xs text-gray-400 block">
                                        ({{ job_tracker.processed_records || 0 }} / {{ job_tracker.total_records }} {{ $t('words.records') }})
                                    </span>
                                </p>
                            </div>
                        </div>
                    </template>

                    <template v-if="report_status === 'finished'">
                        <div class="text-center py-8">
                            <div class="text-green-600 text-6xl mb-4">✓</div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $t('words.report-completed') }}</h3>
                            <p class="text-gray-600 mb-4">{{ $t('words.report-ready-for-download') }}</p>
                            <a
                                :href="route('job-trackers.download', job_tracker.id)"
                                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:shadow-outline-green disabled:opacity-25 transition ease-in-out duration-150"
                            >
                                {{ $t('words.download-report') }}
                            </a>
                        </div>
                    </template>

                    <template v-if="report_status === 'error'">
                        <div class="text-center py-8">
                            <div class="text-red-600 text-6xl mb-4">✗</div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $t('words.report-failed') }}</h3>
                            <p class="text-gray-600 mb-4">{{ $t('words.please-try-again') }}</p>
                            <button
                                @click="resetForm"
                                class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 active:bg-purple-900 focus:outline-none focus:border-purple-900 focus:shadow-outline-purple disabled:opacity-25 transition ease-in-out duration-150"
                            >
                                {{ $t('words.try-again') }}
                            </button>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import BreadcrumbContainer from '@/Components/BreadcrumbContainer'

export default {
    metaInfo() {
        return {
            title: this.$t('words.trainees-report')
        }
    },
    components: {
        AppLayout,
        BreadcrumbContainer,
    },
    props: {
        educational_levels: Array,
        deleted_marks: Array,
    },
    data() {
        return {
            report_status: 'new', // new, processing, finished, error
            job_tracker: null,
            form: {
                processing: false,
                age_under: '',
                has_invoices: '',
                assigned_to_company: '',
                status: '',
                phone_is_owned: '',
                educational_level_id: '',
                deleted_mark: '',
            }
        }
    },
    methods: {
        generateReport() {
            this.form.processing = true;
            axios.post(route('back.reports.trainees.generate'), this.form)
                .then(response => {
                    this.job_tracker = response.data;
                    this.report_status = 'processing';
                    let vm = this;
                    setTimeout(function() {
                        vm.checkJobTracker();
                    }, 2000);
                })
                .catch(error => {
                    this.form.processing = false;
                    this.report_status = 'error';
                    console.error('Error generating report:', error);
                });
        },
        checkJobTracker() {
            if (!this.job_tracker) return;
            
            axios.get(route('job-trackers.show', {id: this.job_tracker.id}))
                .then(response => {
                    this.job_tracker = response.data;
                    
                    if (response.data.finished_at) {
                        this.report_status = 'finished';
                        this.form.processing = false;
                        return;
                    }

                    if (response.data.failure_reason) {
                        this.report_status = 'error';
                        this.form.processing = false;
                        return;
                    }

                    // Continue checking if not finished
                    if (!response.data.finished_at && !response.data.failure_reason) {
                        let vm = this;
                        setTimeout(function() {
                            vm.checkJobTracker();
                        }, 2000); // Check more frequently for better UX
                    }
                })
                .catch(error => {
                    this.report_status = 'error';
                    this.form.processing = false;
                    console.error('Error checking job tracker:', error);
                });
        },
        resetForm() {
            this.report_status = 'new';
            this.form.processing = false;
            this.job_tracker = null;
        }
    },
    computed: {
        realProgress() {
            if (!this.job_tracker) return 0;
            
            // Use the actual progress percentage from the database
            if (this.job_tracker.progress_percentage !== undefined) {
                return Math.round(this.job_tracker.progress_percentage);
            }
            
            // Fallback calculation if progress_percentage is not available
            if (this.job_tracker.total_records && this.job_tracker.processed_records) {
                return Math.round((this.job_tracker.processed_records / this.job_tracker.total_records) * 100);
            }
            
            return 0;
        }
    }
}
</script> 