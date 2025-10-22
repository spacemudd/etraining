<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'trainees', link: route('back.trainees.index')},
                    {title_raw: leave.trainee.name, link: route('back.trainees.show', leave.trainee.id)},
                    {title: 'تعديل طلب الإجازة'},
                ]"
            ></breadcrumb-container>

            <div class="max-w-2xl mx-auto">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">تعديل طلب الإجازة</h2>
                        <p class="text-gray-600">للمتدرب: {{ leave.trainee.name }}</p>
                    </div>

                    <form @submit.prevent="submitForm" class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">نوع الإجازة</label>
                            <select 
                                v-model="form.leave_type" 
                                class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                required
                            >
                                <option value="">اختر نوع الإجازة</option>
                                <option value="أجازة وضع">أجازة وضع</option>
                            </select>
                            <p v-if="errors.leave_type" class="mt-1 text-sm text-red-600">{{ errors.leave_type[0] }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">ملف الإجازة الحالي</label>
                            <div v-if="leave.has_file" class="flex items-center gap-2 p-3 bg-gray-50 rounded-md">
                                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <button 
                                    @click="openCurrentFile"
                                    class="text-blue-600 hover:text-blue-800 flex items-center gap-1 cursor-pointer"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                    </svg>
                                    {{ leave.leave_file_name || 'عرض الملف الحالي' }}
                                </button>
                            </div>
                            
                            <div class="mt-3">
                                <label class="block text-sm font-medium text-gray-700 mb-2">استبدال الملف (اختياري)</label>
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-gray-400 transition-colors">
                                    <div class="space-y-1 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600">
                                            <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                                <span>رفع ملف جديد</span>
                                                <input 
                                                    id="file-upload" 
                                                    name="file-upload" 
                                                    type="file" 
                                                    class="sr-only"
                                                    @change="handleFileChange"
                                                    accept=".pdf,.jpg,.jpeg,.png"
                                                />
                                            </label>
                                            <p class="pl-1">أو اسحب وأفلت</p>
                                        </div>
                                        <p class="text-xs text-gray-500">PDF, JPG, PNG بحد أقصى 10 ميجابايت</p>
                                    </div>
                                </div>
                                <div v-if="selectedFile" class="mt-2 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="text-sm text-gray-600">{{ selectedFile.name }}</span>
                                    <button 
                                        type="button" 
                                        @click="removeFile"
                                        class="text-red-500 hover:text-red-700"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <p v-if="errors.leave_file" class="mt-1 text-sm text-red-600">{{ errors.leave_file[0] }}</p>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">من تاريخ</label>
                                <input 
                                    type="date" 
                                    v-model="form.from_date" 
                                    class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    required
                                />
                                <p v-if="errors.from_date" class="mt-1 text-sm text-red-600">{{ errors.from_date[0] }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">إلى تاريخ</label>
                                <input 
                                    type="date" 
                                    v-model="form.to_date" 
                                    class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    required
                                />
                                <p v-if="errors.to_date" class="mt-1 text-sm text-red-600">{{ errors.to_date[0] }}</p>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">ملاحظات (اختياري)</label>
                            <textarea 
                                v-model="form.notes" 
                                rows="4"
                                class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="أضف أي ملاحظات إضافية حول طلب الإجازة..."
                            ></textarea>
                            <p v-if="errors.notes" class="mt-1 text-sm text-red-600">{{ errors.notes[0] }}</p>
                        </div>
                        
                        <div class="flex gap-4 pt-6">
                            <button 
                                type="submit" 
                                class="flex-1 bg-blue-500 text-white font-semibold py-3 px-6 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
                                :disabled="loading"
                            >
                                <span v-if="loading" class="flex items-center justify-center">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    جاري التحديث...
                                </span>
                                <span v-else>تحديث طلب الإجازة</span>
                            </button>
                            
                            <inertia-link 
                                :href="route('back.trainees.show', leave.trainee.id)"
                                class="flex-1 bg-gray-300 text-gray-700 font-semibold py-3 px-6 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors text-center"
                            >
                                إلغاء
                            </inertia-link>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import BreadcrumbContainer from "@/Components/BreadcrumbContainer";
import { Inertia } from "@inertiajs/inertia";

export default {
    props: ['leave'],
    components: {
        AppLayout,
        BreadcrumbContainer,
    },
    data() {
        return {
            loading: false,
            selectedFile: null,
            form: {
                leave_type: this.leave.leave_type,
                from_date: this.leave.from_date_formatted,
                to_date: this.leave.to_date_formatted,
                notes: this.leave.notes || '',
                leave_file: null
            },
            errors: {}
        }
    },
    methods: {
        handleFileChange(event) {
            const file = event.target.files[0];
            if (file) {
                this.selectedFile = file;
                this.form.leave_file = file;
            }
        },
        
        removeFile() {
            this.selectedFile = null;
            this.form.leave_file = null;
            const fileInput = document.getElementById('file-upload');
            if (fileInput) {
                fileInput.value = '';
            }
        },
        
        async openCurrentFile() {
            if (!this.leave.has_file) {
                alert('عذراً، لا يوجد ملف مرفق.');
                return;
            }
            
            try {
                // إظهار رسالة تحميل
                const loadingMessage = document.createElement('div');
                loadingMessage.innerHTML = 'جاري تحميل الملف...';
                loadingMessage.style.cssText = 'position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: #333; color: white; padding: 20px; border-radius: 8px; z-index: 9999;';
                document.body.appendChild(loadingMessage);
                
                // الحصول على signed URL من الخادم
                const response = await axios.get(route('back.trainees.leaves.file-url', {
                    trainee_id: this.leave.trainee_id,
                    id: this.leave.id
                }));
                
                // إزالة رسالة التحميل
                document.body.removeChild(loadingMessage);
                
                if (response.data && response.data.file_url) {
                    // فتح الملف في نافذة جديدة
                    window.open(response.data.file_url, '_blank');
                } else {
                    alert('عذراً، لا يمكن الوصول إلى الملف في الوقت الحالي. يرجى المحاولة لاحقاً.');
                }
            } catch (error) {
                // إزالة رسالة التحميل في حالة الخطأ
                const loadingMessage = document.querySelector('div[style*="position: fixed"]');
                if (loadingMessage) {
                    document.body.removeChild(loadingMessage);
                }
                
                console.error('Error getting file URL:', error);
                
                if (error.response && error.response.status === 404) {
                    alert('الملف غير موجود.');
                } else if (error.response && error.response.data && error.response.data.error) {
                    alert('خطأ: ' + error.response.data.error);
                } else {
                    alert('عذراً، حدث خطأ أثناء فتح الملف. يرجى المحاولة مرة أخرى.');
                }
            }
        },
        
        submitForm() {
            this.loading = true;
            this.errors = {};
            
            const formData = new FormData();
            formData.append('leave_type', this.form.leave_type);
            formData.append('from_date', this.form.from_date);
            formData.append('to_date', this.form.to_date);
            formData.append('notes', this.form.notes);
            if (this.form.leave_file) {
                formData.append('leave_file', this.form.leave_file);
            }
            
            axios.put(route('back.trainees.leaves.update', { 
                trainee_id: this.leave.trainee.id, 
                leave: this.leave.id 
            }), formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            })
            .then(response => {
                Inertia.visit(route('back.trainees.show', this.leave.trainee.id));
            })
            .catch(error => {
                this.loading = false;
                if (error.response?.data?.errors) {
                    this.errors = error.response.data.errors;
                } else {
                    alert('حدث خطأ أثناء تحديث طلب الإجازة');
                }
            });
        }
    }
}
</script>

<style scoped>
/* Custom styles for better Arabic RTL support */
[dir="rtl"] .gap-2 > * + * {
    margin-right: 0.5rem;
    margin-left: 0;
}

[dir="rtl"] .gap-4 > * + * {
    margin-right: 1rem;
    margin-left: 0;
}

[dir="rtl"] .gap-6 > * + * {
    margin-right: 1.5rem;
    margin-left: 0;
}
</style>

