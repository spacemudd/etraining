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
        <div class="container px-6 mx-auto grid pt-6" v-if="user.trainee.deleted_at">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'payment', link: route('trainees.payment.choose-invoice')},
                ]"
            ></breadcrumb-container>
            <div class="container px-6 mx-auto grid" v-if="user.trainee.deleted_at">
                <div class="container mx-auto grid">
                    <div class="bg-red-100 rounded-lg p-10 border-red-500 border-2">
                        <div style="width: 100%;">
                            <p class="mt-2 text-gray-500" style="text-align: center;
                                font-size: 20px;
                                color: #323232;
                                letter-spacing: 1px;
                            }">لا يمكنك حضور الدورات في الوقت الحالي، للتواصل معنا على الأرقام التالية.
                                <br><br/>
                                <span class="img {display:block} center-block inline-flex" style="text-align: center;">
                                <svg width="40" height="40" class="mx-0.5 mt-1">
                                    <image class="inline" xlink:href="https://i.ibb.co/CWK3g2s/whatsapp.png" src="https://i.ibb.co/CWK3g2s/whatsapp.png" width="15" height="15"/>
                                </svg>
                            <a class="center text-green-600 text-sm" style="text-align: center;" href="https://api.whatsapp.com/send?phone=966553139979"> &nbsp;  شؤون المتدربات </a>
                            </span>
                                <span class="img {display:block} center-block inline-flex" style="text-align: center;">
                                <svg width="40" height="40" class="mx-0.5 mt-1">
                                    <image class="inline" xlink:href="https://i.ibb.co/CWK3g2s/whatsapp.png" src="https://i.ibb.co/CWK3g2s/whatsapp.png" width="15" height="15"/>
                                </svg>
                            <a class="center text-green-600 text-sm" style="text-align: center;" href="https://api.whatsapp.com/send?phone=966553139979"> &nbsp;  إدارة المعهد </a>
                            </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container mx-auto grid p-6" v-if="user.trainee.has_outstanding_amount">
                <div class="bg-blue-100 rounded-lg p-10 border-blue-500 border-2">
                    <p class="text-gray-600 flex">
                        <svg style="margin-left:10px;" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ $t('words.due-balance-notice') }}
                    </p>
                    <div class="grid grid-cols-2 gap-6 mt-4">
                        <div>

                            <p class="text-red-600 flex mr-0.5">
                                <svg style="margin-left:10px;" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ $t('words.to-pay-by-credit') }}
                            </p>

                        </div>
                        <div>
                            <!--<p class="text-black flex mr-0.5">-->
                            <!--    <b>2)</b> &ensp; {{ $t('words.to-pay-by-bank') }}-->
                            <!--</p>-->
                        </div>
                    </div>
                </div>
            </div>
            <div class="grid md:grid-cols-4 grid-cols-1 gap-6">
                <div class="col-span-1 p-5 transition-all duration-500 ease-in-out hover:bg-gray-200">
                    <form @submit.prevent="submitForm">
                        <div class="payment-options mt-2 mb-4" value="cc" >
                            <p class="text-xl font-bold mb-1">{{ $t('words.credit-card-method') }}</p>
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
                        </div>
                        <p class="text-xl font-bold mb-6">{{ $t('words.choose-invoice') }}:</p>
                        <select class=" my-4 bg-gray-100 border-2 border-gray-500 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                v-model="invoiceToPay"
                                required>
                            <option selected v-for="invoice in invoices" :value="invoice">{{ $t('words.dues') }} {{ invoice.month_of }} - {{ invoice.grand_total }}</option>
                        </select>

                        <div class="mt-4" v-if="invoiceToPay">
                            <p class="text-xl font-bold">{{ $t('words.amount') }}<p>
                            <p class="text-xl">{{ invoiceToPay ? invoiceToPay.grand_total : '' }} {{ $t('words.sr')}}</p>

                            <inertia-link
                                class="text-blue-600"
                                target="_blank"
                                :href="route('trainees.payment.objection', {invoice_id: this.invoiceToPay ? this.invoiceToPay.id : '' })">
                                دفع مبلغ آخر
                            </inertia-link>
                        </div>

                        <button class="mt-10 inline-flex items-center px-4 py-2 bg-red-500 hover:bg-red-700 active:bg-red-900 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150 disabled:cursor-not-allowed mx-">
                        <span v-if="paymentMethod === 'cc'">
                            {{ $t('words.pay-now') }}
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
        'user',
        'pending_amount',
        'online_payment',
        'invoice',
        'invoices',
    ],
    data() {
        return {
            paymentMethod: 'cc',
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
