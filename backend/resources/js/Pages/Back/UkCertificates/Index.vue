<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'uk-certificates', link: route('back.uk-certificates.index')},
                ]"
            ></breadcrumb-container>
            <div class="flex justify-between">
                <h1 class="mb-8 font-bold text-3xl">{{ $t('words.uk-certificates') }}</h1>
            </div>

            <div class="bg-white rounded shadow p-6">
                <h2 class="text-xl font-semibold mb-4">{{ $t('words.send-certificates-by-id') }}</h2>
                
                <!-- Course Selection -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        {{ $t('words.course') }}
                    </label>
                    <select v-model="selectedCourseId" class="w-full form-select">
                        <option value="">{{ $t('words.select-course') }}</option>
                        <option v-for="course in courses.data" :key="course.id" :value="course.id">
                            {{ course.name_ar }} - {{ course.instructor ? course.instructor.name : 'No Instructor' }} ({{ new Date(course.created_at).toLocaleDateString() }})
                        </option>
                    </select>
                </div>

                <!-- Upload Method Selection -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        {{ $t('words.upload-method') }}
                    </label>
                    <div class="flex space-x-4">
                        <label class="flex items-center">
                            <input 
                                type="radio" 
                                v-model="uploadMethod" 
                                value="zip" 
                                class="form-radio"
                            />
                            <span class="ml-2">{{ $t('words.zip-file') }}</span>
                        </label>
                        <label class="flex items-center">
                            <input 
                                type="radio" 
                                v-model="uploadMethod" 
                                value="google-drive" 
                                class="form-radio"
                            />
                            <span class="ml-2">{{ $t('words.google-drive-url') }}</span>
                        </label>
                    </div>
                </div>

                <!-- ZIP File Upload -->
                <div v-if="uploadMethod === 'zip'" class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        {{ $t('words.zip-file') }}
                    </label>
                    <input 
                        type="file" 
                        @change="onZipFileChange" 
                        accept=".zip"
                        class="w-full form-input"
                    />
                    <p class="text-sm text-gray-500 mt-1">
                        {{ $t('words.uk-certificates-help') }}
                    </p>
                </div>

                <!-- Google Drive URL -->
                <div v-if="uploadMethod === 'google-drive'" class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        {{ $t('words.google-drive-url') }}
                    </label>
                    <input 
                        type="url" 
                        v-model="googleDriveUrl"
                        :placeholder="$t('words.google-drive-url-placeholder')"
                        class="w-full form-input"
                    />
                    <p class="text-sm text-gray-500 mt-1">
                        {{ $t('words.google-drive-help') }}
                    </p>
                </div>

                <!-- Processing Status -->
                <div v-if="isProcessing" class="mb-6 p-4 bg-blue-100 border border-blue-400 text-blue-700 rounded">
                    <div class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>{{ processingStatus }}</span>
                    </div>
                    <div v-if="progressPercentage > 0" class="mt-2">
                        <div class="bg-blue-200 rounded-full h-2">
                            <div class="bg-blue-500 h-2 rounded-full transition-all duration-300" :style="{ width: progressPercentage + '%' }"></div>
                        </div>
                        <p class="text-sm mt-1">{{ progressPercentage.toFixed(1) }}% - {{ currentFile }}</p>
                    </div>
                </div>

                <!-- Upload Button -->
                <div class="mb-6">
                    <button 
                        @click="handleFileProcessing" 
                        :disabled="!canProcess || isProcessing"
                        class="btn-gray"
                    >
                        {{ $t('words.process-files') }}
                    </button>
                </div>

                <!-- Error Message -->
                <div v-if="uploadError" class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    {{ uploadError }}
                </div>

                <!-- Results Section -->
                <div v-if="matchedTrainees.length > 0 || unmatchedTrainees.length > 0" class="mt-8">
                    <h3 class="text-lg font-semibold mb-4">{{ $t('words.processing-results') }}</h3>
                    
                    <!-- Matched Trainees -->
                    <div v-if="matchedTrainees.length > 0" class="mb-6">
                        <h4 class="font-medium text-green-700 mb-2">
                            {{ $t('words.matched-trainees') }} ({{ matchedTrainees.length }})
                        </h4>
                        <div class="bg-green-50 p-4 rounded">
                            <div v-for="trainee in matchedTrainees" :key="trainee.filename" class="mb-2">
                                <span class="font-medium">{{ trainee.name }}</span> 
                                ({{ trainee.identity_number }}) - {{ trainee.email }}
                            </div>
                        </div>
                    </div>

                    <!-- Unmatched Trainees -->
                    <div v-if="unmatchedTrainees.length > 0" class="mb-6">
                        <h4 class="font-medium text-orange-700 mb-2">
                            {{ $t('words.unmatched-trainees') }} ({{ unmatchedTrainees.length }})
                        </h4>
                        <div class="bg-orange-50 p-4 rounded">
                            <div v-for="(trainee, index) in unmatchedTrainees" :key="trainee.filename" class="mb-4 p-3 bg-white rounded border">
                                <div class="mb-2">
                                    <strong>{{ trainee.trainee_name }}</strong> ({{ trainee.identity_number }})
                                    <br>
                                    <span class="text-sm text-gray-600">{{ trainee.filename }}</span>
                                </div>
                                
                                <!-- Search for trainee -->
                                <div class="mb-2">
                                    <input 
                                        v-model="trainee.searchQuery" 
                                        @input="searchTrainees(index)"
                                        :placeholder="$t('words.search-trainee')"
                                        class="w-full form-input text-sm"
                                    />
                                </div>
                                
                                <!-- Search Results -->
                                <div v-if="trainee.searchResults.length > 0" class="mb-2">
                                    <div class="text-sm text-gray-600 mb-1">{{ $t('words.select-trainee') }}:</div>
                                    <div class="max-h-32 overflow-y-auto border rounded">
                                        <div 
                                            v-for="result in trainee.searchResults" 
                                            :key="result.id"
                                            @click="selectTraineeForUnmatched(index, result)"
                                            class="p-2 hover:bg-gray-100 cursor-pointer border-b last:border-b-0"
                                        >
                                            {{ result.name }} ({{ result.identity_number }}) - {{ result.email }}
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Selected Trainee -->
                                <div v-if="trainee.selectedTrainee" class="mb-2 p-2 bg-green-100 rounded">
                                    <span class="text-sm">
                                        {{ $t('words.selected') }}: {{ trainee.selectedTrainee.name }}
                                        <button 
                                            @click="removeLinkedTrainee(index)"
                                            class="ml-2 text-red-600 hover:text-red-800"
                                        >
                                            {{ $t('words.remove') }}
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-6">
                        <button 
                            @click="submitCertificatesImport" 
                            :disabled="!canSubmit"
                            class="btn-gray"
                        >
                            {{ $t('words.submit-and-send-certificates') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import BreadcrumbContainer from "@/Components/BreadcrumbContainer"
import axios from 'axios'

export default {
    metaInfo: { title: 'UK Certificates' },
    components: {
        BreadcrumbContainer,
        AppLayout,
    },
    props: {
        courses: Object,
    },
    data() {
        return {
            selectedCourseId: '',
            uploadMethod: 'zip',
            certificateZipFile: null,
            googleDriveUrl: '',
            uploadError: '',
            matchedTrainees: [],
            unmatchedTrainees: [],
            searchTimeouts: {},
            lastImportId: null,
            isProcessing: false,
            processingStatus: '',
            progressPercentage: 0,
            currentFile: '',
            statusCheckInterval: null,
        }
    },
    computed: {
        canSubmit() {
            return this.lastImportId && this.unmatchedTrainees.every(t => t.selectedTrainee);
        },
        canProcess() {
            if (!this.selectedCourseId) return false;
            if (this.uploadMethod === 'zip') {
                return !!this.certificateZipFile;
            }
            if (this.uploadMethod === 'google-drive') {
                return !!this.googleDriveUrl.trim();
            }
            return false;
        }
    },
    methods: {
        onZipFileChange(event) {
            this.certificateZipFile = event.target.files[0];
            this.uploadError = '';
        },
        
        async handleFileProcessing() {
            console.log('handleFileProcessing called');
            console.log('uploadMethod:', this.uploadMethod);
            console.log('selectedCourseId:', this.selectedCourseId);
            console.log('canProcess:', this.canProcess);
            
            if (this.uploadMethod === 'zip') {
                console.log('Processing ZIP file');
                await this.handleCertificateZipUpload();
            } else if (this.uploadMethod === 'google-drive') {
                console.log('Processing Google Drive URL');
                console.log('googleDriveUrl:', this.googleDriveUrl);
                await this.handleGoogleDriveUpload();
            }
        },

        async handleCertificateZipUpload() {
            if (!this.certificateZipFile || !this.selectedCourseId) {
                this.uploadError = this.$t('words.please-select-course-and-file');
                return;
            }
            
            this.uploadError = '';
            this.isProcessing = true;
            
            const formData = new FormData();
            formData.append('zip', this.certificateZipFile);
            formData.append('course_id', this.selectedCourseId);
            
            try {
                const response = await axios.post('/back/uk-certificates/upload-zip', formData, {
                    headers: { 'Content-Type': 'multipart/form-data' }
                });
                
                // Redirect to processing page
                this.$inertia.visit(route('back.uk-certificates.processing', response.data.import_id));
                
            } catch (err) {
                this.isProcessing = false;
                this.uploadError = this.$t('words.upload-failed');
            }
        },

        async handleGoogleDriveUpload() {
            console.log('handleGoogleDriveUpload called');
            console.log('googleDriveUrl:', this.googleDriveUrl);
            console.log('selectedCourseId:', this.selectedCourseId);
            
            if (!this.googleDriveUrl.trim() || !this.selectedCourseId) {
                console.log('Validation failed');
                this.uploadError = this.$t('words.please-select-course-and-url');
                return;
            }
            
            console.log('Starting Google Drive upload...');
            this.uploadError = '';
            this.isProcessing = true;
            this.processingStatus = this.$t('words.starting-processing');
            this.progressPercentage = 0;
            this.currentFile = '';
            
            try {
                console.log('Making API request to:', '/back/uk-certificates/google-drive');
                const response = await axios.post('/back/uk-certificates/google-drive', {
                    drive_url: this.googleDriveUrl.trim(),
                    course_id: this.selectedCourseId
                });
                
                console.log('API response:', response.data);
                
                // Redirect to processing page
                this.$inertia.visit(route('back.uk-certificates.processing', response.data.import_id));
                
            } catch (err) {
                console.error('Google Drive upload error:', err);
                console.error('Error response:', err.response);
                this.isProcessing = false;
                this.uploadError = err.response?.data?.error || this.$t('words.upload-failed');
            }
        },

        async startStatusChecking() {
            this.statusCheckInterval = setInterval(async () => {
                try {
                    const response = await axios.get(`/back/uk-certificates/google-drive/${this.lastImportId}/status`);
                    const data = response.data;
                    
                    this.progressPercentage = data.progress_percentage || 0;
                    this.currentFile = data.current_file || '';
                    
                    if (data.status === 'completed' || data.status === 'failed') {
                        clearInterval(this.statusCheckInterval);
                        this.isProcessing = false;
                        
                        if (data.status === 'completed') {
                            this.processingStatus = this.$t('words.processing-completed');
                            this.matchedTrainees = data.matched || [];
                            this.unmatchedTrainees = (data.unmatched || []).map(t => ({ 
                                ...t, 
                                searchQuery: '', 
                                searchResults: [], 
                                selectedTrainee: null 
                            }));
                        } else {
                            this.uploadError = this.$t('words.processing-failed');
                        }
                    }
                } catch (err) {
                    clearInterval(this.statusCheckInterval);
                    this.isProcessing = false;
                    this.uploadError = this.$t('words.status-check-failed');
                }
            }, 2000); // Check every 2 seconds
        },
        
        async searchTrainees(index) {
            const trainee = this.unmatchedTrainees[index];
            const query = trainee.searchQuery.trim();
            
            if (query.length < 2) {
                trainee.searchResults = [];
                return;
            }
            
            // Clear previous timeout
            if (this.searchTimeouts[index]) {
                clearTimeout(this.searchTimeouts[index]);
            }
            
            // Set new timeout
            this.searchTimeouts[index] = setTimeout(async () => {
                try {
                    const response = await axios.get('/back/search', {
                        params: { search: query }
                    });
                    trainee.searchResults = response.data.slice(0, 10); // Limit to 10 results
                } catch (err) {
                    trainee.searchResults = [];
                }
            }, 300);
        },
        
        selectTraineeForUnmatched(index, trainee) {
            this.unmatchedTrainees[index].selectedTrainee = trainee;
            this.unmatchedTrainees[index].searchResults = [];
            this.unmatchedTrainees[index].searchQuery = '';
        },
        
        removeLinkedTrainee(index) {
            this.unmatchedTrainees[index].selectedTrainee = null;
        },
        
        async submitCertificatesImport() {
            const mappings = this.unmatchedTrainees.map(t => ({
                filename: t.filename,
                trainee_id: t.selectedTrainee.id,
            }));
            
            try {
                const response = await axios.post('/back/uk-certificates/finalize', {
                    import_id: this.lastImportId,
                    mappings,
                });
                
                alert(this.$t('words.certificates-queued-for-sending'));
                
                // Reset form
                this.selectedCourseId = '';
                this.uploadMethod = 'zip';
                this.certificateZipFile = null;
                this.googleDriveUrl = '';
                this.matchedTrainees = [];
                this.unmatchedTrainees = [];
                this.lastImportId = null;
                this.isProcessing = false;
                this.progressPercentage = 0;
                this.currentFile = '';
                
                if (this.statusCheckInterval) {
                    clearInterval(this.statusCheckInterval);
                    this.statusCheckInterval = null;
                }
            } catch (err) {
                alert(this.$t('words.submission-failed'));
            }
        },
    },
    beforeDestroy() {
        if (this.statusCheckInterval) {
            clearInterval(this.statusCheckInterval);
        }
    }
}
</script> 