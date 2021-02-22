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
    <div class="flex flex-col flex-1 w-full h-screen">
        <main class="h-full">
            <div class="text-center bg-gray-500 p-5 text-white text-xl">
                طلب تحويل من شركة احترافية التدريب
            </div>

            <div style="max-width:900px;margin:auto;">
                <div class="flex justify-between text-center px-5 mt-5">
                    <div>
                        <p class="text-gray-500">الإجمالي</p>
                        {{ saleInvoice.grand_total_display }}
                    </div>
                    <div>
                        <p class="text-gray-500">رمز المرجعي</p>
                        {{ saleInvoice.number }}
                    </div>
                    <div>
                        <p class="text-gray-500">التاريخ</p>
                        {{ saleInvoice.issued_at }}
                    </div>
                </div>

                <hr class="my-5">

                <div class="text-center">
                    <inertia-link :href="'/'">
                        <img src="/img/logo-lg.png" style="max-height:100px;margin:auto;">
                    </inertia-link>
                </div>

                <hr class="my-5">

                <div class="px-5 justify-end flex">
                <table class="text-right">
                <colgroup>
                    <col>
                    <col style="width:120px;">
                </colgroup>
                <tr>
                    <td>{{ saleInvoice.number }}</td>
                    <td>رقم المرجعي</td>
                </tr>
                <tr>
                    <td>{{ saleInvoice.grand_total_display }}</td>
                    <td>الإجمالي</td>
                </tr>
                <tr>
                    <td>{{ saleInvoice.billable.name }}</td>
                    <td>موجه الى</td>
                </tr>
            </table>
            </div>

                <hr class="my-5">

                <p class="text-right px-5">دفع عبر تحويل بنكي</p>

                <div class="flex justify-center">
                    <p class="my-5 bg-yellow-200 text-black text-center p-2 rounded-lg w-56">
                        يرجى ارفاق الوصل مع ذكر البنك المحول اليه و اسم المحول
                    </p>
                </div>

                <!-- Uploading bank receipt -->
                <form class="mt-5 text-center px-5 pb-20" @submit.prevent="submitForm" enctype="multipart/form-data">
                    <div class="text-right mt-5">
                            <jet-label class="font-bold" for="amount_transferred" value="المبلغ المحول" />
                            <jet-input v-model="form.amount_transferred" id="name" type="number" class="mt-1 block w-full" autocomplete="off" required />
                            <jet-input-error :message="form.error('amount_transferred')" class="mt-2" />
                    </div>

                    <div class="text-right mt-5">
                        <jet-label class="font-bold" for="amount_transferred" value="أسم المحول" />
                        <jet-input v-model="form.sender_name" id="sender_name" type="text" class="mt-1 block w-full" autocomplete="off" required />
                        <jet-input-error :message="form.error('sender_name')" class="mt-2" />
                    </div>

                    <div class="text-right mt-5">
                        <jet-label class="font-bold" for="amount_transferred" value="البنك المحول منه" />
                        <jet-input v-model="form.sender_bank" id="sender_bank" type="text" class="mt-1 block w-full" autocomplete="off" required />
                        <jet-input-error :message="form.error('sender_bank')" class="mt-2" />
                    </div>

                    <label class="mt-5 mx-auto w-64 flex flex-col items-center px-4 py-6 bg-white text-blue rounded-lg shadow-lg border border-blue cursor-pointer">
                        <svg class="w-8 h-8" fill="#1c64f2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M16.88 9.1A4 4 0 0 1 16 17H5a5 5 0 0 1-1-9.9V7a3 3 0 0 1 4.52-2.59A4.98 4.98 0 0 1 17 8c0 .38-.04.74-.12 1.1zM11 11h3l-4-4-4 4h3v3h2v-3z" />
                        </svg>

                        <span v-if="bank_receipt" class="mt-2 text-base leading-normal text-center">
                                ✅<br/>
                                <div class="text-sm text-gray-500 mt-5" v-if="bank_receipt.name">{{ bank_receipt.name }}</div>
                            </span>
                        <span v-else class="mt-2 text-base leading-normal">
                            قم برفع إيصال التحويل
                        </span>

                        <input type='file'
                               class="hidden"
                               :ref="'bank_receipt'"
                               @change="uploadFile($event)"
                               name="bank_receipt"
                               required />
                    </label>

                    <button class="mt-5 text-sm inline-flex items-center rounded-md px-4 py-2 bg-blue-600 hover:bg-blue-800 text-white">
                            ارسال
                    </button>
                </form>
            </div>
        </main>
    </div>
</template>

<script>
import JetInput from '@/Jetstream/Input'
import JetInputError from '@/Jetstream/InputError'
import JetButton from '@/Jetstream/Button';
import JetLabel from '@/Jetstream/Label';
export default {
    components: {
        JetInput,
        JetInputError,
        JetButton,
        JetLabel,
    },
    props: ['saleInvoice'],
    name: "Show.vue",
    data() {
        return {
            bank_receipt: null,
            formData: new FormData(),
            form: this.$inertia.form({
                amount_transferred: '',
                sender_name: '',
                sender_bank: '',
            }, {
                bag: 'bankTransfer',
            })
        }
    },
    methods: {
        uploadFile(e) {
            this.bank_receipt = e.target.files[0];
        },
        submitForm() {
            this.formData = new FormData();
            this.formData.append('bank_receipt', this.bank_receipt);
            this.formData.append('amount_transferred', this.form.amount_transferred);
            this.formData.append('sender_name', this.form.sender_name);
            this.formData.append('sender_bank', this.form.sender_bank);
            axios.post(route('sale-invoices.pay.bank-transfer.transfer-receipt', this.saleInvoice.id), this.formData)
                .then(response => {
                    window.location = route('sale-invoices.show', this.saleInvoice.id);
                });
        },
    }
}
</script>
