<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'trainees', link: route('back.trainees.index')},
                    {title_raw: leave.trainee.name, link: route('back.trainees.show', leave.trainee.id)},
                    {title: 'تفاصيل طلب الإجازة'},
                ]"
            ></breadcrumb-container>

            <div class="max-w-4xl mx-auto">
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <!-- Header -->
                    <div class="px-6 py-4 bg-gray-50 border-b">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900">تفاصيل طلب الإجازة</h2>
                                <p class="text-gray-600">للمتدرب: {{ leave.trainee.name }}</p>
                            </div>
                            <div class="flex gap-3">
                                <inertia-link 
                                    :href="route('back.trainees.leaves.edit', { trainee_id: leave.trainee.id, leave: leave.id })"
                                    class="bg-blue-500 text-white font-semibold px-4 py-2 rounded-md hover:bg-blue-600 transition-colors"
                                >
                                    تعديل
                                </inertia-link>
                                <inertia-link 
                                    :href="route('back.trainees.show', leave.trainee.id)"
                                    class="bg-gray-300 text-gray-700 font-semibold px-4 py-2 rounded-md hover:bg-gray-400 transition-colors"
                                >
                                    رجوع
                                </inertia-link>
                            </div>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Left Column -->
                            <div class="space-y-6">
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">معلومات الإجازة</h3>
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">نوع الإجازة</label>
                                            <div class="p-3 bg-gray-50 rounded-md">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium"
                                                      :class="{
                                                          'bg-blue-100 text-blue-800': leave.leave_type === 'أجازة وضع',
                                                          'bg-gray-100 text-gray-800': leave.leave_type !== 'أجازة وضع'
                                                      }">
                                                    {{ leave.leave_type }}
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">من تاريخ</label>
                                            <div class="p-3 bg-gray-50 rounded-md text-gray-900" dir="ltr">
                                                {{ leave.from_date_formatted }}
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">إلى تاريخ</label>
                                            <div class="p-3 bg-gray-50 rounded-md text-gray-900" dir="ltr">
                                                {{ leave.to_date_formatted }}
                                            </div>
                                        </div>
                                        
                                        <div v-if="leave.notes">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">ملاحظات</label>
                                            <div class="p-3 bg-gray-50 rounded-md text-gray-900">
                                                {{ leave.notes }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="space-y-6">
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">الملف المرفوع</h3>
                                    <div v-if="leave.has_file" class="space-y-4">
                                        <div class="p-4 bg-blue-50 border border-blue-200 rounded-md">
                                            <div class="flex items-center gap-3">
                                                <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                <div>
                                                    <p class="font-medium text-blue-900">{{ leave.leave_file_name || 'ملف الإجازة' }}</p>
                                                    <p class="text-sm text-blue-700">تم رفع الملف بنجاح</p>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="flex gap-3">
                                            <a 
                                                :href="leave.leave_file_url" 
                                                target="_blank"
                                                class="flex-1 bg-blue-500 text-white font-semibold px-4 py-2 rounded-md hover:bg-blue-600 transition-colors text-center flex items-center justify-center gap-2"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                                عرض الملف
                                            </a>
                                            
                                            <a 
                                                :href="leave.leave_file_url" 
                                                download
                                                class="flex-1 bg-green-500 text-white font-semibold px-4 py-2 rounded-md hover:bg-green-600 transition-colors text-center flex items-center justify-center gap-2"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                تحميل
                                            </a>
                                        </div>
                                    </div>
                                    
                                    <div v-else class="p-4 bg-gray-50 border border-gray-200 rounded-md text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <p class="mt-2 text-sm text-gray-500">لا يوجد ملف مرفوع</p>
                                    </div>
                                </div>

                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">معلومات إضافية</h3>
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">تاريخ الإنشاء</label>
                                            <div class="p-3 bg-gray-50 rounded-md text-gray-900" dir="ltr">
                                                {{ leave.created_at_timezone }}
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">آخر تحديث</label>
                                            <div class="p-3 bg-gray-50 rounded-md text-gray-900" dir="ltr">
                                                {{ leave.updated_at ? new Date(leave.updated_at).toLocaleString('ar-SA') : 'لم يتم التحديث' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import BreadcrumbContainer from "@/Components/BreadcrumbContainer";

export default {
    props: ['leave'],
    components: {
        AppLayout,
        BreadcrumbContainer,
    }
}
</script>

<style scoped>
/* Custom styles for better Arabic RTL support */
[dir="rtl"] .gap-2 > * + * {
    margin-right: 0.5rem;
    margin-left: 0;
}

[dir="rtl"] .gap-3 > * + * {
    margin-right: 0.75rem;
    margin-left: 0;
}

[dir="rtl"] .gap-4 > * + * {
    margin-right: 1rem;
    margin-left: 0;
}

[dir="rtl"] .gap-8 > * + * {
    margin-right: 2rem;
    margin-left: 0;
}
</style>

