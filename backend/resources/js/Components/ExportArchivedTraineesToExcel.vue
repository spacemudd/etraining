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
            <modal name="exportArchivedTraineesToExcelSheet" classes="force-overflow-auto">
                <div class="bg-white block h-5 p-10">
                    <h1 class="text-lg font-bold">{{ $t('words.export-archived-trainees') }}</h1>

                    <!-- Form -->
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
    name: "ExportArchivedTraineesToExcel",
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
                processing: true,
            },
        }
    },
    mounted() {

    },
    methods: {
        openModal() {
            this.$emit('modal:opened');
            this.report_status = 'processing';
            this.excelJob = null;
            this.$modal.toggle('exportArchivedTraineesToExcelSheet');

            //
            this.sendReportRequestForm.processing = true;
            axios.post(route('back.trainees.archived.excel'))
            .then(response => {
                this.report_status = 'processing';
                this.excelJob = response.data;
                this.sendReportRequestForm.processing = false;
                this.checkExcelJobStatus();
            });
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
                        let vm = this;
                        setTimeout(function() {
                            vm.checkExcelJobStatus();
                        }, 5000);
                    }
            })
        },
    },
}
</script>
s
