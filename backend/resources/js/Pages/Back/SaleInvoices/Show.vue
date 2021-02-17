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
                    <td class="t">رقم المرجعي</td>
                </tr>
                <tr>
                    <td>{{ saleInvoice.grand_total_display }}</td>
                    <td class="t">الإجمالي</td>
                </tr>
                <tr>
                    <td>{{ saleInvoice.billable.name }}</td>
                    <td class="t">موجه الى</td>
                </tr>
            </table>
            </div>

                <hr class="my-5">

                <p class="text-right px-5">طريقة الدفع</p>

                <!-- Uploading bank receipt -->
                <div class="mt-5 text-center">
                    <inertia-link :href="route('sale-invoices.pay.bank-transfer', {sale_invoice: saleInvoice.id})"
                                  class="text-sm inline-flex items-center rounded-md px-4 py-2 bg-blue-600 hover:bg-blue-800 text-white">
                        تحويل بنكي
                    </inertia-link>
                </div>

                <div class="mt-5 text-center">
                    <div class="text-sm inline-flex items-center rounded-md px-4 py-2 bg-blue-600 hover:bg-blue-800 text-white">
                        الدفع اونلاين
                    </div>
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
