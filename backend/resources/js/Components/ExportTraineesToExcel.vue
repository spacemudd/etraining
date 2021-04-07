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
    <div>
        <div @click="openModal">
            <slot name="buttonContent"></slot>
        </div>

        <portal to="app-modal-container">
            <modal name="exportTraineesToExcelSheet" classes="force-overflow-auto">
                <div class="bg-white block h-5 p-10">
                    <h1 class="text-lg font-bold">{{ $t('words.export-trainees') }}</h1>

                    <!-- Form -->
                    <template v-if="report_status === 'new'">
                        <div class="mt-5">
                            <label class="inline-flex items-center mt-3">
                                <span>{{ $t('words.status') }}</span>
                                <div class="mx-2">
                                    <select name="status_id"
                                            v-model="sendReportRequestForm.trainee_status_id"
                                            class="mt-1 block w-full border py-2.5 px-4 pr-8 rounded leading-tight focus:outline-none">
                                        <option :value="null" selected>{{ $t('words.all') }}</option>
                                        <option value="0">{{ $t('words.incomplete-application') }}</option>
                                        <option value="2">{{ $t('words.nominated') }}</option>
                                        <option value="1">{{ $t('words.approved') }}</option>
                                    </select>
                                </div>
                            </label>
                        </div>
                        <div class="mt-15">
                            <jet-button @click.native="sendReportRequest"
                                        :class="{ 'opacity-25': sendReportRequestForm.processing }"
                                        :disabled="sendReportRequestForm.processing">
                                {{ $t('words.submit') }}
                            </jet-button>
                        </div>
                    </template>

                    <template v-if="report_status === 'processing'">
                        <div class="mt-10">
                            <h1 class="text-center">{{ $t('words.report-work-on-progress') }}</h1>
                            <p class="text-center text-gray-500 mt-2">{{ $t('words.please-dont-close-the-window') }}</p>
                            <div class="flex justify-center mt-5">
                                <btn-loading-indicator />
                            </div>
                        </div>
                    </template>

                    <template v-if="report_status === 'finished'">
                        <div class="mt-10 flex justify-center">
                            <a target="_blank"
                               :href="route('back.trainees.excel.job.download', this.excelJob.id)"
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
            </modal>
        </portal>
    </div>
</template>

<script>
import DialogModal from "../Jetstream/DialogModal";
import JetButton from "../Jetstream/Button";
import BtnLoadingIndicator from "./BtnLoadingIndicator";

export default {
    name: "ExportTraineesToExcel",
    components: {
        DialogModal,
        JetButton,
        BtnLoadingIndicator,
    },
    data() {
        return {
            report_status: 'new',
            excelJob: null,
            sendReportRequestForm: {
                processing: false,
                trainee_status_id: null,
            },
        }
    },
    mounted() {

    },
    methods: {
        openModal() {
            this.$emit('modal:opened');
            this.report_status = 'new';
            this.excelJob = null;
            this.$modal.toggle('exportTraineesToExcelSheet');
        },
        checkExcelJobStatus() {
            axios.get(route('back.trainees.excel.job', {id: this.excelJob.id}))
                .then(response => {
                    if (response.data.finished_at) {
                        this.report_status = 'finished';
                        this.excelJob = response.data;
                        return;
                    }

                    if (response.data.failure_reason) {
                        this.report_status = 'error';
                        alert('An error occurred');
                        return;
                    }

                    if (!response.data.completed_at) {
                        setTimeout(this.checkExcelJobStatus(), 10000);
                    }
            })
        },
        sendReportRequest() {
            this.processing = true;
            axios.post(route('back.trainees.excel'), this.sendReportRequestForm)
            .then(response => {
                this.report_status = 'processing';
                this.excelJob = response.data;
                this.processing = false;
                this.checkExcelJobStatus();
            })
        },
    },
}
</script>

<style scoped>

</style>
