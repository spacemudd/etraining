<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'invoices', link: route('back.finance.invoices.index')},
                    {title_raw: invoice.number_formatted},
                ]"
            ></breadcrumb-container>

            <div class="flex flex-col md:flex-row md:space-x-5">
                <div class="w-full md:w-8/12">
                    <div class="flex justify-between">
                        <h1 class="mb-8 font-bold text-2xl img {display:block} inline-flex">{{ $t('words.invoice') }}
                            <svg v-if="invoice.status === 1" width="30" height="30" class="mx-2 mt-1">
                                <image class=inline xlink:href="https://www.svgrepo.com/show/402906/white-heavy-check-mark.svg" src="https://www.svgrepo.com/show/402906/white-heavy-check-mark.svg" width="30" height="30"/>
                            </svg>
                        </h1>
                        <div class="mb-6 flex justify-end items-center">
                            <div>
                                <div v-if="invoice.status === -2"
                                     class="text-red-600 rounded-lg mx-8 px-4 py-1 font-bold border-solid border-2 border-red-600">
                                    {{ $t('words.archived') }}
                                </div>
                                <div v-if="invoice.status === -1"
                                     class="text-red-600 rounded-lg mx-8 px-4 py-1 font-bold border-solid border-2 border-red-600">
                                    {{ $t('words.under-review') }}
                                </div>
                                <div v-if="invoice.status === 0"
                                     class="text-red-600 rounded-lg mx-8 px-4 py-1 font-bold border-solid border-2 border-red-600">
                                    {{ $t('words.unpaid') }}
                                </div>
                                <div v-if="invoice.status === 1"
                                     class="text-green-500 rounded-lg mx-8 px-4 py-1 font-bold border-solid border-2 border-green-500">
                                    {{ $t('words.paid') }}
                                </div>
                                <div v-if="invoice.status === 2"
                                     class="text-yellow-400 rounded-lg mx-8 px-4 py-1 font-bold border-solid border-2 border-yellow-400">
                                    {{ $t('words.audit-required') }}
                                </div>
                                <div v-if="invoice.status === 3"
                                     class="text-orange-500 rounded-lg mx-8 px-4 py-1 font-bold border-solid border-2 border-orange-500">
                                    {{ $t('words.reject-payment-receipt') }}
                                </div>
                                <div v-if="invoice.status === 4"
                                     class="text-yellow-400 rounded-lg mx-8 px-4 py-1 font-bold border-solid border-2 border-yellow-400">
                                    {{ $t('words.finance-audit-required') }}
                                </div>
                            </div>
                            <div>
                                <a class="inline-flex items-center mx-2 px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150 disabled:cursor-not-allowed mx-"
                                   target="_blank"
                                   :href="route('back.finance.invoices.date-period', {id: invoice.id, from_date: invoice.from_date, to_date: invoice.to_date, created_by_id: invoice.created_by_id, created_at_date: invoice.created_at_date,})">
                                    {{ $t('words.change-date-period') }}
                                </a>
                            </div>
                            <a class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150 disabled:cursor-not-allowed mx-"
                               target="_blank"
                               :href="route('back.finance.invoices.pdf', invoice.id)">
                                {{ $t('words.print') }}
                            </a>

                            <button @click="resetStatus(invoice)"
                                    v-can="'can-delete-invoice-anytime'"
                                    type="button"
                                    v-if="invoice.status == -2"
                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest focus:outline-none focus:shadow-outline-gray transition ease-in-out duration-700 disabled:cursor-not-allowed mx-2 bg-purple-400 hover:bg-red-600 active:bg-red-700 foucs:bg-red-700 mb-0 hover:mb-2">
                                {{ $t('words.mark-unpaid') }}
                            </button>

                            <button @click="markUnderReview(invoice)"
                                    v-can="'can-delete-invoice-anytime'"
                                    type="button"
                                    v-if="invoice.status <= 4 && invoice.status != -1 && invoice.status != -2"
                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest focus:outline-none focus:shadow-outline-gray transition ease-in-out duration-700 disabled:cursor-not-allowed mx-2 bg-purple-400 hover:bg-red-600 active:bg-red-700 foucs:bg-red-700 mb-0 hover:mb-2">
                                {{ $t('words.mark-under-review') }}
                            </button>

                            <button @click="markAsArchived(invoice)"
                                    v-can="'can-delete-invoice-anytime'"
                                    type="button"
                                    v-if="invoice.status == -1"
                                    class="hoverme inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest focus:outline-none focus:shadow-outline-gray transition ease-in-out duration-700 disabled:cursor-not-allowed mx-2 bg-purple-400 hover:bg-red-600 active:bg-red-700 foucs:bg-red-700 mb-0 hover:mb-2">
                                {{ $t('words.mark-archived') }}
                            </button>

                            <inertia-link v-if="invoice.can_upload_receipt"
                                          :href="route('back.finance.invoices.upload-receipt-form', invoice.id)"
                                          class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest focus:outline-none focus:shadow-outline-gray transition ease-in-out duration-150 disabled:cursor-not-allowed mx-2 bg-red-500 hover:bg-red-600 active:bg-red-700 foucs:bg-red-700">
                                {{ $t('words.upload-receipt') }}
                            </inertia-link>

                            <button @click="deleteInvoice(invoice)"
                                    v-can="'can-delete-invoice-anytime'"
                                    type="button"
                                    v-if="invoice.status <= 4 && invoice.payment_method != 1"
                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest focus:outline-none focus:shadow-outline-gray transition ease-in-out duration-150 disabled:cursor-not-allowed mx-2 bg-red-500 hover:bg-red-600 active:bg-red-700 foucs:bg-red-700">
                                {{ $t('words.delete') }}
                            </button>
                            <button @click="markAsUnpaid"
                                    v-can="'approve-payment-receipt'"
                                    type="button"
                                    v-if="invoice.status === 3 || invoice.status === 2"
                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest focus:outline-none focus:shadow-outline-gray transition ease-in-out duration-150 disabled:cursor-not-allowed mx-2 bg-red-500 hover:bg-red-600 active:bg-red-700 foucs:bg-red-700">
                                [{{ $t('words.chase') }}] {{ $t('words.mark-as-unpaid') }}
                            </button>
                            <button @click="markAsPaid"
                                    v-can="'approve-payment-receipt'"
                                    type="button"
                                    v-if="invoice.status === 3 || invoice.status === 2"
                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest focus:outline-none focus:shadow-outline-gray transition ease-in-out duration-150 disabled:cursor-not-allowed mx-2 bg-green-500 hover:bg-green-600 active:bg-green-700 foucs:bg-green-700">
                                [{{ $t('words.chase') }}] {{ $t('words.mark-as-paid') }}
                            </button>

                            <template v-if="invoice.status === 4">
                                <button @click="rejectPaymentReceipt"
                                        v-can="'approve-invoice-paid'"
                                        v-if="invoice.status === 4 || invoice.status === 3"
                                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest focus:outline-none focus:shadow-outline-gray transition ease-in-out duration-150 disabled:cursor-not-allowed mx-2 bg-red-500 hover:bg-red-600 active:bg-red-700 foucs:bg-red-700"
                                        type="button">
                                    [{{ $t('words.finance') }}] {{ $t('words.reject-payment-receipt') }}
                                </button>

                                <inertia-link :href="route('back.finance.invoices.approve-payment-receipt', invoice.id)"
                                              v-can="'approve-invoice-paid'"
                                              v-if="invoice.status === 3 || invoice.status === 4"
                                              class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest focus:outline-none focus:shadow-outline-gray transition ease-in-out duration-150 disabled:cursor-not-allowed mx-2 bg-green-500 hover:bg-green-600 active:bg-green-700 foucs:bg-green-700">
                                    [{{ $t('words.finance') }}] {{ $t('words.approve-payment-receipt') }}
                                </inertia-link>
                            </template>
                        </div>
                    </div>

                    <div class="flex flex-col justify-start rounded bg-gray-50 rounded shadow-lg p-5">
                        <div class="w-full p-4">
                            <div class="grid grid-cols-2">
                                <div class="text-2xl font-bold my-1.5">{{ $t('words.total-amount') }}</div>
                                <div class="text-2xl img {display:block} inline-flex font-bold my-2 mb-4">{{ invoice.grand_total }}
                                    <svg width="25" height="25" class="mx-1 mt-2">
                                        <image class="inline " xlink:href="https://upload.wikimedia.org/wikipedia/commons/2/2b/Rial_Sign.PNG" src="https://upload.wikimedia.org/wikipedia/commons/2/2b/Rial_Sign.PNG" width="25" height="25"/>
                                    </svg></div>
                                <div class="font-bold my-1">{{ $t('words.client-name') }}</div>
                                <div class="my-0.5">
                                    <inertia-link class="text-blue-500 hover:text-blue-600" :href="invoice.trainee.show_url">{{ invoice.trainee ? invoice.trainee.name : '-' }}</inertia-link>
                                    <inertia-link class="text-blue-500 hover:text-blue-600" :href="invoice.company.show_url">({{ invoice.company ? invoice.company.name_ar : 'Unknown' }})</inertia-link>
                                </div>
                                <div class="font-bold my-1">{{ $t('words.paid') }}</div>
                                <div class="my-0.5">{{ invoice.is_paid ? $t('words.yes') : $t('words.no') }} <span v-if="invoice.is_paid">({{ invoice.payment_method_formatted }})
                                    <span class="img {display:block} inline-flex ml-8/12">
                                        <svg v-if="invoice.payment_method === 1" width="55" height="55" class="mx-0.5">
                                            <image class=inline xlink:href="https://www.svgrepo.com/show/328112/visa.svg" src="https://www.svgrepo.com/show/328112/visa.svg" width="55" height="55"/>
                                        </svg>
                                        <svg v-if="invoice.payment_method === 1" width="35" height="55" class="mx-0.5">
                                            <image class=inline xlink:href="https://www.svgrepo.com/show/163750/mastercard.svg" src="https://www.svgrepo.com/show/163750/mastercard.svg" width="35" height="55"/>
                                        </svg>
                                        <svg v-if="invoice.payment_method === 1" width="55" height="55" class="mx-0.5">
                                            <image class=inline xlink:href="https://upload.wikimedia.org/wikipedia/commons/f/fb/Mada_Logo.svg" src="https://upload.wikimedia.org/wikipedia/commons/f/fb/Mada_Logo.svg" width="55" height="55"/>
                                        </svg>
                                        <svg v-if="invoice.payment_method === 1" width="55" height="55" class="mx-0.5">
                                            <image class=inline xlink:href="https://www.svgrepo.com/show/303191/apple-pay-logo.svg" src="https://www.svgrepo.com/show/303191/apple-pay-logo.svg" width="55" height="55"/>
                                        </svg>
                                    </span>
                                </span>
                                </div>
                            </div>
                            <hr class="my-0.5">
                            <div class="grid grid-cols-2">
                                <div class="font-bold my-0.5">{{ $t('words.date') }}</div>
                                <div class="my-0.5">{{ invoice.created_at | formatDate }}</div>
                                <div class="font-bold my-0.5">{{ $t('words.invoice-no') }}</div>
                                <div class="my-0.5">{{ invoice.number_formatted }}</div>
                                <div class="font-bold my-0.5">{{ $t('words.from-date') }}</div>
                                <div class="my-0.5">{{ invoice.from_date | formatDate }}</div>
                                <div class="font-bold my-0.5">{{ $t('words.to-date') }}</div>
                                <div class="my-0.5">{{ invoice.to_date | formatDate }}</div>
                                <hr class="my-3">
                                <hr class="my-3">
                                <div v-if="invoice.trainee_bank_payment_receipt">
                                    <div v-if="invoice.payment_method === 0" class="font-bold my-0.51">{{ $t('words.sender-bank-name') }}</div>
                                    <div v-if="invoice.payment_method === 0" class="font-bold my-0.5">{{ $t('words.receiver-bank-name') }}<br/></div>
                                </div>
                                <div v-if="invoice.trainee_bank_payment_receipt">
                                    <div v-if="invoice.payment_method === 0">{{ invoice.trainee_bank_payment_receipt.bank_from }}</div>
                                    <div v-if="invoice.payment_method === 0">{{ invoice.trainee_bank_payment_receipt.bank_to }}</div>
                                </div>
                                <hr class="my-3" v-if="invoice.trainee_bank_payment_receipt">
                                <hr class="my-3" v-if="invoice.trainee_bank_payment_receipt">
                                <div class="font-bold my-0.5">{{ $t('words.chase') }}</div>
                                <div class="my-0.5" dir="rtl">{{ invoice.chase_status }} <span v-if="invoice.under_review_reason">- {{ invoice.under_review_reason }}</span></div>
                                <div class="font-bold my-0.5">{{ $t('words.chased-by') }}</div>
                                <div class="my-0.5"><span v-if="invoice.chased_by">{{ invoice.chased_by.name }}</span></div>
                                <div class="font-bold my-0.5" v-if="invoice.verified_by">{{ $t('words.approved-by') }}</div>
                                <div v-if="invoice.verified_by">
                                    <span>{{ invoice.verified_by.name }}</span>
                                </div>
                                <div v-if="invoice.rejection_reason_payment_receipt" class="bg-red-200 border-2 border-red-500 text-black p-3 mt-2 border-l-0">
                                    <div class="font-bold my-0.5">{{ $t('words.reject-payment-receipt-reason') }}</div>
                                </div>
                                <div v-if="invoice.rejection_reason_payment_receipt" class="bg-red-200 border-2 border-red-500 text-black py-3 mt-2 border-r-0">
                                    <div>
                                        <span>{{ invoice.rejection_reason_payment_receipt }}</span>
                                    </div>
                                </div>
                                <hr class="my-3">
                                <hr class="my-3">
                                <div class="font-bold my-0.5">{{ $t('words.verified') }}</div>
                                <div class="my-0.5">
                                    <span v-if="invoice.is_verified"
                                          class="bg-green-500 text-white rounded px-2 my-1.5">
                                        {{ $t('words.yes') }}
                                    </span>
                                    <span v-else
                                          class="bg-red-600 text-white rounded px-2 my-1.5">
                                        {{ $t('words.no') }}
                                    </span>
                                </div>

                                <hr v-if="invoice.center_id" class="my-3">
                                <hr v-if="invoice.center_id" class="my-3">
                                <div v-if="invoice.center_id" class="font-bold my-0.5">{{ $t('words.account') }}</div>
                                <div v-if="invoice.center_id" class="my-0.5">
                                    <span v-if="invoice.center_id==' 5675'"
                                          class="rounded px-4 py-1 my-1">
                                        معهد جسر
                                    </span>
                                    <span v-if="invoice.center_id=='5676'"
                                          class=" rounded px-4 py-1 my-1">
                                        مركز جسارة
                                    </span>
                                </div>

                                <hr class="my-3" v-if="invoice.payment_method === 1">
                                <hr class="my-3" v-if="invoice.payment_method === 1">
                                <div v-if="invoice.payment_method === 1" class="font-bold my-0.5">{{ $t('words.payment-reference-id') }}</div>
                                <a class="a-9" :href="invoice.noon_link"><div v-if="invoice.payment_method === 1" class="truncate my-0.5" >{{ invoice.payment_reference_id }}</div></a>
                                <div v-if="invoice.payment_method === 1" class="font-bold my-0.5">{{ $t('words.paid-at') }}</div>
                                <div v-if="invoice.payment_method === 1" class="truncate my-0.5">{{ invoice.paid_at_time }}</div>


                            </div>


                        </div>
                    </div>
                </div>
                <div class="w-full md:w-4/12">
                    <div class="mx-5">
                        <h1 class="mb-8 font-bold text-2xl">{{ $t('words.documents') }}</h1>
                        <div class="white-bg rounded bg-gray-50 rounded shadow-lg p-5">
                            <ul class="ml-auto flex">
                                <template v-if="invoice.trainee_bank_payment_receipt">
                                    <li v-for="file in invoice.trainee_bank_payment_receipt.approvals" class="inline-block">
                                        <a :href="file.download_url" target="_blank" class="font-bold hover:text-blue-600" alt="invoice.">
                                            {{ $t('words.receipt-approval') }}:
                                            <svg v-if="file.mime_type.includes('pdf')" width="90" height="90" class="mx-0.5 mt-2">
                                                <image class=inline xlink:href="https://cdn-icons-png.flaticon.com/512/4726/4726010.png" src="https://cdn-icons-png.flaticon.com/512/4726/4726010.png" width="90" height="90"/>
                                            </svg>
                                            <svg v-else width="90" height="90" class="mx-0.5 mt-2">
                                                <image xlink:href="https://cdn-icons-png.flaticon.com/512/4725/4725998.png" src="https://cdn-icons-png.flaticon.com/512/4725/4725998.png"  width="90" height="90"/>
                                            </svg>
                                            <span class="text-sm text-gray-800 inline-flex mx-4 mt-4" dir="ltr">{{ file.created_at_timezone }}</span>
                                        </a>
                                    </li>
                                </template>

                                <template v-if="invoice.trainee_bank_payment_receipt">
                                    <li v-for="file in invoice.trainee_bank_payment_receipt.attachments" class="inline-block">
                                        <a :href="file.download_url" target="_blank" class="font-bold hover:text-blue-600">
                                            {{ $t('words.receipt') }}:
                                            <svg v-if="file.mime_type.includes('pdf')" width="90" height="90" class="mx-0.5 mt-2">
                                                <image class=inline xlink:href="https://cdn-icons-png.flaticon.com/512/4726/4726010.png" src="https://cdn-icons-png.flaticon.com/512/4726/4726010.png" width="90" height="90"/>
                                            </svg>
                                            <svg v-else width="90" height="90" class="mx-0.5 mt-2">
                                                <image xlink:href="https://cdn-icons-png.flaticon.com/512/4725/4725998.png" src="https://cdn-icons-png.flaticon.com/512/4725/4725998.png"  width="90" height="90"/>
                                            </svg>
                                            <span class="text-sm text-gray-800 inline-flex mx-4 mt-4" dir="ltr">{{ file.created_at_timezone }}</span>
                                        </a>
                                    </li>
                                </template>
                                <template v-else>
                                    <empty-slate />
                                </template>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex flex-col md:flex-row md:space-x-5">
                <div class="w-full md:w-8/12">
                    <h1 class="mb-8 font-bold text-2xl">{{ $t('words.items') }}</h1>

                    <div>
                        <table class="w-full whitespace-no-wrap bg-white rounded-lg my-5 p-5 bg-gray-50 rounded shadow-lg text-sm">
                            <tr class="text-left font-bold">
                                <th class="p-4">{{ $t('words.name') }}</th>
                                <th class="p-4">{{ $t('words.subtotal') }}</th>
                                <th class="p-4">{{ $t('words.tax') }}</th>
                                <th class="p-4">{{ $t('words.total') }}</th>
                            </tr>
                            <tr
                                v-for="item in invoice.items"
                                :key="item.id"
                                class="border-t hover:bg-gray-100 focus-within:bg-gray-100"
                            >
                                <td class="px-4 py-4">
                                    {{ item.name_ar }}
                                </td>

                                <td class="px-4 py-4">
                                    {{ item.sub_total }}
                                </td>

                                <td class="px-4 py-4">
                                    {{ item.tax }}
                                </td>

                                <td class="px-4 py-4">
                                    {{ item.grand_total }}
                                </td>
                            </tr>

                            <tr v-if="invoice.items.length === 0">
                                <td
                                    class="border-t px-4 py-4"
                                    colspan="4"
                                >
                                    <empty-slate />
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!--<div class="w-full md:w-4/12">-->
                <!--    <div class="mx-5">-->
                <!--        <h1 class="mb-8 font-bold text-2xl">{{ $t('words.comments') }}</h1>-->
                <!--        <div class="white-bg rounded shadow p-5">-->

                <!--        </div>-->
                <!--    </div>-->
                <!--</div>-->
            </div>
            <div class="mt-8 flex flex-col md:flex-row md:space-x-5 mt-8 mb-8" v-if="invoice.status === 0" v-can="'edit-invoice-amount'">
                <div class="w-full md:w-8/12">
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <h1 class="mb-8 font-bold text-2xl">{{ $t('words.edit-amount')}}</h1>
                        </div>
                        <div>
                            <button
                                v-if="!editButton.editOption"
                                @click="editInvoice"
                                class="items-center justify-end rounded-md px-4 py-2 bg-yellow-200 hover:bg-gray-300 float-left text-right"
                            >
                                {{ editButton.text }}
                            </button>
                            <button
                                v-else
                                @click="editInvoice"
                                :disabled="$wait.is('UPDATING_TRAINEE')"
                                :class="{'bg-green-200 cursor-wait': $wait.is('UPDATING_TRAINEE')}"
                                class=" items-center justify-end rounded-md px-4 py-2 mx-2 bg-green-300 hover:bg-green-400 float-left text-right"
                            >
                                <svg v-if="$wait.is('UPDATING_TRAINEE')" role="status" class="inline w-4 h-4 text-black animate-spin" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="#E5E7EB"/>
                                    <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentColor"/>
                                </svg>
                                {{ editButton.text }}
                            </button>

                            <button
                                v-if="editButton.editOption"
                                @click="cancelEdit"
                                class=" items-center justify-end rounded-md px-4 py-2 mx-2 bg-red-300 hover:bg-red-400 float-left text-right"
                            >
                                {{ cancelButton.text }}
                            </button>
                        </div>
                    </div>
                    <div class="white-bg rounded bg-gray-50 rounded shadow-lg p-5">
                        <div class="col-span-6 sm:col-span-2 px-8">
                            <jet-label
                                for="grand_total"
                                :value="$t('words.amount')"
                            />
                            <jet-input
                                id="grand_total"
                                type="text"
                                :class="editButton.inputClass"
                                v-model="invoice.grand_total"
                                :disabled="!editButton.editOption"
                            />
                        </div> <br/>
                        <div class="col-span-6 sm:col-span-2 px-8">
                            <jet-label
                                for="edit_amount_reason"
                                :value="$t('words.reason')"
                            />
                            <jet-input
                                id="edit_amount_reason"
                                type="text"
                                :class="editButton.inputClass"
                                v-model="invoice.edit_amount_reason"
                                :disabled="!editButton.editOption"
                            />
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-8 flex flex-col md:flex-row md:space-x-5 mt-8 mb-8" v-can="'edit-date-period'">
                <div class="w-full md:w-8/12">
                    <jet-form-section @submitted="ChangeDatePeriod">
                        <template #form>
                            <div class="col-span-2 sm:col-span-2">
                                <jet-label
                                    for="from"
                                    :value="$t('words.from-date')"
                                />
                                <jet-input
                                    id="from"
                                    type="date"
                                    class="mt-1 block w-full"
                                    v-model="form.from_date"
                                    autocomplete="off"
                                />
                                <jet-input-error
                                :message="form.error('from_date')"
                                class="mt-2"
                            />
                            </div>
                            <div class="col-span-2 sm:col-span-2">
                                <jet-label
                                    for="year"
                                    :value="$t('words.to-date')"
                                />
                                <jet-input
                                    id="to"
                                    type="date"
                                    class="mt-1 block w-full"
                                    v-model="form.to_date"
                                    autocomplete="off"
                                />
                                <jet-input-error
                                :message="form.error('to_date')"
                                class="mt-2"
                            />
                            </div>
                        </template>
                        <template #actions>
                            <jet-action-message
                                :on="form.recentlySuccessful"
                                class="mr-3"
                            >
                                {{ $t('words.saved-successfully') }}
                            </jet-action-message>
                            <inertia-link
                                :href="`/`"
                                class="flex items-center justify-start rtl:ml-4 ltr:mr-4 rounded-md px-4 py-2 bg-white hover:bg-gray-300 text-right"
                            >
                                {{ $t('words.cancel') }}
                            </inertia-link>
                            <jet-button
                                    :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing"
                                >
                                    {{ $t('words.save') }}
                                </jet-button>
                        </template>
                    </jet-form-section>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import Breadcrumb from "@/Components/Breadcrumb";
import JetSectionBorder from '@/Jetstream/SectionBorder'
import JetDialogModal from '@/Jetstream/DialogModal'
import JetInput from '@/Jetstream/Input'
import JetInputError from '@/Jetstream/InputError'
import JetActionMessage from '@/Jetstream/ActionMessage';
import JetButton from '@/Jetstream/Button';
import JetFormSection from '@/Jetstream/FormSection';
import JetLabel from '@/Jetstream/Label';
import CompanyContractsPagination from "@/Components/CompanyContractsPagination";
import BreadcrumbContainer from "@/Components/BreadcrumbContainer";
import VueDropzone from 'vue2-dropzone'
import 'vue2-dropzone/dist/vue2Dropzone.min.css'
import SelectTraineeGroup from "@/Components/SelectTraineeGroup";
import ChangeTraineePassword from '@/Components/ChangeTraineePassword';
import AttendanceSheetManagementForTrainee from "@/Components/AttendanceSheetManagementForTrainee";
import 'selectize/dist/js/standalone/selectize.min';
import EmptySlate from "@/Components/EmptySlate";
import TraineeAuditContainer from "@/Components/TraineeAuditContainer";
import ValidationErrors from "@/Components/ValidationErrors";
import {Inertia} from "@inertiajs/inertia";
import moment from "moment";
import _ from "lodash";
import VueConfetti from 'vue-confetti';
import Vue from 'vue';

Vue.use(VueConfetti);

export default {
    props: [
        'invoice',
        'invoice',
        'company',
        'old_from_date',
        'old_to_date',
        'created_at',
        'created_by_id',
    ],
    components: {
        AppLayout,
        AttendanceSheetManagementForTrainee,
        Breadcrumb,
        BreadcrumbContainer,
        ChangeTraineePassword,
        CompanyContractsPagination,
        JetActionMessage,
        JetButton,
        JetDialogModal,
        JetFormSection,
        JetInput,
        JetInputError,
        JetLabel,
        JetSectionBorder,
        SelectTraineeGroup,
        VueDropzone,
        EmptySlate,
        ValidationErrors,
        TraineeAuditContainer,
    },
    data() {
        return {
            cancelButton: {
                text: this.$t('words.cancel'),
            },
            editButton: {
                text: this.$t('words.edit'),
                editOption: false,
                inputClass: "mt-1 block w-full bg-gray-200",
                selectInputClass: "mt-1 block w-full border border-gray-200 bg-gray-200 py-2.5 px-4 pr-8 rounded leading-tight focus:outline-none"
            },
            expectedToPay: null,
            current_year: moment().utc().year(),
            traineesCollection: [],
            form: this.$inertia.form({
                from_date: null,
                to_date: null,
                old_from_date: null,
                old_to_date: null,
                created_at: null,
                created_by_id: null,
            }),
            searchString: '',
            searchResults: [],
            searchBoxVisible: false,
        }
    },
    mounted() {
        this.form.old_from_date = this.old_from_date;
        this.form.old_to_date = this.old_to_date;
        this.form.created_at = this.created_at;
        this.form.created_by_id = this.created_by_id;
    },
    methods: {
        triggerSearching() {
            if (this.searchString) {
                this.searchBoxVisible = true;
                this.loadSearchResultsBox();
            } else {
                // this.userFinishedWithResults();
                this.searchBoxVisible = false;
            }
        },
        loadSearchResultsBox: _.debounce(function() {
            if (this.searchString) {
                this.searchResults = 3;
                axios.get('/back/search', {
                    params: {
                        search: this.searchString,
                        trainees: true,
                    }
                })
                    .then(response => {
                        this.searchResults = response.data;
                    })
            }
        }, 320),
        toggleSelectedTrainee(trainee_id) {
            if (this.form.trainees.includes(trainee_id)) {
                let index = this.form.trainees.indexOf(trainee_id);
                if (index !== -1) {
                    this.form.trainees.splice(index, 1);
                }
            } else {
                this.form.trainees.push(trainee_id);
            }
        },
        updateExpectedAmountPerInvoice() {
            if (this.form.from_date && this.form.to_date) {
                axios.post('/back/finance/expected-amount-per-invoice', {
                    from_date: this.form.from_date,
                    to_date: this.form.to_date,
                    id: this.invoice.id,
                }).then(response => {
                    this.expectedToPay = response.data.cost;
                });
            }
        },
        ChangeDatePeriod() {
            if(this.form.from_date && this.form.to_date){
                this.form.put(route('back.finance.invoices.change-date-period', this.invoice.id), {
                    preserveScroll: true
                }).catch(error => {
                    this.form.processing = false;
                }).finally(() => {
                    this.form.processing = false;
                });
            }

        },
        addToTrainees(trainee_id, name) {
            this.traineesCollection[trainee_id] = name;
            this.form.trainees.push(trainee_id);
        },
        selectAllTrainees() {
            if (this.form.trainees.length === _.size(this.traineesCollection)) {
                this.form.trainees = [];
            } else {
                this.form.trainees = [];
                Object.keys(this.traineesCollection).forEach((trainee, key) => {
                    this.form.trainees.push(trainee);
                })
            }
        },
        cancelEdit() {
            this.editButton.editOption = false;
            this.editButton.inputClass = 'mt-1 block w-full bg-gray-200';
            this.editButton.selectInputClass = 'mt-1 block w-full border border-gray-200 bg-gray-200 py-2.5 px-4 pr-8 rounded leading-tight focus:outline-none';
            this.editButton.text = this.$t('words.edit');
            window.location.reload();
        },
        editInvoice() {
            if (!this.editButton.editOption) {
                this.editButton.editOption = true;
                this.editButton.inputClass = 'mt-1 block w-full bg-white';
                this.editButton.selectInputClass = "mt-1 block w-full border border-gray-200 bg-white py-2.5 px-4 pr-8 rounded leading-tight focus:outline-none"
                this.editButton.text = this.$t('words.save');


            } else {
                let newForm = {
                    grand_total: this.invoice.grand_total,
                    edit_amount_reason: this.invoice.edit_amount_reason,
                };
                if (confirm(this.$t('words.are-you-sure-to-delete'))) {
                    this.$wait.start('UPDATING_TRAINEE');
                    this.validationErrors = null;
                    this.$inertia.put(route('back.finance.invoices.update', this.invoice.id), newForm).then(response => {
                        Inertia.reload().then(() => {
                            this.editButton.editOption = false;
                            this.editButton.inputClass = 'mt-1 block w-full bg-gray-200';
                            this.editButton.selectInputClass = 'mt-1 block w-full border border-gray-200 bg-gray-200 py-2.5 px-4 pr-8 rounded leading-tight focus:outline-none';
                            this.editButton.text = this.$t('words.edit');
                            this.$wait.end('UPDATING_TRAINEE');
                        });
                    }).catch(error => {
                        this.$wait.end('UPDATING_TRAINEE');
                        if (error.response.status == 422) {
                            this.validationErrors = error.response.data.errors;
                        }
                    })
                }

            }
        },
        markUnderReview(invoice) {
            let reason = prompt(this.$t('words.reason'));
            if (reason === null || reason === '') {
                alert('يجب وجود سبب 😔');
                return;
            }

            if (confirm(this.$t('words.are-you-sure'))) {
                this.$inertia.post(route('back.finance.invoices.mark-under-review', invoice.id), {under_review_reason: reason});
                this.$confetti.start();
                setTimeout(() => {
                    this.$confetti.stop();
                }, 2000);
            }
        },
        deleteInvoice() {
            let reason = prompt(this.$t('words.reason'));
            if (reason === null || reason === '') {
                alert('يجب وجود سبب لحذف الفاتورة');
                return;
            }

            if (confirm(this.$t('words.are-you-sure'))) {
                this.$inertia.delete(route('back.finance.invoices.destroy', this.invoice.id), {data: {deleted_reason: reason}});
            }
        },
        rejectPaymentReceipt() {
            let reason = prompt(this.$t('words.rejection-reason'));
            if (reason === null) {
                return;
            }
            this.$inertia.post(route('back.finance.invoices.reject-payment-receipt', this.invoice.id), {
                reason: reason,
            });
        },
        markAsUnpaid() {
            let reason = prompt(this.$t('words.rejection-reason'));
            if (reason === null) {
                return;
            }
            this.$inertia.post(route('back.finance.invoices.mark-as-unpaid-from-chaser', this.invoice.id), {
                chased_note: reason,
            });
        },
        resetStatus() {
            if (confirm(this.$t('words.are-you-sure'))) {
                this.$inertia.post(route('back.finance.invoices.reset-status', this.invoice.id));
                this.$confetti.start();
                setTimeout(() => {
                    this.$confetti.stop();
                }, 2000);
            }
        },
        markAsPaid() {
            if (confirm(this.$t('words.are-you-sure'))) {
                this.$inertia.post(route('back.finance.invoices.mark-as-paid-from-chaser', this.invoice.id));
            }
        },
        markAsArchived() {
            if (confirm(this.$t('words.are-you-sure'))) {
                this.$inertia.post(route('back.finance.invoices.mark-as-archived', this.invoice.id));
                this.$confetti.start();
                setTimeout(() => {
                    this.$confetti.stop();
                }, 2000);
            }
        },
    }
}
</script>

<style lang="css">
</style>
