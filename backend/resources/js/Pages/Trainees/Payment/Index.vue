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
                ]"
            ></breadcrumb-container>

            <div class="grid md:grid-cols-4 grid-cols-1 gap-6">
                <div class="col-span-1 p-5 transition-all duration-500 ease-in-out hover:bg-gray-200">
                    <form @submit.prevent="submitForm">
                            <label>
                                <input checked="checked" type="radio" name="payment-method" value="bank-transfer" v-model="paymentMethod">
                                {{ $t('words.bank-transfer-upload-receipt') }}
                            </label>
                        <p class="text-xl font-bold mt-8 mb-6" >{{ $t('words.choose-invoice') }}:</p>
                        <select class=" my-4 bg-gray-100 border-2 border-gray-500 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                v-model="invoiceToPay"
                                required>
                            <option selected v-for="invoice in invoices" :value="invoice">{{ $t('words.dues') }} {{ invoice.month_of }} - {{ invoice.grand_total }}</option>
                        </select>
                        <div class="mt-8">
                            <p class="text-xl font-bold">{{ $t('words.amount') }}<p>
                            <p class="text-xl">{{ invoiceToPay ? invoiceToPay.grand_total : '' }} {{ $t('words.sr')}}</p>
                        </div>

                        <button class="mt-5 inline-flex items-center px-4 py-2 bg-red-500 hover:bg-red-700 active:bg-red-900 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150 disabled:cursor-not-allowed mx-">
                          <span v-if="paymentMethod === 'bank-transfer'">
                            {{ $t('words.attach-receipt') }}
                        </span>
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
import Label from "../../../Jetstream/Label";
import Input from "../../../Jetstream/Input";
export default {
    components: {
        Input,
        Label,
        AppLayoutTrainee,
        BreadcrumbContainer,
    },
    props: [
        'pending_amount',
        'online_payment',
        'invoice',
        'invoices',
    ],
    data() {
        return {
            paymentMethod: 'bank-transfer',
            invoiceToPay: null,
        }
    },
    mounted() {
        //
    },
    methods: {
        submitForm(){
            let link = '';

            if (this.paymentMethod === 'cc') {
                link = route('trainees.payment.card', {invoice_id: this.invoiceToPay ? this.invoiceToPay.id : '' });
            } else {
                link = route('trainees.payment.upload-receipt',  {invoice_id: this.invoiceToPay ? this.invoiceToPay.id : '' })
            }

            window.location.replace(link);
        }
    }
}
</script>
