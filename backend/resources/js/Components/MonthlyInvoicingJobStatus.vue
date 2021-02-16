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
        <hr class="my-5">
        <p class="font-bold">{{ $t('words.stages') }}</p>
        <p class="my-2 text-center bg-blue-700 text-white"
           :class="{'opacity-100': batch.finished_generating_draft_invoices, 'opacity-50': !batch.finished_generating_draft_invoices}">
            {{ $t('words.generated-draft-invoices') }}
        </p>
        <p class="my-2 text-center bg-blue-700 text-white"
           :class="{'opacity-100': batch.finishing_issuing_invoices, 'opacity-50': !batch.finishing_issuing_invoices}">
            {{ $t('words.completed-issuing-invoices') }}
        </p>
        <p class="my-2 text-center bg-blue-700 text-white"
           :class="{'opacity-100': batch.finishing_sending_invoices, 'opacity-50': !batch.finishing_sending_invoices}">
            {{ $t('words.completed-sending-invoices') }}
        </p>
    </div>
</template>

<script>
import BtnLoadingIndicator from "./BtnLoadingIndicator";

export default {
    name: "MonthlyInvoicingJobStatus.vue",
    props: ['batch'],
    components: {
        BtnLoadingIndicator,
    },
    data() {
        return {
            form: this.$inertia.form({
                approved: false,
            }, {
                resetOnSuccess: true,
                bag: 'monthlyInvoicingApprove',
            }),
        }
    },
    methods: {
        approveInvoicesBatchRequest() {
            this.form.post(route('back.finance.invoicing.approve', {batch: this.batch.id}))
                .then(() => {
                    this.$refs.approveInvoicesBatch.close();
                }).catch(error => {
                LogRocket.captureException(error);
                alert(error.response.data.message);
                throw error;
            }).finally(() => {
                this.form.processing = false;
            });
        }
    }
}
</script>
