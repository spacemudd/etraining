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
    <app-layout-trainee>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'payment', link: route('trainees.payment.options')},
                    {title: 'bank-transfer'},
                ]"
            ></breadcrumb-container>

            <div class="grid md:grid-cols-1 grid-cols-1 gap-6 flex justify-center">
                <div class="bg-white shadow-lg p-5 mx-auto">
                    <div class="mb-10">
                        <p class="text-center"><span class="bg-gray-400 text-white rounded p-2">{{ $t('words.bank-transfer') }}</span></p>
                    </div>
                    <div class="bg-yellow-300 p-3 rounded flex">
                        <svg style="margin-left:10px;" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ $t('words.payment-receipt-notice') }}
                    </div>

                    <form class="mt-5" @submit.prevent="saveForm">
                        <div>
                            <jet-label for="amount" :value="$t('words.amount')"/>
                            <jet-input
                                id="amount"
                                type="number"
                                class="mt-1 block w-full"
                                v-model="pending_amount_raw"
                                required
                                autocomplete="off"
                                disabled />
                            <jet-input-error :message="form.error('amount')" class="mt-2" />
                        </div>

                        <div class="mt-3">
                            <jet-label for="sender_name" :value="$t('words.sender-name')"/>
                            <jet-input
                                id="sender_name"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.sender_name"
                                autofocus="autofocus"
                                required
                                autocomplete="off" />
                            <jet-input-error :message="form.error('sender_name')" class="mt-2" />
                        </div>

                        <div class="mt-3">
                            <jet-label for="bank_name_from" :value="$t('words.sender-bank-name')+' '+$t('words.example-bank-name')"/>
                            <jet-input
                                id="bank_name_from"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.bank_name_from"
                                required
                                autocomplete="off" />
                            <jet-input-error :message="form.error('bank_name_from')" class="mt-2" />
                        </div>

                        <div class="mt-3">
                            <jet-label for="bank_name_to" :value="$t('words.receiver-bank-name')+' '+$t('words.example-bank-name')"/>
                            <jet-input
                                id="bank_name_to"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.bank_name_to"
                                required
                                autocomplete="off" />
                            <jet-input-error :message="form.error('bank_name_to')" class="mt-2" />
                        </div>

                        <div class="mt-3">
                            <jet-label for="files" :value="$t('words.upload-receipts')"/>
                            <div class="flex justify-center">
                                <div class="mb-3 w-full">
                                    <input class="form-control block w-full mt-2 px-3 py-1.5 text-base font-normal text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none"
                                           @input="form.files = $event.target.files"
                                           type="file"
                                           id="formFileMultiple"
                                           required
                                           multiple>
                                </div>
                            </div>
                            <jet-input-error :message="form.error('files')" class="mt-2" />
                        </div>


                        <progress v-if="form.progress" :value="form.progress.percentage" max="100">
                            {{ form.progress.percentage }}%
                        </progress>
                        <button v-else
                                class="w-full text-center mt-5 items-center px-4 py-2 bg-blue-500 hover:bg-blue-700 active:bg-blue-900 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150 disabled:cursor-not-allowed mx-"
                                type="submit">
                            {{ $t('words.save') }}
                        </button>
                    </form>


                </div>
            </div>
        </div>
    </app-layout-trainee>
</template>

<script>
import AppLayoutTrainee from '@/Layouts/AppLayoutTrainee'
import BreadcrumbContainer from "@/Components/BreadcrumbContainer";
import JetInput from '@/Jetstream/Input'
import JetInputError from '@/Jetstream/InputError'
import JetLabel from '@/Jetstream/Label';

export default {
    components: {
        AppLayoutTrainee,
        BreadcrumbContainer,
        JetInput,
        JetInputError,
        JetLabel,
    },
    props: [
        'pending_amount',
        'pending_amount_raw',
        'trainee',
        'invoice',
    ],
    data() {
        return {
            form: this.$inertia.form({
                amount: 0,
                sender_name: '',
                bank_name_to: '',
                bank_name_from: '',
                files: '',
            }, {
                bag: 'uploadReceipt',
            })
        }
    },
    mounted() {
        this.form.sender_name = '';
        this.form.amount = this.pending_amount_raw;
    },
    methods: {
        saveForm() {
            this.form.amount = this.pending_amount_raw;
            this.form.post(route('trainees.payment.upload-receipt.store', {invoice_id: this.invoice ? this.invoice.id : ''}));
        },
    }
}
</script>
