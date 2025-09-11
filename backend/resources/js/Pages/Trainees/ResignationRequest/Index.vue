<template>
    <app-layout>
        <div class="container px-6 mx-auto grid">
            <div class="bg-white rounded-lg p-10 flex gap-10">
                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-20 w-20 rounded-full bg-red-100">
                    <ion-icon name="exit-outline" class="h-10 w-10 text-red-600"></ion-icon>
                </div>
                <div class="text-center w-full">
                    <h1 class="text-3xl font-bold text-gray-900 mb-4">طلب إستقالة</h1>
                    <p class="text-lg text-gray-600 mb-8">
                        يرجى التأكد من بياناتك قبل إرسال طلب الاستقالة
                    </p>
                    
                    <!-- Trainee Info Card -->
                    <div class="bg-gray-50 p-6 rounded-lg mb-8 text-right">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">بيانات المتدرب</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-700">الاسم:</span>
                                <span class="text-sm text-gray-900">{{ trainee.name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-700">رقم الهوية:</span>
                                <span class="text-sm text-gray-900">{{ trainee.identity_number }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-700">البريد الإلكتروني:</span>
                                <span class="text-sm text-gray-900">{{ trainee.email }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-700">رقم الهاتف:</span>
                                <span class="text-sm text-gray-900">{{ trainee.phone || 'غير محدد' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Existing Request Status -->
                    <div v-if="existingRequest" class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <ion-icon name="checkmark-circle-outline" class="h-8 w-8 text-blue-600"></ion-icon>
                            </div>
                            <div class="mr-3">
                                <h3 class="text-lg font-medium text-blue-800">تم إرسال طلب الاستقالة</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <p><strong>الحالة:</strong> {{ existingRequest.status_text }}</p>
                                    <p><strong>تاريخ الإرسال:</strong> {{ existingRequest.created_at }}</p>
                                    <p v-if="existingRequest.processed_at"><strong>تاريخ المعالجة:</strong> {{ existingRequest.processed_at }}</p>
                                    <p v-if="existingRequest.admin_notes"><strong>ملاحظات الإدارة:</strong> {{ existingRequest.admin_notes }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form -->
                    <form v-if="!existingRequest" @submit.prevent="submitResignation" class="space-y-6">

                        <!-- Contact Phone -->
                        <div>
                            <label for="contact_phone" class="block text-sm font-medium text-gray-700 mb-2">
                                رقم التواصل الإضافي (اختياري)
                            </label>
                            <input
                                type="text"
                                id="contact_phone"
                                v-model="form.contact_phone"
                                class="w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 text-center text-lg"
                                placeholder="أدخل رقم للتواصل معك"
                            />
                        </div>

                        <!-- Confirmation Checkbox -->
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input
                                        id="confirmation"
                                        v-model="form.confirmation"
                                        type="checkbox"
                                        class="focus:ring-red-500 h-4 w-4 text-red-600 border-gray-300 rounded"
                                        required
                                    />
                                </div>
                                <div class="mr-3 text-sm">
                                    <label for="confirmation" class="font-medium text-gray-700">
                                        أؤكد أنني أريد تقديم طلب استقالة *
                                    </label>
                                    <p class="text-gray-600 mt-1">
                                        سيتم إرسال طلبك إلى الإدارة وسيتم التواصل معك قريباً
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-center">
                            <button
                                type="submit"
                                :disabled="!form.confirmation || loading"
                                class="w-full sm:w-auto px-8 py-3 bg-red-600 text-white font-semibold rounded-lg shadow-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-150"
                            >
                                <span v-if="loading">جاري الإرسال...</span>
                                <span v-else>إرسال طلب الاستقالة</span>
                            </button>
                        </div>

                        <!-- Error Message -->
                        <div v-if="errors.error" class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <p class="text-sm text-red-600">{{ errors.error }}</p>
                        </div>

                        <!-- Success Message -->
                        <div v-if="successMessage" class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <ion-icon name="checkmark-circle-outline" class="h-5 w-5 text-green-400"></ion-icon>
                                </div>
                                <div class="mr-3">
                                    <p class="text-sm font-medium text-green-800">{{ successMessage }}</p>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Message when request already exists -->
                    <div v-if="existingRequest" class="text-center py-8">
                        <p class="text-gray-600">لا يمكن إرسال طلب استقالة جديد لأن لديك طلب سابق قيد المعالجة.</p>
                    </div>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayoutTrainee'
import axios from 'axios';

export default {
    components: {
        AppLayout,
    },
    props: {
        trainee: {
            type: Object,
            required: true
        },
        existingRequest: {
            type: Object,
            default: null
        }
    },
    data() {
        return {
        form: {
            contact_phone: '',
            confirmation: false
        },
            errors: {},
            successMessage: '',
            loading: false
        }
    },
    methods: {
        async submitResignation() {
            this.errors = {};
            this.successMessage = '';
            this.loading = true;
            
            try {
                const response = await axios.post(route('trainees.resignation-request.store'), this.form);
                
                // التحقق من وجود رسالة نجاح في الاستجابة
                if (response.data && response.data.success) {
                    this.successMessage = response.data.success;
                } else if (response.data && response.data.message) {
                    this.successMessage = response.data.message;
                } else {
                    this.successMessage = 'تم إرسال طلب الاستقالة بنجاح. سيتم التواصل معك قريباً.';
                }
                
                // إعادة تعيين النموذج
                this.form = {
                    contact_phone: '',
                    confirmation: false
                };
                
                // إعادة تحميل الصفحة بعد 2 ثانية لعرض الحالة الجديدة
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
                
            } catch (error) {
                console.error('Error submitting resignation request:', error);
                
                if (error.response) {
                    // Server responded with error status
                    if (error.response.data && error.response.data.errors) {
                        this.errors = error.response.data.errors;
                    } else if (error.response.data && error.response.data.error) {
                        this.errors = {
                            error: error.response.data.error
                        };
                    } else {
                        this.errors = {
                            error: 'حدث خطأ أثناء إرسال الطلب. يرجى المحاولة مرة أخرى.'
                        };
                    }
                } else if (error.request) {
                    // Request was made but no response received
                    this.errors = {
                        error: 'لا يمكن الاتصال بالخادم. يرجى التحقق من اتصال الإنترنت.'
                    };
                } else {
                    // Something else happened
                    this.errors = {
                        error: 'حدث خطأ غير متوقع. يرجى المحاولة مرة أخرى.'
                    };
                }
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
