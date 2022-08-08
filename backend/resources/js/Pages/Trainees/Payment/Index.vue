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
                    <p class="text-2xl">{{ $t('words.choose-payment-method') }}:</p>
                    <div class="payment-options mt-2" v-if="online_payment">
                        <input type="radio" name="payment-method" value="cc" v-model="paymentMethod">
                        <label>{{ $t('words.credit-card-method') }}</label>
                    </div>
                    <div class="payment-options">
                        <input type="radio" name="payment-method" value="bank-transfer" v-model="paymentMethod">
                        <label>{{ $t('words.bank-transfer-upload-receipt') }}</label>
                    </div>

                    <div class="mt-5">
                        <p>{{ $t('words.amount') }}<p>
                        <p class="text-2xl">{{ pending_amount }}</p>
                    </div>

                    <a class="mt-5 inline-flex items-center px-4 py-2 bg-red-500 hover:bg-red-700 active:bg-red-900 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150 disabled:cursor-not-allowed mx-"
                       :href="paymentMethod==='cc' ? route('trainees.payment.card') : route('trainees.payment.upload-receipt')">
                        {{ $t('words.attach-now') }}
                    </a>
                </div>
            </div>
        </div>
    </app-layout-trainee>
</template>

<script>
import AppLayoutTrainee from '@/Layouts/AppLayoutTrainee'
import BreadcrumbContainer from "@/Components/BreadcrumbContainer";
export default {
    components: {
        AppLayoutTrainee,
        BreadcrumbContainer,
    },
    props: [
        'pending_amount',
        'online_payment',
    ],
    data() {
        return {
            paymentMethod: 'bank-transfer',
        }
    },
    mounted() {
        //
    },
    methods: {
        //
    }
}
</script>
