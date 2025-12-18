<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6 space-y-6">
            <!-- Breadcrumb -->
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'reports', link: route('back.reports.index')},
                    {title: 'course-attendances', link: route('back.reports.course-attendances.index')}
                ]"
            ></breadcrumb-container>

            <!-- Form Section -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold mb-4">Generate Report</h2>

                <template v-if="report_status === 'new'">
                    <form @submit.prevent="generateReport" class="space-y-4">
                        <!-- Courses Select -->
                        <div>
                            <label for="courseId" class="block text-sm font-medium text-gray-700 mb-1">Select Course</label>
                            <v-select
                                id="courseId"
                                v-model="form.courseId"
                                :options="courses"
                                label="name_ar"
                                placeholder="Select a course"
                                required
                                searchable
                                :multiple="true"
                                class="block w-full mt-1"
                            />
                        </div>

                        <!-- Companies Select -->
                        <div>
                            <label for="companyId" class="block text-sm font-medium text-gray-700 mb-1">Select Company</label>
                            <v-select
                                id="companyId"
                                v-model="form.companyId"
                                :options="companies"
                                label="name_ar"
                                placeholder="Select a company"
                                required
                                searchable
                                :multiple="true"
                                class="block w-full mt-1"
                            />
                        </div>

                        <!-- Submit Button -->
                        <div>
                            <button
                                type="submit"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                :disabled="form.processing"
                            >
                                <span v-if="!form.processing">Generate Report</span>
                                <span v-else>
                                    <BtnLoadingIndicator />
                                    Processing...
                                </span>
                            </button>
                        </div>
                    </form>
                </template>

                <template v-if="report_status === 'processing'">
                    <div class="text-center py-10">
                        <h1 class="text-center">{{ $t('words.report-work-on-progress') }}</h1>
                        <p class="text-center text-gray-500 mt-2">{{ $t('words.please-dont-close-the-window') }}</p>
                        <div class="flex justify-center mt-5">
                            <BtnLoadingIndicator />
                        </div>
                    </div>
                </template>

                <template v-if="report_status === 'finished'">
                    <div class="mt-10 flex justify-center">
                        <a target="_blank"
                           :href="route('job-trackers.download', job_tracker.id)"
                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            إصدار الآن
                        </a>
                    </div>
                </template>

                <template v-if="report_status === 'error'">
                    <div class="mt-10 flex justify-center">
                        <p class="text-red-500">{{ $t('words.error-occurred') }}</p>
                    </div>
                </template>
            </div>
        </div>
    </app-layout>
</template>

<script>
import JetLabel from '@/Jetstream/Label';
import AppLayout from '@/Layouts/AppLayout'
import IconNavigate from 'vue-ionicons/dist/ios-arrow-dropright'
import BreadcrumbContainer from "@/Components/BreadcrumbContainer";
import BtnLoadingIndicator from "../../../../Components/BtnLoadingIndicator";
import vSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';


export default {
    props: [
        'courses',
        'companies',
    ],
    components: {
        IconNavigate,
        AppLayout,
        JetLabel,
        BreadcrumbContainer,
        BtnLoadingIndicator,
        vSelect,
    },
    data() {
        return {
            report_status: 'new',
            job_tracker: null,
            form: {
                courseId: null,
                companyId: [],
                processing: false,
            },
        }
    },
    methods: {
        generateReport() {
            this.form.processing = true;

            axios.post(route('reports.company-certificates.generate'), this.form)
                .then(response => {
                    this.job_tracker = response.data;
                    this.report_status = 'processing';
                    this.form.processing = true;
                    let vm = this;
                    setTimeout(function() {
                        vm.checkJobTracker();
                    }, 2000);
                })
                .catch(error => {
                    this.form.processing = false;
                    this.report_status = 'new';
                    console.error('Error:', error);
                    alert('حدث خطأ أثناء إرسال الطلب. الرجاء المحاولة لاحقًا.');
                });
        },
        checkJobTracker() {
            axios.get(route('job-trackers.show', {id: this.job_tracker.id}))
                .then(response => {
                    if (response.data.finished_at) {
                        this.report_status = 'finished';
                        this.job_tracker = response.data;
                        this.form.processing = false;
                        return;
                    }

                    if (response.data.failure_reason) {
                        this.report_status = 'error';
                        this.form.processing = false;
                        return;
                    }

                    // Continue polling if not finished
                    let vm = this;
                    setTimeout(function() {
                        vm.checkJobTracker();
                    }, 2000);
                })
                .catch(error => {
                    console.error('Error checking job tracker:', error);
                    this.report_status = 'error';
                    this.form.processing = false;
                });
        }
    }
}
</script>

<style scoped>
@import 'vue-select/dist/vue-select.css';
</style>
