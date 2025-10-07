<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: '/dashboard'},
                    {title: 'trainees', link: '/back/trainees'},
                    {title_raw: trainee.name, link: `/back/trainees/${trainee.id}`},
                    {title_raw: $t('words.files')},
                ]"
            ></breadcrumb-container>

            <div class="bg-white rounded shadow overflow-x-auto my-5 p-5">

                <table class="w-full whitespace-no-wrap bg-white rounded-lg my-5 p-5 shadow text-sm">
                    <colgroup>
                        <col>
                        <col style="width:200px;">
                        <col style="width:200px;">
                        <col style="width:30px;">
                    </colgroup>
                <thead>
                <tr>
                	<th class="text-right">{{ $t('words.name') }}</th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
                </thead>
                	<tbody>
                			<tr v-for="file in trainee.general_files" :key="file.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
                				<td class="border-t mt-2 p-1 px-2">
                                    <a target="_blank"
                                       :href="`/back/trainees/${trainee.id}/files/${file.id}`">
                                        {{ file.file_name }}
                                    </a>
                                </td>
                                <td class="border-t mt-2 p-1 px-2" dir="ltr">{{ file.created_at_timezone }}</td>
                                <td class="border-t mt-2 p-1 px-2" dir="ltr">{{ file.human_readable_size }}</td>
                                <td class="border-t mt-2 p-1 px-2" dir="ltr">
                                    <span class="cursor-pointer" @click="deleteFile(file.id)">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                    </span>
                                </td>
                			</tr>
                	</tbody>
                </table>
            </div>
            

            <!-- File Upload Section -->
            <div class="bg-white rounded shadow overflow-x-auto my-5 p-5">
                <form @submit.prevent="submitForm" enctype="multipart/form-data">
                    <div class="importFile mt-10">
                        <div class="flex items-center justify-center w-full">
                            <label for="attached_file" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6" v-if="!attachedFile">
                                    <svg class="w-8 h-8 mb-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                    </svg>
                                    <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">اضغط للرفع</span> أو اسحب الملف هنا</p>
                                    <p class="text-xs text-gray-500">PDF, DOC, DOCX, JPG, JPEG, PNG (حد أقصى 20MB)</p>
                                </div>
                                <div class="flex flex-col items-center justify-center pt-5 pb-6" v-else>
                                    <svg class="w-8 h-8 mb-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <p class="mb-2 text-sm text-green-600 font-semibold">{{ attachedFile.name }}</p>
                                    <p class="text-xs text-gray-500">{{ formatFileSize(attachedFile.size) }}</p>
                                </div>
                                <input type="file"
                                       id="attached_file"
                                       name="attached_file"
                                       ref="attached_file"
                                       class="hidden"
                                       accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                       @change="importFileChanged">
                            </label>
                        </div>
                    </div>
                    
                    <!-- Upload Progress -->
                    <div v-if="uploadProgress > 0 && uploadProgress < 100" class="mt-4">
                        <div class="flex justify-between mb-1">
                            <span class="text-base font-medium text-blue-700">جاري الرفع...</span>
                            <span class="text-sm font-medium text-blue-700">{{ uploadProgress }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-300" :style="{ width: uploadProgress + '%' }"></div>
                        </div>
                    </div>
                    
                    <!-- Error Message -->
                    <div v-if="errorMessage" class="mt-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                        {{ errorMessage }}
                    </div>
                    
                    <!-- Success Message -->
                    <div v-if="successMessage" class="mt-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                        {{ successMessage }}
                    </div>
                    
                    <div class="flex mt-8">
                        <button type="submit"
                                :disabled="!attachedFile || $wait.is('SAVING_FILE')"
                                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150 mx-4 tracking-normal">
                            <svg v-if="$wait.is('SAVING_FILE')" class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ $wait.is('SAVING_FILE') ? 'جاري الرفع...' : $t('words.upload') }}
                        </button>
                        <button type="button"
                                v-if="attachedFile"
                                @click="clearFile"
                                class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:shadow-outline-red transition ease-in-out duration-150 mx-2 tracking-normal">
                            إلغاء
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout'
    import IconNavigate from 'vue-ionicons/dist/ios-arrow-dropright'
    import BreadcrumbContainer from "@/Components/BreadcrumbContainer";
    import JetLabel from '@/Jetstream/Label'
    import SelectTraineeGroup from "@/Components/SelectTraineeGroup";
    import { log } from 'logrocket';

    export default {
        metaInfo: { title: 'Files' },
        props: ['trainee'],
        components: {
            BreadcrumbContainer,
            IconNavigate,
            AppLayout,
            JetLabel,
            SelectTraineeGroup,
        },
        data() {
            return {
                attachedFile: null,
                formData: new FormData(),
                uploadProgress: 0,
                errorMessage: '',
                successMessage: '',
            }
        },
        mounted() {
            this.$wait.end('SAVING_FILE');
            
            // Check if route helper is available
            if (typeof this.route !== 'function') {
                console.error('Route helper not available in Vue component');
            }
        },
        methods: {
            importFileChanged(e) {
                const file = e.target.files[0];
                if (file) {
                    // Reset messages
                    this.errorMessage = '';
                    this.successMessage = '';
                    this.uploadProgress = 0;
                    
                    // Debug info
                    console.log('File selected:', {
                        name: file.name,
                        size: file.size,
                        type: file.type,
                        lastModified: file.lastModified
                    });
                    
                    // Validate file size (20MB = 20 * 1024 * 1024 bytes)
                    if (file.size > 20 * 1024 * 1024) {
                        this.errorMessage = 'حجم الملف يجب أن يكون أقل من 20 ميجابايت';
                        this.clearFile();
                        return;
                    }
                    
                    // Validate file type - check both MIME type and extension
                    const allowedTypes = [
                        'application/pdf', 
                        'application/x-pdf',
                        'application/acrobat',
                        'applications/vnd.pdf',
                        'text/pdf',
                        'text/x-pdf',
                        'application/msword', 
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 
                        'image/jpeg', 
                        'image/jpg', 
                        'image/png'
                    ];
                    
                    const fileName = file.name.toLowerCase();
                    const fileExtension = fileName.split('.').pop();
                    const allowedExtensions = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
                    
                    // Check both MIME type and file extension
                    const isValidType = allowedTypes.includes(file.type) || allowedExtensions.includes(fileExtension);
                    
                    if (!isValidType) {
                        this.errorMessage = 'نوع الملف غير مدعوم. يرجى اختيار ملف PDF, DOC, DOCX, JPG, JPEG, أو PNG';
                        this.clearFile();
                        return;
                    }
                    
                    this.attachedFile = file;
                    // Clear previous FormData and create new one
                    this.formData = new FormData();
                    this.formData.append('attached_file', this.attachedFile);
                }
            },
            
            clearFile() {
                this.attachedFile = null;
                this.formData = new FormData();
                this.uploadProgress = 0;
                this.errorMessage = '';
                this.successMessage = '';
                if (this.$refs.attached_file) {
                    this.$refs.attached_file.value = '';
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
                // Check if file is selected
                if (!this.attachedFile) {
                    this.errorMessage = 'يرجى اختيار ملف للرفع';
                    return;
                }

                // Reset messages
                this.errorMessage = '';
                this.successMessage = '';
                this.uploadProgress = 0;
                
                this.$wait.start('SAVING_FILE');
                
                // Use direct URLs as fallback
                const storeUrl = `/back/trainees/${this.trainee.id}/files`;

                axios.post(storeUrl, this.formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    timeout: 300000, // 5 minutes timeout
                    onUploadProgress: (progressEvent) => {
                        this.uploadProgress = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                    }
                })
                    .then(response => {
                        this.$wait.end('SAVING_FILE');
                        this.uploadProgress = 100;
                        
                        console.log('Upload success response:', response.data);
                        
                        if (response.data && response.data.success) {
                            this.successMessage = response.data.message || 'تم رفع الملف بنجاح!';
                        } else {
                            this.successMessage = 'تم رفع الملف بنجاح!';
                        }
                        
                        // Clear the file input after a short delay
                        setTimeout(() => {
                            this.clearFile();
                            // Reload the page to show updated files
                            window.location.reload();
                        }, 2000);
                    }).catch(error => {
                        this.$wait.end('SAVING_FILE');
                        this.uploadProgress = 0;
                        
                        // Debug error info
                        console.error('Upload error details:', {
                            error: error,
                            response: error.response,
                            status: error.response?.status,
                            data: error.response?.data,
                            message: error.message,
                            code: error.code
                        });
                        
                        if (error.code === 'ECONNABORTED') {
                            this.errorMessage = 'انتهت مهلة الرفع. يرجى المحاولة مرة أخرى أو اختيار ملف أصغر.';
                        } else if (error.response && error.response.status === 422) {
                            // Validation errors
                            if (error.response.data.errors) {
                                const errors = error.response.data.errors;
                                let errorMessage = 'خطأ في البيانات: ';
                                for (let field in errors) {
                                    errorMessage += errors[field][0] + ' ';
                                }
                                this.errorMessage = errorMessage;
                            } else if (error.response.data.error) {
                                this.errorMessage = error.response.data.error;
                            } else {
                                this.errorMessage = 'خطأ في التحقق من الملف';
                            }
                        } else if (error.response && error.response.status === 413) {
                            if (error.response.data && error.response.data.error) {
                                this.errorMessage = error.response.data.error;
                            } else {
                                this.errorMessage = 'حجم الملف كبير جداً. يرجى اختيار ملف أصغر من 10MB.';
                            }
                        } else if (error.response && error.response.status === 500) {
                            this.errorMessage = 'خطأ في الخادم. يرجى المحاولة مرة أخرى لاحقاً.';
                        } else if (error.response && error.response.data && error.response.data.error) {
                            this.errorMessage = error.response.data.error;
                        } else if (error.response && error.response.data && error.response.data.message) {
                            this.errorMessage = 'خطأ: ' + error.response.data.message;
                        } else {
                            this.errorMessage = 'حدث خطأ في الشبكة. يرجى التحقق من الاتصال والمحاولة مرة أخرى.';
                        }
                        console.error('File upload error:', error);
                    });
            },
            deleteFile(file_id) {
                if (confirm('هل أنت متأكد من حذف هذا الملف؟')) {
                    const deleteUrl = `/back/trainees/${this.trainee.id}/files/${file_id}`;
                    
                    axios.delete(deleteUrl, {
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    }).then(() => {
                        window.location.reload();
                    }).catch(error => {
                        alert('حدث خطأ أثناء حذف الملف');
                        console.error('Delete error:', error);
                    });
                }
            },
        },
    }
</script>
