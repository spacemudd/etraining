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
                        <label>
                            <input type="radio" name="payment-method" value="cc" v-model="paymentMethod">
                            {{ $t('words.credit-card-method') }}
                            <span class="img {display:block} inline-flex">
                                        <svg width="40" height="40" class="mx-0.5">
                                            <image class=inline xlink:href="https://www.svgrepo.com/show/328112/visa.svg" src="https://www.svgrepo.com/show/328112/visa.svg" width="40" height="40"/>
                                        </svg>
                                        <svg width="20" height="40" class="mx-0.5">
                                            <image class=inline xlink:href="https://www.svgrepo.com/show/163750/mastercard.svg" src="https://www.svgrepo.com/show/163750/mastercard.svg" width="20" height="40"/>
                                        </svg>
                                        <svg width="40" height="40" class="mx-0.5">
                                            <image class=inline xlink:href="https://upload.wikimedia.org/wikipedia/commons/f/fb/Mada_Logo.svg" src="https://upload.wikimedia.org/wikipedia/commons/f/fb/Mada_Logo.svg" width="40" height="40"/>
                                        </svg>
                                        <svg width="40" height="40" class="mx-0.5">
                                            <image class=inline xlink:href="https://www.svgrepo.com/show/303191/apple-pay-logo.svg" src="https://www.svgrepo.com/show/303191/apple-pay-logo.svg" width="40" height="40"/>
                                        </svg>
                                    </span>
                        </label>
                    </div>
                    <div class="payment-options">
                        <label>
                            <input type="radio" name="payment-method" value="bank-transfer" v-model="paymentMethod">
                            {{ $t('words.bank-transfer-upload-receipt') }}
                        </label>
                    </div>

                    <div class="mt-5">
                        <p>{{ $t('words.amount') }}<p>
                        <p class="text-2xl">{{ pending_amount }}</p>
                    </div>

                    <a class="mt-5 inline-flex items-center px-4 py-2 bg-red-500 hover:bg-red-700 active:bg-red-900 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150 disabled:cursor-not-allowed mx-"
                       :href="paymentMethod==='cc' ? route('trainees.payment.card') : route('trainees.payment.upload-receipt')">
                        <span v-if="paymentMethod === 'cc'">
                            {{ $t('words.pay-now') }}
                        </span>
                        <span v-else>
                            {{ $t('words.attach-receipt') }}
                        </span>
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
            paymentMethod: 'cc',
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
