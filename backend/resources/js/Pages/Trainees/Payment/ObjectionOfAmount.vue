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
                    {title: 'payment', link: route('trainees.payment.choose-invoice')},
                ]"
            ></breadcrumb-container>

            <div class="grid md:grid-cols-4 grid-cols-1 gap-6">
                <div class="col-span-1 p-5 transition-all duration-500 ease-in-out hover:bg-gray-200">
                    <form @submit.prevent="submitForm">
                        <div class="payment-options mt-2 mb-4" value="cc">
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
                                disabled>
                            <option selected :value="invoice">{{ $t('words.dues') }} {{ invoice.month_of }} - {{ invoice.grand_total }}</option>
                        </select>
                        <div class="mt-4">
                            <p class="text-xl font-bold">{{ $t('words.amount') }}</p>
                            <input type="text"
                                   class="px-3 py-4 placeholder-slate-300 text-slate-600 relative bg-white bg-white rounded text-base border-0 shadow outline-none focus:outline-none focus:ring w-full"
                                   min="1"
                                   v-model="form.grand_total_override">
                        </div>

                        <button class="mt-5 inline-flex items-center px-4 py-2 bg-red-500 hover:bg-red-700 active:bg-red-900 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150 disabled:cursor-not-allowed mx-">
                            <span>
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
        'invoice',
    ],
    data() {
        return {
            form: {
                invoice_id: null,
                grand_total_override: null,
            },
        }
    },
    mounted() {
        this.form.invoice_id = this.invoice.id;
        this.form.grand_total_override = this.invoice.grand_total;
    },
    methods: {
        submitForm() {
            if(this.form.grand_total_override <= 5000 ) {
                axios.post(route('trainees.override-payment.card'), {
                    invoice_id: this.form.invoice_id,
                    grand_total_override: this.form.grand_total_override,
                })
                    .then(response => {
                        window.location.href = response.data;
                    });
            }else if(confirm(this.$t('words.the-maximum'))){
                return 0;
            }
        }
    }
}
</script>
