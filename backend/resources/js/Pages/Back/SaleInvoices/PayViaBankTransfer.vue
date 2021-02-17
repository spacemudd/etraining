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
        <main class="h-full overflow-y-auto">
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

                <!-- Uploading bank receipt -->
                <div class="mt-5 text-center">
                    <div class="flex w-full items-center justify-center bg-grey-lighter">
                        <label class="w-64 flex flex-col items-center px-4 py-6 bg-white text-blue rounded-lg shadow-lg tracking-wide uppercase border border-blue cursor-pointer hover:bg-blue">
                            <svg class="w-8 h-8" fill="#1c64f2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path d="M16.88 9.1A4 4 0 0 1 16 17H5a5 5 0 0 1-1-9.9V7a3 3 0 0 1 4.52-2.59A4.98 4.98 0 0 1 17 8c0 .38-.04.74-.12 1.1zM11 11h3l-4-4-4 4h3v3h2v-3z" />
                            </svg>
                            <span class="mt-2 text-base leading-normal">قم برفع إيصال التحويل</span>
                            <input type='file' class="hidden" />
                        </label>
                    </div>

                    <button class="mt-5 text-sm inline-flex items-center rounded-md px-4 py-2 bg-blue-600 hover:bg-blue-800 text-white">
                        ارسال
                    </button>
                </div>
            </div>
        </main>
    </div>
</template>

<script>
export default {
    props: ['saleInvoice'],
    name: "Show.vue"
}
</script>

<style scoped>

</style>
