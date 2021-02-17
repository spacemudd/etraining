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
    <div class="inline-block">
        <button @click="$refs.approveInvoicesBatch.open()"
                class="inline-flex items-center rounded-md px-4 py-2 bg-blue-600 hover:bg-blue-800 text-white text-right">
            {{ $t('words.approve-invoices') }}
        </button>

        <sweet-modal ref="approveInvoicesBatch">
            <form ref="approveInvoicesBatchForm" @submit.prevent="approveInvoicesBatchRequest">
                <div class="grid grid-cols-3">
                    <div class="col-span-3 lg:col-span-1">
                        <img src="/img/approval.svg" class="w-28 m-auto mt-8">
                    </div>
                    <div class="col-span-2 text-right pt-5">
                        بعد اعتماد الفواتير، المنصة تبدأ بإصدار الفواتير وإرسالها الى المتدربين. هل انت متأكد من البدء الآن؟ علماً ان العملية لا يمكن إيقافها بعد البدء.

                        <br/>
                        <br/>
                        <br/>

                        <label class="flex">
                            <input type="checkbox" class="form-checkbox" v-model="form.approved">
                            <span class="ml-2 rtl:mr-2 text-sm text-gray-600">أقر انني تأكدت من صحة الفواتير (المسودة) وهي جاهزة للإرسال للمتدربات</span>
                        </label>
                    </div>
                </div>
            </form>
            <div slot="button">
                <button @click="approveInvoicesBatchRequest"
                        class="btn-primary"
                        :class="{ 'opacity-25': form.processing || !form.approved }"
                        :disabled="form.processing || !form.approved">
                    <btn-loading-indicator v-if="form.processing" />
                    {{ $t('words.start-sending-invoices') }}
                </button>
            </div>
        </sweet-modal>
    </div>
</template>

<script>
import { SweetModal, SweetModalTab } from 'sweet-modal-vue';
import JetInput from '@/Jetstream/Input'
import JetInputError from '@/Jetstream/InputError'
import JetLabel from '@/Jetstream/Label';
import BtnLoadingIndicator from "./BtnLoadingIndicator";
import LogRocket from 'logrocket';

export default {
    name: "ApproveInvoicesBatch.vue",
    props: ['batch'],
    components: {
        SweetModal,
        SweetModalTab,
        JetInput,
        JetInputError,
        JetLabel,
        BtnLoadingIndicator,
    },
    data() {
        return {
            form: this.$inertia.form({
                approved: false,
            }, {
                resetOnSuccess: true,
                bag: 'changingTraineePassword',
            }),
        }
    },
    methods: {
        approveInvoicesBatchRequest() {
            axios.post(route('back.finance.invoicing.approve', {batch: this.batch.id}), {
                approved: this.form.approved,
            })
                .then(() => {
                    this.$emit('refreshBatch');
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
