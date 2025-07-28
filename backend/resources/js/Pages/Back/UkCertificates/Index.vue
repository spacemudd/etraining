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
                            {{ course.name_ar }}
                        </option>
                    </select>
                </div>

                <!-- File Upload -->
                <div class="mb-6">
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

                <!-- Upload Button -->
                <div class="mb-6">
                    <button 
                        @click="handleCertificateZipUpload" 
                        :disabled="!selectedCourseId || !certificateZipFile"
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
            certificateZipFile: null,
            uploadError: '',
            matchedTrainees: [],
            unmatchedTrainees: [],
            searchTimeouts: {},
            lastImportId: null,
        }
    },
    computed: {
        canSubmit() {
            return this.lastImportId && this.unmatchedTrainees.every(t => t.selectedTrainee);
        }
    },
    methods: {
        onZipFileChange(event) {
            this.certificateZipFile = event.target.files[0];
            this.uploadError = '';
        },
        
        async handleCertificateZipUpload() {
            if (!this.certificateZipFile || !this.selectedCourseId) {
                this.uploadError = this.$t('words.please-select-course-and-file');
                return;
            }
            
            this.uploadError = '';
            const formData = new FormData();
            formData.append('zip', this.certificateZipFile);
            formData.append('course_id', this.selectedCourseId);
            
            try {
                const response = await axios.post('/back/uk-certificates/upload-zip', formData, {
                    headers: { 'Content-Type': 'multipart/form-data' }
                });
                
                this.matchedTrainees = response.data.matched || [];
                this.unmatchedTrainees = (response.data.unmatched || []).map(t => ({ 
                    ...t, 
                    searchQuery: '', 
                    searchResults: [], 
                    selectedTrainee: null 
                }));
                this.lastImportId = response.data.import_id;
            } catch (err) {
                this.uploadError = this.$t('words.upload-failed');
            }
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
                        params: { q: query }
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
                this.certificateZipFile = null;
                this.matchedTrainees = [];
                this.unmatchedTrainees = [];
                this.lastImportId = null;
            } catch (err) {
                alert(this.$t('words.submission-failed'));
            }
        },
    }
}
</script> 