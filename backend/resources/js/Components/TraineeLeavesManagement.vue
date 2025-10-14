<template>
    <div class="bg-white rounded-lg shadow-lg overflow-hidden border border-gray-200">
        <!-- Header Section -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="bg-white/20 rounded-full p-2">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white">طلبات الإجازة</h3>
                        <p class="text-blue-100 text-sm">إدارة طلبات الإجازة للمتدرب</p>
                    </div>
                </div>
                <button 
                    @click="openCreateModal" 
                    class="bg-white text-blue-600 hover:bg-blue-50 font-semibold px-6 py-3 rounded-lg flex items-center gap-2 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    طلب إجازة جديد
                </button>
            </div>
        </div>

        <!-- Table Section -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700 tracking-wider">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                                نوع الإجازة
                            </div>
                        </th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700 tracking-wider">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                الملف المرفوع
                            </div>
                        </th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700 tracking-wider">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                الفترة الزمنية
                            </div>
                        </th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700 tracking-wider">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                تاريخ الطلب
                            </div>
                        </th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700 tracking-wider">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                الإجراءات
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="leave in leaves" :key="leave.id" class="hover:bg-blue-50 transition-colors duration-200">
                        <!-- نوع الإجازة -->
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                                      :class="{
                                          'bg-pink-100 text-pink-800 border border-pink-200': leave.leave_type === 'أجازة وضع',
                                          'bg-blue-100 text-blue-800 border border-blue-200': leave.leave_type === 'أجازة مرضية',
                                          'bg-green-100 text-green-800 border border-green-200': leave.leave_type === 'أجازة سنوية',
                                          'bg-yellow-100 text-yellow-800 border border-yellow-200': leave.leave_type === 'أجازة طارئة',
                                          'bg-gray-100 text-gray-800 border border-gray-200': !['أجازة وضع', 'أجازة مرضية', 'أجازة سنوية', 'أجازة طارئة'].includes(leave.leave_type)
                                      }">
                                    <svg v-if="leave.leave_type === 'أجازة وضع'" class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <svg v-else-if="leave.leave_type === 'أجازة مرضية'" class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                    </svg>
                                    <svg v-else-if="leave.leave_type === 'أجازة سنوية'" class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <svg v-else class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ leave.leave_type }}
                                </span>
                            </div>
                        </td>

                                                 <!-- الملف المرفوع -->
                         <td class="px-6 py-4">
                             <div v-if="leave.has_file && leave.leave_file_url" class="flex items-center">
                                 <a 
                                     :href="leave.leave_file_url" 
                                     target="_blank"
                                     @click="handleFileClick($event, leave)"
                                     class="inline-flex items-center px-3 py-2 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition-colors duration-200 border border-green-200 group"
                                     title="انقر لفتح الملف"
                                 >
                                     <svg class="w-4 h-4 mr-2 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                     </svg>
                                     <span class="text-sm font-medium">{{ leave.leave_file_name || 'عرض الملف' }}</span>
                                 </a>
                             </div>
                             <div v-else-if="leave.has_file && !leave.leave_file_url" class="flex items-center">
                                 <span class="inline-flex items-center px-3 py-2 bg-red-50 text-red-600 rounded-lg border border-red-200">
                                     <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                     </svg>
                                     <span class="text-sm">خطأ في تحميل الملف</span>
                                 </span>
                             </div>
                             <div v-else class="flex items-center">
                                 <span class="inline-flex items-center px-3 py-2 bg-gray-50 text-gray-500 rounded-lg border border-gray-200">
                                     <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                     </svg>
                                     <span class="text-sm">لا يوجد ملف</span>
                                 </span>
                             </div>
                         </td>

                        <!-- الفترة الزمنية -->
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <div class="flex items-center gap-2 text-sm">
                                    <span class="text-gray-600">من:</span>
                                    <span class="font-medium text-gray-900 bg-blue-50 px-2 py-1 rounded" dir="ltr">{{ leave.from_date_formatted }}</span>
                                </div>
                                <div class="flex items-center gap-2 text-sm mt-1">
                                    <span class="text-gray-600">إلى:</span>
                                    <span class="font-medium text-gray-900 bg-blue-50 px-2 py-1 rounded" dir="ltr">{{ leave.to_date_formatted }}</span>
                                </div>
                            </div>
                        </td>

                        <!-- تاريخ الطلب -->
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <span class="text-sm text-gray-600 bg-gray-50 px-3 py-1 rounded-lg border" dir="ltr">
                                    {{ leave.created_at_timezone }}
                                </span>
                            </div>
                        </td>

                        <!-- الإجراءات -->
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <button 
                                    @click="editLeave(leave)" 
                                    class="inline-flex items-center px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200 shadow-sm hover:shadow-md"
                                    title="تعديل الطلب"
                                >
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    تعديل
                                </button>
                                <button 
                                    @click="confirmDeleteLeave(leave.id)" 
                                    class="inline-flex items-center px-3 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors duration-200 shadow-sm hover:shadow-md"
                                    title="حذف الطلب"
                                >
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    حذف
                                </button>
                            </div>
                        </td>
                    </tr>

                                         <!-- Loading State -->
                     <tr v-if="loadingLeaves">
                         <td colspan="5" class="px-6 py-12 text-center">
                             <div class="flex flex-col items-center gap-4">
                                 <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                                 <div>
                                     <h3 class="text-lg font-medium text-gray-900 mb-2">جاري التحميل...</h3>
                                     <p class="text-gray-500">يرجى الانتظار</p>
                                 </div>
                             </div>
                         </td>
                     </tr>
                     
                     <!-- Empty State -->
                     <tr v-else-if="!leaves.length">
                         <td colspan="5" class="px-6 py-12 text-center">
                             <div class="flex flex-col items-center gap-4">
                                 <div class="bg-gray-100 rounded-full p-6">
                                     <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                     </svg>
                                 </div>
                                 <div>
                                     <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد طلبات إجازة</h3>
                                     <p class="text-gray-500">لم يتم إرسال أي طلبات إجازة بعد</p>
                                 </div>
                                 <button 
                                     @click="openCreateModal" 
                                     class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200"
                                 >
                                     <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                     </svg>
                                     إرسال أول طلب إجازة
                                 </button>
                             </div>
                         </td>
                     </tr>
                </tbody>
            </table>
        </div>

        <!-- Create/Edit Modal -->
        <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-10 mx-auto p-5 border w-full max-w-md shadow-2xl rounded-xl bg-white">
                <div class="mt-3">
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <div class="bg-blue-100 rounded-full p-2">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">
                                    {{ editingLeave ? 'تعديل طلب الإجازة' : 'طلب إجازة جديد' }}
                                </h3>
                                <p class="text-gray-500 text-sm">{{ editingLeave ? 'تحديث بيانات الطلب' : 'إرسال طلب إجازة جديد' }}</p>
                            </div>
                        </div>
                        <button @click="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Modal Form -->
                    <form @submit.prevent="submitForm" class="space-y-6">
                        <!-- نوع الإجازة -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">نوع الإجازة</label>
                            <select 
                                v-model="form.leave_type" 
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                required
                            >
                                <option value="">اختر نوع الإجازة</option>
                                <option value="أجازة وضع">أجازة وضع</option>
                            </select>
                        </div>
                        
                        <!-- ملف الإجازة -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">ملف الإجازة</label>
                            
                            <!-- عرض الملف المختار -->
                            <div v-if="form.leave_file" class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg class="w-8 h-8 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <div>
                                            <p class="text-sm font-medium text-green-800">{{ form.leave_file.name }}</p>
                                            <p class="text-xs text-green-600">{{ formatFileSize(form.leave_file.size) }}</p>
                                        </div>
                                    </div>
                                    <button 
                                        type="button" 
                                        @click="removeFile"
                                        class="text-red-600 hover:text-red-800 transition-colors duration-200"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- منطقة رفع الملف -->
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition-colors duration-200"
                                 :class="{ 'border-green-400 bg-green-50': form.leave_file }">
                                <input 
                                    type="file" 
                                    @change="handleFileChange"
                                    accept=".pdf,.jpg,.jpeg,.png"
                                    class="hidden"
                                    id="file-input"
                                    :required="!editingLeave"
                                />
                                <label for="file-input" class="cursor-pointer">
                                    <svg class="w-12 h-12 mx-auto mb-4 transition-colors duration-200"
                                         :class="form.leave_file ? 'text-green-500' : 'text-gray-400'"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                    <p class="text-sm mb-2 transition-colors duration-200"
                                       :class="form.leave_file ? 'text-green-600' : 'text-gray-600'">
                                        {{ form.leave_file ? 'تم اختيار الملف بنجاح' : 'انقر لاختيار الملف أو اسحبه هنا' }}
                                    </p>
                                    <p class="text-xs text-gray-500">PDF, JPG, PNG (حد أقصى 10 ميجابايت)</p>
                                </label>
                            </div>
                        </div>
                        
                        <!-- التواريخ -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">من تاريخ</label>
                                <input 
                                    type="date" 
                                    v-model="form.from_date" 
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                    required
                                />
                            </div>
                            
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">إلى تاريخ</label>
                                <input 
                                    type="date" 
                                    v-model="form.to_date" 
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                    required
                                />
                            </div>
                        </div>
                        
                        <!-- ملاحظات -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">ملاحظات (اختياري)</label>
                            <textarea 
                                v-model="form.notes" 
                                rows="3"
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 resize-none"
                                placeholder="أضف أي ملاحظات إضافية..."
                            ></textarea>
                        </div>

                        <!-- إعدادات البريد الإلكتروني -->
                        <div v-if="form.leave_type === 'أجازة وضع'" class="bg-blue-50 border border-blue-200 rounded-lg p-4 space-y-4">
                            <div class="flex items-center gap-3">
                                <input 
                                    type="checkbox" 
                                    id="send-email"
                                    v-model="form.send_email"
                                    class="w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                                />
                                <label for="send-email" class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    إرسال إشعار بالبريد الإلكتروني
                                </label>
                            </div>

                            <div v-if="form.send_email" class="space-y-4 pt-2">
                                <!-- إلى (TO) -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">إلى (TO)</label>
                                    <textarea 
                                        v-model="form.email_to"
                                        rows="2"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 resize-none text-sm"
                                        placeholder="أدخل عناوين البريد الإلكتروني مفصولة بفواصل"
                                    ></textarea>
                                    <p class="text-xs text-gray-500 mt-1">يتم ملء هذا الحقل تلقائياً بإيميل الشركة</p>
                                </div>

                                <!-- نسخة (CC) -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">نسخة (CC)</label>
                                    <textarea 
                                        v-model="form.email_cc"
                                        rows="2"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 resize-none text-sm"
                                        placeholder="أدخل عناوين البريد الإلكتروني مفصولة بفواصل"
                                    ></textarea>
                                </div>

                                <!-- نسخة مخفية (BCC) -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">نسخة مخفية (BCC)</label>
                                    <textarea 
                                        v-model="form.email_bcc"
                                        rows="3"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 resize-none text-sm"
                                        placeholder="أدخل عناوين البريد الإلكتروني مفصولة بفواصل"
                                    ></textarea>
                                    <p class="text-xs text-gray-500 mt-1">يتم ملء هذا الحقل تلقائياً بالإيميلات المحددة</p>
                                </div>

                                <!-- معاينة محتوى الإيميل -->
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        معاينة محتوى الإيميل
                                    </h4>
                                    <div class="text-xs text-gray-600 leading-relaxed">
                                        <p><strong>الموضوع:</strong> إشعار إجازة وضع - {{ emailDefaults.trainee_name }}</p>
                                        <p class="mt-2">نود إحاطتكم علماً بأن السيدة <strong>{{ emailDefaults.trainee_name }}</strong> قد تم منحها إجازة وضع اعتباراً من تاريخ <strong>{{ form.from_date }}</strong> وحتى تاريخ <strong>{{ form.to_date }}</strong>.</p>
                                        <p class="mt-2 text-gray-500">وبناءً على نظام التأمينات الاجتماعية واللوائح المعمول بها، نود إحاطتكم علماً بأن هذه الإجازة مدفوعة الأجر من التأمينات الاجتماعية...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- أزرار الإجراءات -->
                        <div class="flex gap-3 pt-4">
                            <button 
                                type="submit" 
                                class="flex-1 bg-blue-600 text-white font-semibold py-3 px-6 rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105"
                                :disabled="loading"
                            >
                                <span v-if="loading" class="flex items-center justify-center">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    جاري الإرسال...
                                </span>
                                <span v-else class="flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    {{ editingLeave ? 'تحديث الطلب' : 'إرسال الطلب' }}
                                </span>
                            </button>
                            <button 
                                type="button" 
                                @click="closeModal"
                                class="flex-1 bg-gray-300 text-gray-700 font-semibold py-3 px-6 rounded-lg hover:bg-gray-400 transition-all duration-200"
                            >
                                إلغاء
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['trainee_id'],
    data() {
        return {
            leaves: [],
            showModal: false,
            editingLeave: null,
            loading: false,
            loadingLeaves: false,
            form: {
                leave_type: '',
                from_date: '',
                to_date: '',
                notes: '',
                leave_file: null,
                send_email: false,
                email_to: '',
                email_cc: '',
                email_bcc: ''
            },
            emailDefaults: {
                company_email: '',
                default_bcc_emails: '',
                trainee_name: '',
                company_name: ''
            }
        }
    },
    mounted() {
        this.getLeaves();
    },
    watch: {
        'form.leave_type'(newVal) {
            if (newVal === 'أجازة وضع') {
                this.form.send_email = true;
                this.loadEmailDefaults();
            } else {
                this.form.send_email = false;
            }
        }
    },
    methods: {
        getLeaves() {
            this.loadingLeaves = true;
            axios.get(route('back.trainees.leaves.index', { trainee_id: this.trainee_id }))
            .then(response => {
                this.leaves = response.data;
            })
            .catch(error => {
                console.error('Error loading leaves:', error);
                // إضافة رسالة خطأ للمستخدم
                if (error.response && error.response.data && error.response.data.error) {
                    alert('خطأ في تحميل البيانات: ' + error.response.data.error);
                } else {
                    alert('حدث خطأ أثناء تحميل البيانات. يرجى المحاولة مرة أخرى.');
                }
            })
            .finally(() => {
                this.loadingLeaves = false;
            });
        },
        
        openCreateModal() {
            this.editingLeave = null;
            this.resetForm();
            this.loadEmailDefaults();
            this.showModal = true;
        },
        
        editLeave(leave) {
            this.editingLeave = leave;
            this.form = {
                leave_type: leave.leave_type,
                from_date: leave.from_date_formatted,
                to_date: leave.to_date_formatted,
                notes: leave.notes || '',
                leave_file: null,
                send_email: false,
                email_to: '',
                email_cc: '',
                email_bcc: ''
            };
            this.loadEmailDefaults();
            this.showModal = true;
        },
        
        closeModal() {
            this.showModal = false;
            this.editingLeave = null;
            this.resetForm();
        },
        
        resetForm() {
            this.form = {
                leave_type: '',
                from_date: '',
                to_date: '',
                notes: '',
                leave_file: null,
                send_email: false,
                email_to: '',
                email_cc: '',
                email_bcc: ''
            };
        },

        loadEmailDefaults() {
            console.log('Loading email defaults for trainee:', this.trainee_id);
            
            axios.get(route('back.trainees.leaves.email-defaults', { trainee_id: this.trainee_id }))
            .then(response => {
                console.log('Email defaults loaded successfully:', response.data);
                
                this.emailDefaults = response.data;
                // ملء الحقول تلقائياً
                this.form.email_to = response.data.company_email;
                this.form.email_bcc = response.data.default_bcc_emails;
                
                console.log('Email form fields populated:', {
                    email_to: this.form.email_to,
                    email_bcc: this.form.email_bcc,
                    company_name: response.data.company_name,
                    trainee_name: response.data.trainee_name
                });
            })
            .catch(error => {
                console.error('Error loading email defaults:', error);
                console.error('Error response:', error.response);
                console.error('Error data:', error.response?.data);
            });
        },
        
        handleFileChange(event) {
            this.form.leave_file = event.target.files[0];
        },
        
        removeFile() {
            this.form.leave_file = null;
            // إعادة تعيين input file
            const fileInput = document.getElementById('file-input');
            if (fileInput) {
                fileInput.value = '';
            }
        },
        
        formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        },
        
        submitForm() {
            if (this.editingLeave) {
                this.updateLeave();
            } else {
                this.createLeave();
            }
        },
        
        createLeave() {
            this.loading = true;
            
            // تسجيل بداية العملية
            console.log('Creating maternity leave request:', {
                trainee_id: this.trainee_id,
                leave_type: this.form.leave_type,
                from_date: this.form.from_date,
                to_date: this.form.to_date,
                send_email: this.form.send_email,
                email_data: {
                    to: this.form.email_to,
                    cc: this.form.email_cc,
                    bcc: this.form.email_bcc
                }
            });
            
            const formData = new FormData();
            formData.append('leave_type', this.form.leave_type);
            formData.append('from_date', this.form.from_date);
            formData.append('to_date', this.form.to_date);
            formData.append('notes', this.form.notes);
            if (this.form.leave_file) {
                formData.append('leave_file', this.form.leave_file);
                console.log('File attached:', this.form.leave_file.name, 'Size:', this.form.leave_file.size);
            }
            
            // إضافة حقول البريد الإلكتروني
            formData.append('send_email', this.form.send_email ? '1' : '0');
            if (this.form.send_email) {
                formData.append('email_to', this.form.email_to);
                formData.append('email_cc', this.form.email_cc);
                formData.append('email_bcc', this.form.email_bcc);
                
                console.log('Email data being sent:', {
                    send_email: true,
                    email_to: this.form.email_to,
                    email_cc: this.form.email_cc,
                    email_bcc: this.form.email_bcc
                });
            } else {
                console.log('Email sending is disabled');
            }
            
            console.log('Sending request to:', route('back.trainees.leaves.store', { trainee_id: this.trainee_id }));
            
            axios.post(route('back.trainees.leaves.store', { trainee_id: this.trainee_id }), formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            })
            .then(response => {
                console.log('Leave request created successfully:', response.data);
                this.getLeaves();
                this.closeModal();
                this.$inertia.reload();
            })
            .catch(error => {
                console.error('Error creating leave:', error);
                console.error('Error response:', error.response);
                console.error('Error data:', error.response?.data);
                
                if (error.response?.data?.errors) {
                    // Handle validation errors
                    Object.keys(error.response.data.errors).forEach(key => {
                        console.error('Validation error for', key, ':', error.response.data.errors[key]);
                        alert(error.response.data.errors[key][0]);
                    });
                } else {
                    alert('حدث خطأ أثناء إرسال الطلب. يرجى المحاولة مرة أخرى.');
                }
            })
            .finally(() => {
                this.loading = false;
                console.log('Leave creation request completed');
            });
        },
        
        updateLeave() {
            this.loading = true;
            
            const formData = new FormData();
            formData.append('leave_type', this.form.leave_type);
            formData.append('from_date', this.form.from_date);
            formData.append('to_date', this.form.to_date);
            formData.append('notes', this.form.notes);
            if (this.form.leave_file) {
                formData.append('leave_file', this.form.leave_file);
            }
            
            axios.put(route('back.trainees.leaves.update', { 
                trainee_id: this.trainee_id, 
                leave: this.editingLeave.id 
            }), formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            })
            .then(response => {
                this.getLeaves();
                this.closeModal();
                this.$inertia.reload();
            })
            .catch(error => {
                console.error('Error updating leave:', error);
                if (error.response?.data?.errors) {
                    Object.keys(error.response.data.errors).forEach(key => {
                        alert(error.response.data.errors[key][0]);
                    });
                }
            })
            .finally(() => {
                this.loading = false;
            });
        },
        
        confirmDeleteLeave(leaveId) {
            if (confirm('هل أنت متأكد من حذف طلب الإجازة هذا؟')) {
                axios.delete(route('back.trainees.leaves.destroy', { 
                    trainee_id: this.trainee_id, 
                    id: leaveId 
                }))
                .then(response => {
                    this.getLeaves();
                    // إضافة رسالة نجاح
                    if (response.data && response.data.message) {
                        alert(response.data.message);
                    }
                })
                .catch(error => {
                    console.error('Error deleting leave:', error);
                    // إضافة رسالة خطأ
                    if (error.response && error.response.data && error.response.data.message) {
                        alert('خطأ في حذف الإجازة: ' + error.response.data.message);
                    } else {
                        alert('حدث خطأ أثناء حذف الإجازة');
                    }
                });
            }
        },
        
        handleFileClick(event, leave) {
            // التحقق من أن الملف متاح قبل فتحه
            if (!leave.leave_file_url) {
                event.preventDefault();
                alert('عذراً، لا يمكن الوصول إلى الملف في الوقت الحالي. يرجى المحاولة لاحقاً.');
                return;
            }
            
            // محاولة فتح الملف في نافذة جديدة
            try {
                window.open(leave.leave_file_url, '_blank');
            } catch (error) {
                console.error('Error opening file:', error);
                alert('عذراً، حدث خطأ أثناء فتح الملف. يرجى المحاولة مرة أخرى.');
            }
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

[dir="rtl"] .gap-3 > * + * {
    margin-right: 0.75rem;
    margin-left: 0;
}

[dir="rtl"] .gap-4 > * + * {
    margin-right: 1rem;
    margin-left: 0;
}

/* Smooth transitions */
.transition-all {
    transition: all 0.2s ease-in-out;
}

/* Hover effects */
.hover\:scale-105:hover {
    transform: scale(1.05);
}

/* Custom scrollbar */
.overflow-x-auto::-webkit-scrollbar {
    height: 8px;
}

.overflow-x-auto::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>

