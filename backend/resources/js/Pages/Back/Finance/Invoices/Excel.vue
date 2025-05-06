<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'invoices', link: route('back.finance.invoices.index')},
                    {title_raw: 'تقرير'},
                ]"
            ></breadcrumb-container>

            <template v-if="report_status === 'new'">
                <form @submit.prevent="generateReport">
                <input type="hidden" name="_token" :value="token">

                <div class="grid grid-cols-12 gap-6">
                    <div class="col-span-12 sm:col-span-6 mt-5">
                        <jet-label class="mb-2" for="course_id" :value="$t('words.company')" />
                        <select v-model="form.company_id"
                                name="company_id"
                                id="company_id">
                            <option v-for="company in companies" :key="company.id" :value="company.id">
                                {{ company.name_ar }}
                            </option>
                        </select>
                    </div>

                    <div class="col-span-12 sm:col-span-2 mt-5">
                        <jet-label class="mb-2" for="date_from" :value="$t('words.date-from')" />
                        <input name="date_from" type="date" v-model="form.date_from" class="form-input rounded-md shadow-sm w-full" required>
                    </div>

                    <div class="col-span-12 sm:col-span-2 mt-5">
                        <jet-label class="mb-2" for="date_from" :value="$t('words.date-to')" />
                        <input name="date_to" type="date" v-model="form.date_to" class="form-input rounded-md shadow-sm w-full" required>
                    </div>
                </div>

                <button class="btn btn-gray mt-5" type="submit" :disabled="form.processing">{{ $t('words.export') }}</button>
            </form>
            </template>

            <template v-if="report_status === 'processing'">
                <div class="mt-10">
                    <h1 class="text-center">{{ $t('words.report-work-on-progress') }}</h1>
                    <p class="text-center text-gray-500 mt-2">{{ $t('words.please-dont-close-the-window') }}</p>
                    <p class="text-center text-sm text-gray-400 mt-2" v-if="job_tracker?.started_at">
                        {{ $t('words.started-at') }}: {{ formattedStartTime }}
                    </p>
                    <p class="text-center text-sm text-gray-400 mt-1" v-if="job_tracker?.started_at">
                        {{ $t('words.elapsed-time') }}: {{ durationSinceStart }}
                    </p>
                    <div class="flex justify-center mt-5">
                        <btn-loading-indicator />
                    </div>
                </div>
            </template>

            <template v-if="report_status === 'finished'">
                <div class="mt-10 flex justify-center">
                    <a target="_blank"
                       :href="route('job-trackers.download', this.job_tracker.id)"
                       class="btn btn-gray">
                        {{ $t('words.download-file') }}
                    </a>
                </div>
            </template>

            <template v-if="report_status === 'error'">
                <div class="mt-10 flex justify-center">
                    <p class="text-red-500">{{ $t('words.error-occurred') }}</p>
                </div>
            </template>

        </div>
    </app-layout>
</template>

<script>
    import JetLabel from '@/Jetstream/Label';
    import AppLayout from '@/Layouts/AppLayout'
    import IconNavigate from 'vue-ionicons/dist/ios-arrow-dropright'
    import BreadcrumbContainer from "@/Components/BreadcrumbContainer";
    import BtnLoadingIndicator from "../../../../Components/BtnLoadingIndicator";
    import 'selectize/dist/js/standalone/selectize.min';

    export default {
        props: [
            'companies',
        ],
        metaInfo() {
            return {
                title: this.$t('words.invoices-report'),
            }
        },
        components: {
            IconNavigate,
            AppLayout,
            JetLabel,
            BreadcrumbContainer,
            BtnLoadingIndicator,
        },
        computed: {
            token() {
                return document.head.querySelector('meta[name="csrf-token"]').content;
            },
            formattedStartTime() {
                return new Date(this.job_tracker?.started_at).toLocaleString('ar-EG');
            },
            durationSinceStart() {
                if (!this.job_tracker?.started_at) return '';
                const start = new Date(this.job_tracker.started_at);
                const diffMs = this.now - start;
                const minutes = Math.floor(diffMs / 60000);
                const seconds = Math.floor((diffMs % 60000) / 1000);
                return `${minutes} دقيقة ${seconds} ثانية`;
            }
        },
        mounted() {
            let vm = this;
            $(document).ready(function () {
                vm.companySelector = $('#company_id').selectize({
                    sortField: 'text',
                    maxOptions: 9999,
                    onChange: function (value) {
                        vm.form.company_id = value;
                    }
                })
            });
            setInterval(() => {
                this.now = new Date();
            }, 1000);
        },
        data() {
            return {
                report_status: 'new',
                job_tracker: null,
                form: {
                    company_id: null,
                    processing: false,
                    date_from: new Date().toISOString().substring(0, 10),
                    date_to: new Date().toISOString().substring(0, 10),
                },
                now: new Date(),
            }
        },
        methods: {
            generateReport() {
                this.form.processing = true;
                axios.post(route('back.finance.invoices.excel.generate'), this.form)
                    .then(response => {
                        this.job_tracker = response.data;
                        this.report_status = 'processing';
                        this.form.processing = true;
                        let vm = this;
                        setTimeout(function() {
                            vm.checkJobTracker();
                        }, 2000);
                    })
            },
            checkJobTracker() {
                axios.get(route('job-trackers.show', {id: this.job_tracker.id}))
                    .then(response => {
                        this.job_tracker = response.data;
                        if (response.data.finished_at) {
                            this.report_status = 'finished';
                            this.job_tracker = response.data;
                            return;
                        }

                        if (response.data.failure_reason) {
                            this.report_status = 'error';
                            alert('An error occurred');
                            return;
                        }

                        if (!response.data.completed_at) {
                            let vm = this;
                            setTimeout(function() {
                                vm.checkJobTracker();
                            }, 5000);
                        }
                    })
            },
        },
    }
</script>
