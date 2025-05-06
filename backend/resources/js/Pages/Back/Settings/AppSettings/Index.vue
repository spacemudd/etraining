<!-- - Copyright (c) 2020 - Clarastars, LLC  - All Rights Reserved.
  -
  - Unauthorized copying of this file via any medium is strictly prohibited.
  - This file is a proprietary of Clarastars LLC and is confidential / educational purpose only.
  -
  - https://clarastars.com - info@clarastars.com
  - @author Shafiq al-Shaar <shafiqalshaar@gmail.com>
  -->

<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'settings', link: route('back.settings')},
                    {title: 'app-settings'},
                ]"
            ></breadcrumb-container>

            <div class="px-6">
                <div class="col-span-1 bg-white shadow-lg rounded-lg p-5">
                    <h2>{{ $t('words.app-settings') }}</h2>

                    <div class="mt-5 text-xs">
                        <form @submit.prevent="updateRequest">

                            <p class="mt-5">الحد الشهري لطلبات قوسي</p>

                            <div class="grid grid-cols-3 mt-2">
                                <div class="col-sm-1">
                                    <p class="text-gray-600 text-xs">القيمة الحالية: {{ gosi_monthly_requests }}</p>
                                    <input
                                        type="number"
                                        class="input w-full"
                                        v-model="updateForm.gosi_monthly_requests"
                                        min="0"
                                        required
                                    />
                                </div>
                            </div>

                            <hr class="my-6">

                            <p class="mt-5">إعدادات Google Sheet</p>

                            <div class="grid grid-cols-3 mt-2 gap-4">
                                <div class="col-span-1">
                                    <p class="text-gray-600 text-xs">معرّف الجدول</p>
                                    <input
                                        type="text"
                                        class="input w-full"
                                        v-model="updateForm.google_sheet_id"
                                        required
                                    />
                                </div>

                                <div class="col-span-1">
                                    <p class="text-gray-600 text-xs">النطاق (افتراضي: Sheet1!A:A)</p>
                                    <input
                                        type="text"
                                        class="input w-full"
                                        v-model="updateForm.google_sheet_range"
                                    />
                                </div>
                            </div>

                            <p class="mt-5">تنبيهات البريد الإلكتروني</p>
                            <p class="text-gray-500 text-xs mb-2">
                                إذا تم العثور على رقم الهوية في Google Sheet، سيتم إيقاف العملية تلقائيًا وإرسال تنبيه إلى البريد الإلكتروني المحدد.
                            </p>

                            <div class="grid grid-cols-3 mt-2 gap-4">
                                <div class="col-span-1">
                                    <p class="text-gray-600 text-xs">عناوين البريد الإلكتروني لتنبيهها إذا تم تعديل متدرب محجوز (مفصولة بفاصلة)</p>
                                    <input
                                        type="email"
                                        class="input w-full"
                                        v-model="updateForm.alert_email"
                                        required
                                    />
                                </div>
                            </div>

                            <p class="mt-5">تحميل مفتاح JSON لخدمة Google</p>
                            <p class="text-gray-500 text-xs mb-2">
                                سيتم استخدام هذا المفتاح للوصول إلى Google Sheets. تأكد من أن الملف بصيغة JSON وأنه تم الحصول عليه من Google Cloud Console.
                            </p>
                            <div class="grid grid-cols-3 mt-2 gap-4">
                                <div class="col-span-1">
                                    <input
                                        type="file"
                                        class="input w-full"
                                        @change="handleFileUpload"
                                        accept="application/json"
                                    />
                                    <div v-if="jsonUploadedMessage" class="text-green-600 text-sm mt-2">
                                        {{ jsonUploadedMessage }}
                                    </div>
                                    <div class="mt-2">
                                        <jet-button class="bg-red-500 hover:bg-red-600 text-white text-xs" @click="deleteJsonKey">
                                            حذف المفتاح
                                        </jet-button>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-5">
                                <jet-button class="col-xs-3" :class="{ 'opacity-25': updateForm.processing }" :disabled="updateForm.processing">
                                    {{ $t('words.save') }}
                                </jet-button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import IconNavigate from 'vue-ionicons/dist/ios-arrow-dropright'
import BreadcrumbContainer from "@/Components/BreadcrumbContainer";
import JetButton from '@/Jetstream/Button'

export default {
    metaInfo: { title: 'Settings' },
    // layout: Layout,
    components: {
        IconNavigate,
        AppLayout,
        BreadcrumbContainer,
        JetButton,
    },
    props: ['gosi_monthly_requests', 'google_sheet_id', 'google_sheet_range', 'alert_email', 'json_key_uploaded'],
    data() {
        return {
            updateForm: this.$inertia.form({
                gosi_monthly_requests: '',
                google_sheet_id: '',
                google_sheet_range: '',
                alert_email: '',
            }, {
                bag: 'updateForm',
                resetOnSuccess: false,
            }),
            jsonUploadedMessage: '',
        }
    },
    mounted() {
        this.updateForm.gosi_monthly_requests = this.gosi_monthly_requests;
        this.updateForm.google_sheet_id = this.google_sheet_id;
        this.updateForm.google_sheet_range = this.google_sheet_range;
        this.updateForm.alert_email = this.alert_email;
        if (this.json_key_uploaded) {
            this.jsonUploadedMessage = 'تم رفع المفتاح مسبقًا.';
        }
    },
    methods: {
        updateRequest() {
            this.updateForm.put(route('back.settings.app.update'), {
                onSuccess: () => {
                    alert('Settings updated successfully.');
                },
            });
        },
        handleFileUpload(event) {
            const file = event.target.files[0];
            if (!file) return;

            const formData = new FormData();
            formData.append('json_key', file);

            this.$inertia.post(route('back.settings.app.uploadJsonKey'), formData, {
                preserveScroll: true,
                onSuccess: () => {
                    this.jsonUploadedMessage = 'تم رفع المفتاح بنجاح.';
                }
            });
        },
        deleteJsonKey() {
            if (!confirm('هل أنت متأكد من حذف المفتاح؟')) return;
            this.$inertia.delete(route('back.settings.app.deleteJsonKey'), {
                preserveScroll: true,
                onSuccess: () => {
                    this.jsonUploadedMessage = '';
                    alert('تم حذف المفتاح بنجاح.');
                }
            });
        },
    },
}
</script>
