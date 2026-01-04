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
                        <div class="payment-options mt-4 mb-4" v-if="(online_payment || showTabbyForTesting) && (tabbyOptionsAvailable || showTabbyForTesting)">
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
                            <!-- Show message if no plans available but Tabby is shown for testing -->
                            <div v-if="paymentMethod === 'tabby' && tabbyPlans.length === 0 && !loadingTabbyOptions && showTabbyForTesting" class="mt-4 text-sm text-yellow-600">
                                {{ $t('words.tabby-no-plans-available') }}
                            </div>
                            <div v-if="paymentMethod === 'tabby' && loadingTabbyOptions" class="mt-4 text-sm text-gray-600">
                                {{ $t('words.loading-tabby-options') }}...
                            </div>
                            <div v-if="paymentMethod === 'tabby' && tabbyError && !loadingTabbyOptions" class="mt-4 text-sm text-red-600">
                                <strong>{{ $t('words.error') }}:</strong> {{ tabbyError }}
                                <button @click="checkTabbyOptions" class="ml-2 text-blue-600 underline text-xs">
                                    {{ $t('words.retry') }}
                                </button>
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
            showTabbyForTesting: false, // Set to true for testing even if API fails
        }
    },
    watch: {
        tabbyOptionsAvailable(newVal) {
            // #region agent log
            fetch('http://127.0.0.1:7244/ingest/06d35c0e-7279-47b0-bac0-fd169f82d83d',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'IndexTap.vue:watch:tabbyOptionsAvailable',message:'Tabby availability changed',data:{newValue:newVal,onlinePayment:this.online_payment,shouldShow:this.online_payment&&(newVal||this.showTabbyForTesting)},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'A'})}).catch(()=>{});
            // #endregion
        },
        online_payment(newVal) {
            // #region agent log
            fetch('http://127.0.0.1:7244/ingest/06d35c0e-7279-47b0-bac0-fd169f82d83d',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'IndexTap.vue:watch:online_payment',message:'Online payment changed',data:{newValue:newVal,tabbyAvailable:this.tabbyOptionsAvailable,shouldShow:newVal&&(this.tabbyOptionsAvailable||this.showTabbyForTesting)},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'A'})}).catch(()=>{});
            // #endregion
        }
    },
    mounted() {
        // #region agent log
        fetch('http://127.0.0.1:7244/ingest/06d35c0e-7279-47b0-bac0-fd169f82d83d',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'IndexTap.vue:mounted',message:'Component mounted',data:{invoicesCount:this.invoices?.length||0,onlinePayment:this.online_payment,hasInvoices:!!this.invoices},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'A'})}).catch(()=>{});
        // #endregion
        
        if (this.invoices && this.invoices.length > 0) {
            this.invoiceToPay = this.invoices[0];
            // #region agent log
            fetch('http://127.0.0.1:7244/ingest/06d35c0e-7279-47b0-bac0-fd169f82d83d',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'IndexTap.vue:mounted',message:'Invoice selected',data:{invoiceId:this.invoiceToPay?.id,invoiceAmount:this.invoiceToPay?.grand_total},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'B'})}).catch(()=>{});
            // #endregion
            this.checkTabbyOptions();
        }
        
        // For debugging: Check if route helper is available
        console.log('[Tabby] Route helper available:', typeof route === 'function');
        if (typeof route === 'function') {
            try {
                const testRoute = route('payment.tabby.check-options');
                console.log('[Tabby] Route test:', testRoute);
                // #region agent log
                fetch('http://127.0.0.1:7244/ingest/06d35c0e-7279-47b0-bac0-fd169f82d83d',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'IndexTap.vue:mounted',message:'Route helper test',data:{routeUrl:testRoute,routeHelperExists:true},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'C'})}).catch(()=>{});
                // #endregion
            } catch (e) {
                // Route not found in Ziggy - this is expected, fallback will be used in checkTabbyOptions
                console.log('[Tabby] Route not in Ziggy (will use fallback):', e.message);
                // #region agent log
                fetch('http://127.0.0.1:7244/ingest/06d35c0e-7279-47b0-bac0-fd169f82d83d',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'IndexTap.vue:mounted',message:'Route helper error (expected)',data:{error:e.message},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'C'})}).catch(()=>{});
                // #endregion
            }
        } else {
            // #region agent log
            fetch('http://127.0.0.1:7244/ingest/06d35c0e-7279-47b0-bac0-fd169f82d83d',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'IndexTap.vue:mounted',message:'Route helper not available',data:{routeHelperExists:false},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'C'})}).catch(()=>{});
            // #endregion
        }
    },
    methods: {
        async checkTabbyOptions() {
            // #region agent log
            fetch('http://127.0.0.1:7244/ingest/06d35c0e-7279-47b0-bac0-fd169f82d83d',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'IndexTap.vue:checkTabbyOptions',message:'Function entry',data:{hasInvoice:!!this.invoiceToPay,invoiceId:this.invoiceToPay?.id,onlinePayment:this.online_payment},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'B'})}).catch(()=>{});
            // #endregion
            
            if (!this.invoiceToPay) {
                console.log('[Tabby] No invoice selected');
                // #region agent log
                fetch('http://127.0.0.1:7244/ingest/06d35c0e-7279-47b0-bac0-fd169f82d83d',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'IndexTap.vue:checkTabbyOptions',message:'Early return - no invoice',data:{},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'B'})}).catch(()=>{});
                // #endregion
                return;
            }

            console.log('[Tabby] Checking options for invoice:', this.invoiceToPay.id);
            this.loadingTabbyOptions = true;
            this.tabbyError = null;
            this.tabbyPlans = [];

            try {
                // Try to get route URL, fallback to direct URL if route helper fails
                let routeUrl;
                try {
                    routeUrl = route('payment.tabby.check-options');
                    // #region agent log
                    fetch('http://127.0.0.1:7244/ingest/06d35c0e-7279-47b0-bac0-fd169f82d83d',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'IndexTap.vue:checkTabbyOptions',message:'Route helper success',data:{routeUrl:routeUrl},timestamp:Date.now(),sessionId:'debug-session',runId:'run2',hypothesisId:'C'})}).catch(()=>{});
                    // #endregion
                } catch (e) {
                    // Fallback: construct URL directly (routes are under /trainees prefix)
                    const baseUrl = window.location.origin;
                    routeUrl = `${baseUrl}/trainees/payment/tabby/check-options`;
                    console.warn('[Tabby] Route helper failed, using direct URL:', routeUrl);
                    // #region agent log
                    fetch('http://127.0.0.1:7244/ingest/06d35c0e-7279-47b0-bac0-fd169f82d83d',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'IndexTap.vue:checkTabbyOptions',message:'Route helper failed, using fallback',data:{routeUrl:routeUrl,error:e.message},timestamp:Date.now(),sessionId:'debug-session',runId:'run2',hypothesisId:'C'})}).catch(()=>{});
                    // #endregion
                }
                
                console.log('[Tabby] Route URL:', routeUrl);
                console.log('[Tabby] Request params:', { invoice_id: this.invoiceToPay.id });
                
                // #region agent log
                fetch('http://127.0.0.1:7244/ingest/06d35c0e-7279-47b0-bac0-fd169f82d83d',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'IndexTap.vue:checkTabbyOptions',message:'Before API call',data:{routeUrl:routeUrl,invoiceId:this.invoiceToPay.id},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'C'})}).catch(()=>{});
                // #endregion

                const response = await axios.get(routeUrl, {
                    params: {
                        invoice_id: this.invoiceToPay.id
                    }
                });
                
                // #region agent log
                fetch('http://127.0.0.1:7244/ingest/06d35c0e-7279-47b0-bac0-fd169f82d83d',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'IndexTap.vue:checkTabbyOptions',message:'API response received',data:{status:response.status,hasData:!!response.data,resultCode:response.data?.resultCode,hasResult:!!response.data?.result,hasProducts:!!response.data?.result?.products,productsCount:response.data?.result?.products?.length||0},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'D'})}).catch(()=>{});
                // #endregion

                console.log('[Tabby] Full API Response:', response);
                console.log('[Tabby] Response Data:', response.data);
                console.log('[Tabby] Response Status:', response.status);

                // Check multiple possible response structures
                let products = [];
                let resultCode = null;
                let message = null;

                if (response.data) {
                    // Try different response structures
                    if (response.data.resultCode !== undefined) {
                        resultCode = response.data.resultCode;
                        message = response.data.message;
                        
                        // Check for products in different possible locations
                        if (response.data.result) {
                            if (Array.isArray(response.data.result.products)) {
                                products = response.data.result.products;
                            } else if (response.data.result.product) {
                                products = [response.data.result.product];
                            } else if (Array.isArray(response.data.result)) {
                                products = response.data.result;
                            }
                        }
                    } else if (response.data.products) {
                        products = Array.isArray(response.data.products) ? response.data.products : [response.data.products];
                    } else if (Array.isArray(response.data)) {
                        products = response.data;
                    }
                }

                console.log('[Tabby] Extracted products:', products);
                console.log('[Tabby] Result Code:', resultCode);
                console.log('[Tabby] Message:', message);

                // #region agent log
                fetch('http://127.0.0.1:7244/ingest/06d35c0e-7279-47b0-bac0-fd169f82d83d',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'IndexTap.vue:checkTabbyOptions',message:'Before processing products',data:{resultCode:resultCode,productsCount:products.length,products:products},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'D'})}).catch(()=>{});
                // #endregion
                
                if (resultCode === 0 && products.length > 0) {
                    this.tabbyOptionsAvailable = true;
                    this.tabbyPlans = products.map(product => ({
                        productId: product.productId || product.id,
                        productType: product.productType || product.type || 'INSTALLMENTS',
                        description: product.description || product.name || `${product.productType || 'INSTALLMENTS'} - ${product.productId || product.id}`,
                    }));
                    
                    console.log('[Tabby] Plans mapped:', this.tabbyPlans);
                    
                    // Auto-select first plan
                    if (this.tabbyPlans.length > 0) {
                        this.selectedTabbyPlan = this.tabbyPlans[0].productId;
                        console.log('[Tabby] Auto-selected plan:', this.selectedTabbyPlan);
                    }
                    
                    // #region agent log
                    fetch('http://127.0.0.1:7244/ingest/06d35c0e-7279-47b0-bac0-fd169f82d83d',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'IndexTap.vue:checkTabbyOptions',message:'Tabby options available set to true',data:{tabbyOptionsAvailable:this.tabbyOptionsAvailable,plansCount:this.tabbyPlans.length},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'E'})}).catch(()=>{});
                    // #endregion
                } else {
                    this.tabbyOptionsAvailable = false;
                    this.tabbyError = message || 'No Tabby options available';
                    console.log('[Tabby] No options available. ResultCode:', resultCode, 'Products count:', products.length);
                    // #region agent log
                    fetch('http://127.0.0.1:7244/ingest/06d35c0e-7279-47b0-bac0-fd169f82d83d',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'IndexTap.vue:checkTabbyOptions',message:'Tabby options not available',data:{resultCode:resultCode,productsCount:products.length,error:this.tabbyError},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'D'})}).catch(()=>{});
                    // #endregion
                }
            } catch (error) {
                console.error('[Tabby] Error details:', error);
                console.error('[Tabby] Error response:', error.response);
                console.error('[Tabby] Error message:', error.message);
                this.tabbyOptionsAvailable = false;
                
                // #region agent log
                fetch('http://127.0.0.1:7244/ingest/06d35c0e-7279-47b0-bac0-fd169f82d83d',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'IndexTap.vue:checkTabbyOptions',message:'API call error',data:{errorMessage:error.message,hasResponse:!!error.response,responseStatus:error.response?.status,responseData:error.response?.data},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'F'})}).catch(()=>{});
                // #endregion
                
                if (error.response) {
                    console.error('[Tabby] Error response data:', error.response.data);
                    console.error('[Tabby] Error response status:', error.response.status);
                    this.tabbyError = error.response.data?.error || error.response.data?.message || `API Error: ${error.response.status}`;
                } else if (error.request) {
                    console.error('[Tabby] No response received:', error.request);
                    this.tabbyError = 'No response from server. Please check your connection.';
                } else {
                    this.tabbyError = error.message || 'Failed to load Tabby options';
                }
            } finally {
                this.loadingTabbyOptions = false;
                console.log('[Tabby] Final state - Available:', this.tabbyOptionsAvailable, 'Plans:', this.tabbyPlans.length);
                // #region agent log
                fetch('http://127.0.0.1:7244/ingest/06d35c0e-7279-47b0-bac0-fd169f82d83d',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'IndexTap.vue:checkTabbyOptions',message:'Function exit',data:{tabbyOptionsAvailable:this.tabbyOptionsAvailable,plansCount:this.tabbyPlans.length,hasError:!!this.tabbyError,onlinePayment:this.online_payment},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'A'})}).catch(()=>{});
                // #endregion
            }
        },
        onInvoiceChanged() {
            console.log('[Tabby] Invoice changed to:', this.invoiceToPay?.id);
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
                
                // Try to get route URL, fallback to direct URL if route helper fails
                let initiateUrl;
                try {
                    initiateUrl = route('payment.tabby.initiate');
                } catch (e) {
                    const baseUrl = window.location.origin;
                    initiateUrl = `${baseUrl}/trainees/payment/tabby/initiate`;
                    console.warn('[Tabby] Route helper failed for initiate, using direct URL:', initiateUrl);
                }
                form.action = initiateUrl;
                
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
