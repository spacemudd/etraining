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
                        <!-- Card Payment Option -->
                        <div class="payment-options mt-2 mb-4" v-if="online_payment">
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" name="payment-method" value="cc" v-model="paymentMethod" class="mr-2">
                                <p class="text-xl font-bold mb-1">{{ $t('words.credit-card-method') }}</p>
                            </label>
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

                        <!-- Tabby Payment Option -->
                        <div class="payment-options mt-4 mb-4" v-if="online_payment && tabbyOptionsAvailable">
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" name="payment-method" value="tabby" v-model="paymentMethod" class="mr-2" @change="onTabbySelected">
                                <p class="text-xl font-bold mb-1">{{ $t('words.tabby-method') }}</p>
                            </label>
                            <div class="mt-2">
                                <img src="https://tabby.ai/images/tabby-logo.svg" alt="Tabby" class="h-8" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                <span style="display:none;" class="text-lg font-semibold text-blue-600">Tabby</span>
                            </div>
                            <!-- Tabby Plans Selection -->
                            <div v-if="paymentMethod === 'tabby' && tabbyPlans.length > 0" class="mt-4">
                                <p class="text-sm font-semibold mb-2">{{ $t('words.choose-tabby-plan') }}:</p>
                                <div v-for="plan in tabbyPlans" :key="plan.productId" class="mb-2">
                                    <label class="flex items-center cursor-pointer p-2 border rounded hover:bg-gray-100">
                                        <input type="radio" :value="plan.productId" v-model="selectedTabbyPlan" class="mr-2">
                                        <div>
                                            <span class="font-semibold">{{ plan.productType === 'INSTALLMENTS' ? $t('words.installments') : plan.productType }}</span>
                                            <span class="text-sm text-gray-600 ml-2" v-if="plan.description">{{ plan.description }}</span>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <div v-if="paymentMethod === 'tabby' && loadingTabbyOptions" class="mt-4 text-sm text-gray-600">
                                {{ $t('words.loading-tabby-options') }}...
                            </div>
                            <div v-if="paymentMethod === 'tabby' && tabbyError" class="mt-4 text-sm text-red-600">
                                {{ tabbyError }}
                            </div>
                        </div>

                        <p class="text-xl font-bold mb-6">{{ $t('words.choose-invoice') }}:</p>
                        <select class=" my-4 bg-gray-100 border-2 border-gray-500 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                v-model="invoiceToPay"
                                @change="onInvoiceChanged"
                                required>
                            <option selected v-for="invoice in invoices" :value="invoice">{{ $t('words.dues') }} {{ invoice.month_of }} - {{ invoice.grand_total }}</option>
                        </select>

                        <div class="mt-4" v-if="invoiceToPay">
                            <p class="text-xl font-bold">{{ $t('words.amount') }}<p>
                            <p class="text-xl">{{ invoiceToPay ? invoiceToPay.grand_total : '' }} {{ $t('words.sr')}}</p>
                        </div>

                        <button 
                            class="mt-10 inline-flex items-center px-4 py-2 bg-red-500 hover:bg-red-700 active:bg-red-900 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150 disabled:cursor-not-allowed mx-"
                            :disabled="paymentMethod === 'tabby' && (!selectedTabbyPlan || loadingTabbyOptions)">
                            <span v-if="paymentMethod === 'cc'">
                                {{ $t('words.pay-now') }}
                            </span>
                            <span v-else-if="paymentMethod === 'tabby'">
                                {{ $t('words.pay-with-tabby') }}
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
import axios from 'axios';

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
            paymentMethod: 'cc',
            invoiceToPay: null,
            tabbyOptionsAvailable: false,
            tabbyPlans: [],
            selectedTabbyPlan: null,
            loadingTabbyOptions: false,
            tabbyError: null,
        }
    },
    mounted() {
        if (this.invoices && this.invoices.length > 0) {
            this.invoiceToPay = this.invoices[0];
            this.checkTabbyOptions();
        }
    },
    methods: {
        async checkTabbyOptions() {
            if (!this.invoiceToPay) {
                return;
            }

            this.loadingTabbyOptions = true;
            this.tabbyError = null;
            this.tabbyPlans = [];

            try {
                const response = await axios.get(route('payment.tabby.check-options'), {
                    params: {
                        invoice_id: this.invoiceToPay.id
                    }
                });

                if (response.data && response.data.resultCode === 0) {
                    const products = response.data.result?.products || [];
                    if (products.length > 0) {
                        this.tabbyOptionsAvailable = true;
                        this.tabbyPlans = products.map(product => ({
                            productId: product.productId,
                            productType: product.productType,
                            description: product.description || `${product.productType} - ${product.productId}`,
                        }));
                        
                        // Auto-select first plan
                        if (this.tabbyPlans.length > 0) {
                            this.selectedTabbyPlan = this.tabbyPlans[0].productId;
                        }
                    } else {
                        this.tabbyOptionsAvailable = false;
                    }
                } else {
                    this.tabbyOptionsAvailable = false;
                    this.tabbyError = response.data?.message || 'No Tabby options available';
                }
            } catch (error) {
                console.error('Tabby options error:', error);
                this.tabbyOptionsAvailable = false;
                this.tabbyError = error.response?.data?.error || 'Failed to load Tabby options';
            } finally {
                this.loadingTabbyOptions = false;
            }
        },
        onInvoiceChanged() {
            this.checkTabbyOptions();
            if (this.paymentMethod === 'tabby') {
                this.selectedTabbyPlan = null;
            }
        },
        onTabbySelected() {
            if (this.tabbyPlans.length > 0 && !this.selectedTabbyPlan) {
                this.selectedTabbyPlan = this.tabbyPlans[0].productId;
            }
        },
        submitForm(){
            let link = '';

            if (this.paymentMethod === 'cc') {
                link = route('trainees.payment.card', {invoice_id: this.invoiceToPay ? this.invoiceToPay.id : '' });
                window.location.replace(link);
            } else if (this.paymentMethod === 'tabby') {
                if (!this.selectedTabbyPlan) {
                    alert(this.$t('words.please-select-tabby-plan'));
                    return;
                }
                
                const selectedPlan = this.tabbyPlans.find(p => p.productId === this.selectedTabbyPlan);
                if (!selectedPlan) {
                    alert(this.$t('words.invalid-tabby-plan'));
                    return;
                }

                // Create form and submit to initiate Tabby payment
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = route('payment.tabby.initiate');
                
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                if (csrfToken) {
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = csrfToken;
                    form.appendChild(csrfInput);
                }

                const invoiceInput = document.createElement('input');
                invoiceInput.type = 'hidden';
                invoiceInput.name = 'invoice_id';
                invoiceInput.value = this.invoiceToPay.id;
                form.appendChild(invoiceInput);

                const productTypeInput = document.createElement('input');
                productTypeInput.type = 'hidden';
                productTypeInput.name = 'product_type';
                productTypeInput.value = selectedPlan.productType;
                form.appendChild(productTypeInput);

                const productIdInput = document.createElement('input');
                productIdInput.type = 'hidden';
                productIdInput.name = 'product_id';
                productIdInput.value = selectedPlan.productId;
                form.appendChild(productIdInput);

                document.body.appendChild(form);
                form.submit();
                return;
            } else {
                link = route('trainees.payment.upload-receipt',  {invoice_id: this.invoiceToPay ? this.invoiceToPay.id : '' })
                window.location.replace(link);
            }
        }
    }
}
</script>
