<template>
    <div class="bg-white rounded shadow overflow-x-auto">
        <div class="flex justify-between items-center mx-5 mt-5 mb-4">
            <h3 class="text-lg font-medium text-gray-900">طلبات الإجازة</h3>
            <button 
                @click="openCreateModal" 
                class="bg-blue-500 hover:bg-blue-600 text-white font-semibold px-4 py-2 rounded-lg flex items-center gap-2 transition-colors"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                طلب إجازة
            </button>
        </div>
        
        <table class="w-full whitespace-no-wrap">
            <colgroup>
                <col>
                <col>
                <col>
                <col>
                <col width="120px">
            </colgroup>
            <thead class="text-left font-bold">
                <th class="px-6 pt-6 pb-4">نوع الإجازة</th>
                <th class="px-6 pt-6 pb-4">الملف المرفوع</th>
                <th class="px-6 pt-6 pb-4">من تاريخ</th>
                <th class="px-6 pt-6 pb-4">إلى تاريخ</th>
                <th class="px-6 pt-6 pb-4">الإجراءات</th>
            </thead>
            <tbody>
                <tr v-for="leave in leaves" :key="leave.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
                    <td class="border-t px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                              :class="{
                                  'bg-blue-100 text-blue-800': leave.leave_type === 'أجازة وضع',
                                  'bg-gray-100 text-gray-800': leave.leave_type !== 'أجازة وضع'
                              }">
                            {{ leave.leave_type }}
                        </span>
                    </td>
                                         <td class="border-t px-6 py-4">
                         <div v-if="leave.has_file && leave.leave_file_url" class="flex items-center gap-2">
                             <a 
                                 :href="leave.leave_file_url" 
                                 target="_blank"
                                 class="text-blue-600 hover:text-blue-800 flex items-center gap-1 underline"
                                 title="انقر لفتح الملف"
                             >
                                 <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                 </svg>
                                 {{ leave.leave_file_name || 'عرض الملف' }}
                             </a>
                         </div>
                         <span v-else class="text-gray-500 text-sm">لا يوجد ملف</span>
                     </td>
                    <td class="border-t px-6 py-4" dir="ltr">{{ leave.from_date_formatted }}</td>
                    <td class="border-t px-6 py-4" dir="ltr">{{ leave.to_date_formatted }}</td>
                    <td class="border-t px-6 py-4">
                        <div class="flex gap-2">
                            <button 
                                @click="editLeave(leave)" 
                                class="bg-blue-500 text-white font-semibold px-3 py-1 text-center rounded text-sm hover:bg-blue-600 transition-colors"
                            >
                                تعديل
                            </button>
                            <button 
                                @click="confirmDeleteLeave(leave.id)" 
                                class="bg-red-500 text-white font-semibold px-3 py-1 text-center rounded text-sm hover:bg-red-600 transition-colors"
                            >
                                حذف
                            </button>
                        </div>
                    </td>
                </tr>
                <tr v-if="!leaves.length">
                    <td colspan="5" class="border-t text-center py-8 text-gray-500">
                        <div class="flex flex-col items-center gap-2">
                            <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span>لا توجد طلبات إجازة</span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Create/Edit Modal -->
        <div v-if="showModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">
                            {{ editingLeave ? 'تعديل طلب الإجازة' : 'طلب إجازة جديد' }}
                        </h3>
                        <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <form @submit.prevent="submitForm" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">نوع الإجازة</label>
                            <select 
                                v-model="form.leave_type" 
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required
                            >
                                <option value="">اختر نوع الإجازة</option>
                                <option value="أجازة وضع">أجازة وضع</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">ملف الإجازة</label>
                            <input 
                                type="file" 
                                @change="handleFileChange"
                                accept=".pdf,.jpg,.jpeg,.png"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                :required="!editingLeave"
                            />
                            <p class="text-xs text-gray-500 mt-1">يُسمح بملفات PDF أو صور (JPG, PNG) بحد أقصى 10 ميجابايت</p>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">من تاريخ</label>
                                <input 
                                    type="date" 
                                    v-model="form.from_date" 
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    required
                                />
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">إلى تاريخ</label>
                                <input 
                                    type="date" 
                                    v-model="form.to_date" 
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    required
                                />
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">ملاحظات (اختياري)</label>
                            <textarea 
                                v-model="form.notes" 
                                rows="3"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="أضف أي ملاحظات إضافية..."
                            ></textarea>
                        </div>
                        
                        <div class="flex gap-3 pt-4">
                            <button 
                                type="submit" 
                                class="flex-1 bg-blue-500 text-white font-semibold py-2 px-4 rounded-md hover:bg-blue-600 transition-colors"
                                :disabled="loading"
                            >
                                <span v-if="loading" class="flex items-center justify-center">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    جاري الإرسال...
                                </span>
                                <span v-else>{{ editingLeave ? 'تحديث' : 'إرسال' }}</span>
                            </button>
                            <button 
                                type="button" 
                                @click="closeModal"
                                class="flex-1 bg-gray-300 text-gray-700 font-semibold py-2 px-4 rounded-md hover:bg-gray-400 transition-colors"
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
            form: {
                leave_type: '',
                from_date: '',
                to_date: '',
                notes: '',
                leave_file: null
            }
        }
    },
    mounted() {
        this.getLeaves();
    },
    methods: {
        getLeaves() {
            axios.get(route('back.trainees.leaves.index', { trainee_id: this.trainee_id }))
            .then(response => {
                this.leaves = response.data;
            })
            .catch(error => {
                console.error('Error loading leaves:', error);
            });
        },
        
        openCreateModal() {
            this.editingLeave = null;
            this.resetForm();
            this.showModal = true;
        },
        
        editLeave(leave) {
            this.editingLeave = leave;
            this.form = {
                leave_type: leave.leave_type,
                from_date: leave.from_date_formatted,
                to_date: leave.to_date_formatted,
                notes: leave.notes || '',
                leave_file: null
            };
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
                leave_file: null
            };
        },
        
        handleFileChange(event) {
            this.form.leave_file = event.target.files[0];
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
            
            const formData = new FormData();
            formData.append('leave_type', this.form.leave_type);
            formData.append('from_date', this.form.from_date);
            formData.append('to_date', this.form.to_date);
            formData.append('notes', this.form.notes);
            if (this.form.leave_file) {
                formData.append('leave_file', this.form.leave_file);
            }
            
            axios.post(route('back.trainees.leaves.store', { trainee_id: this.trainee_id }), formData, {
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
                console.error('Error creating leave:', error);
                if (error.response?.data?.errors) {
                    // Handle validation errors
                    Object.keys(error.response.data.errors).forEach(key => {
                        alert(error.response.data.errors[key][0]);
                    });
                }
            })
            .finally(() => {
                this.loading = false;
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
                     leave: leaveId 
                 }))
                 .then(response => {
                     this.getLeaves();
                 })
                 .catch(error => {
                     console.error('Error deleting leave:', error);
                 });
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
</style>

