<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'uk-certificates', link: route('back.uk-certificates.index')},
                    {title: 'processing', link: '#'},
                ]"
            ></breadcrumb-container>
            
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="font-bold text-3xl">{{ $t('words.processing-uk-certificates') }}</h1>
                    <p class="text-gray-600 mt-2">{{ $t('words.course') }}: {{ importData.course?.name_ar || 'Unknown Course' }}</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-right">
                        <div class="text-sm text-gray-500">{{ $t('words.import-id') }}: {{ importData.id }}</div>
                        <div class="text-sm text-gray-500">{{ $t('words.started-at') }}: {{ formatDateTime(importData.started_at) }}</div>
                    </div>
                    <button 
                        @click="confirmDelete" 
                        class="btn-red px-4 py-2 text-sm"
                        :disabled="isDeleting"
                    >
                        <span v-if="isDeleting" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ $t('words.deleting') }}...
                        </span>
                        <span v-else class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 000-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $t('words.delete') }}
                        </span>
                    </button>
                </div>
            </div>

            <!-- Processing Status Card -->
            <div class="bg-white rounded shadow p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold">{{ getStatusTitle() }}</h2>
                    <div class="flex items-center">
                        <div v-if="isProcessing" class="flex items-center text-blue-600">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ $t('words.processing') }}...
                        </div>
                        <div v-else-if="status.status === 'completed'" class="flex items-center text-green-600">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $t('words.completed') }}
                        </div>
                        <div v-else-if="status.status === 'failed'" class="flex items-center text-red-600">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $t('words.failed') }}
                        </div>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div v-if="isProcessing || status.progress_percentage > 0" class="mb-4">
                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                        <span>{{ $t('words.progress') }}</span>
                        <span>{{ (status.progress_percentage || 0).toFixed(1) }}%</span>
                    </div>
                    <div class="bg-gray-200 rounded-full h-3">
                        <div class="bg-blue-500 h-3 rounded-full transition-all duration-300" :style="{ width: (status.progress_percentage || 0) + '%' }"></div>
                    </div>
                    <div v-if="status.current_file" class="text-sm text-gray-500 mt-2">
                        {{ $t('words.current-file') }}: {{ status.current_file }}
                    </div>
                </div>

                <!-- Summary Stats -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-gray-50 p-4 rounded">
                        <div class="text-2xl font-bold text-gray-900">{{ status.total_files || 0 }}</div>
                        <div class="text-sm text-gray-600">{{ $t('words.total-files') }}</div>
                    </div>
                    <div class="bg-green-50 p-4 rounded">
                        <div class="text-2xl font-bold text-green-600">{{ status.matched_count || 0 }}</div>
                        <div class="text-sm text-gray-600">{{ $t('words.matched') }}</div>
                    </div>
                    <div class="bg-orange-50 p-4 rounded">
                        <div class="text-2xl font-bold text-orange-600">{{ status.unmatched_count || 0 }}</div>
                        <div class="text-sm text-gray-600">{{ $t('words.unmatched') }}</div>
                    </div>
                    <div class="bg-red-50 p-4 rounded">
                        <div class="text-2xl font-bold text-red-600">{{ status.failed_count || 0 }}</div>
                        <div class="text-sm text-gray-600">{{ $t('words.failed') }}</div>
                    </div>
                </div>

                <!-- Google Drive URL Info -->
                <div v-if="status.drive_url" class="mb-4 p-3 bg-blue-50 rounded">
                    <div class="text-sm font-medium text-blue-800">{{ $t('words.google-drive-folder') }}:</div>
                    <a :href="status.drive_url" target="_blank" class="text-sm text-blue-600 hover:text-blue-800 break-all">
                        {{ status.drive_url }}
                    </a>
                </div>
            </div>

            <!-- Matched Files -->
            <div v-if="status.matched && status.matched.length > 0" class="bg-white rounded shadow p-6 mb-6">
                <h3 class="text-lg font-semibold text-green-700 mb-4">
                    {{ $t('words.matched-trainees') }} ({{ status.matched.length }})
                </h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $t('words.filename') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $t('words.trainee-name') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $t('words.identity-number') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $t('words.email') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $t('words.status') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="(file, index) in status.matched" :key="`matched-${index}-${file.filename}`" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ file.filename }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ file.name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ file.identity_number }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ file.email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        {{ $t('words.matched') }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Unmatched Files -->
            <div v-if="status.unmatched && status.unmatched.length > 0" class="bg-white rounded shadow p-6 mb-6">
                <h3 class="text-lg font-semibold text-orange-700 mb-4">
                    {{ $t('words.unmatched-trainees') }} ({{ status.unmatched.length }})
                </h3>
                <div class="space-y-4">
                    <div v-for="(trainee, index) in status.unmatched" :key="`unmatched-${index}-${trainee.filename}`" class="border border-orange-200 rounded-lg p-4">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <div class="font-medium text-gray-900">{{ trainee.trainee_name }}</div>
                                <div class="text-sm text-gray-500">{{ trainee.identity_number }} - {{ trainee.filename }}</div>
                            </div>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">
                                {{ $t('words.unmatched') }}
                            </span>
                        </div>
                        
                        <!-- Search for trainee -->
                        <div class="mb-3">
                            <input 
                                v-model="trainee.searchQuery" 
                                @input="searchTrainees(index)"
                                :placeholder="$t('words.search-trainee')"
                                class="w-full form-input text-sm"
                            />
                        </div>
                        
                        <!-- Search Results -->
                        <div v-if="trainee.searchResults && trainee.searchResults.length > 0" class="mb-3">
                            <div class="text-sm text-gray-600 mb-2">{{ $t('words.select-trainee') }}:</div>
                            <div class="max-h-32 overflow-y-auto border rounded">
                                <div 
                                    v-for="(result, resultIndex) in trainee.searchResults" 
                                    :key="`search-${index}-${resultIndex}-${result.id}`"
                                    @click="selectTraineeForUnmatched(index, result)"
                                    class="p-2 hover:bg-gray-100 cursor-pointer border-b last:border-b-0"
                                >
                                    {{ result.name }} ({{ result.identity_number }}) - {{ result.email }}
                                </div>
                            </div>
                        </div>
                        
                        <!-- Selected Trainee -->
                        <div v-if="trainee.selectedTrainee" class="p-2 bg-green-100 rounded">
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

            <!-- Failed Files -->
            <div v-if="status.failed && status.failed.length > 0" class="bg-white rounded shadow p-6 mb-6">
                <h3 class="text-lg font-semibold text-red-700 mb-4">
                    {{ $t('words.failed-files') }} ({{ status.failed.length }})
                </h3>
                <div class="space-y-3">
                    <div v-for="(file, index) in status.failed" :key="`failed-${index}-${file.filename}`" class="border border-red-200 rounded-lg p-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <div class="font-medium text-gray-900">{{ file.filename }}</div>
                                <div class="text-sm text-gray-500">{{ file.identity_number }} - {{ file.trainee_name }}</div>
                                <div class="text-sm text-red-600 mt-1">{{ file.error_message }}</div>
                            </div>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                {{ $t('words.failed') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div v-if="status.status === 'completed'" class="bg-white rounded shadow p-6">
                <div class="flex space-x-4">
                    <button 
                        @click="submitCertificatesImport" 
                        :disabled="!canSubmit"
                        class="btn-gray"
                    >
                        {{ $t('words.submit-and-send-certificates') }}
                    </button>
                    <button 
                        @click="goBack" 
                        class="btn-gray bg-gray-500 hover:bg-gray-600"
                    >
                        {{ $t('words.back-to-imports') }}
                    </button>
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
    metaInfo: { title: 'Processing UK Certificates' },
    components: {
        BreadcrumbContainer,
        AppLayout,
    },
    props: {
        import: Object,
    },
    computed: {
        importData() {
            return this.import;
        },
        isProcessing() {
            return this.status.status === 'processing' || this.status.status === 'sending';
        },
        canSubmit() {
            return this.status.status === 'completed' && 
                   this.status.unmatched.every(t => t.selectedTrainee);
        }
    },
    data() {
        return {
            status: {
                import_id: this.import?.id || null,
                status: this.import?.status || 'processing',
                progress_percentage: parseFloat(this.import?.progress_percentage || 0),
                current_file: this.import?.current_file || '',
                total_files: this.import?.total_files || 0,
                matched_count: this.import?.matched_count || 0,
                unmatched_count: this.import?.unmatched_count || 0,
                failed_count: this.import?.failed_count || 0,
                started_at: this.import?.started_at || null,
                completed_at: this.import?.completed_at || null,
                drive_url: this.import?.drive_url || '',
                course_name: this.import?.course?.name_ar || 'Unknown Course',
                matched: [],
                unmatched: [],
                failed: [],
            },
            statusCheckInterval: null,
            searchTimeouts: {},
            isDeleting: false,
        }
    },
    mounted() {
        this.loadStatus();
        if (this.isProcessing) {
            this.startStatusChecking();
        }
    },
    beforeDestroy() {
        if (this.statusCheckInterval) {
            clearInterval(this.statusCheckInterval);
        }
        // Clear search timeouts
        Object.values(this.searchTimeouts).forEach(timeout => clearTimeout(timeout));
    },
    methods: {
        async loadStatus() {
            try {
                const response = await axios.get(`/back/uk-certificates/${this.import.id}/status`);
                const data = response.data;
                // Ensure progress_percentage is a number
                if (data.progress_percentage !== undefined) {
                    data.progress_percentage = parseFloat(data.progress_percentage || 0);
                }
                this.status = { ...this.status, ...data };
            } catch (err) {
                console.error('Failed to load status:', err);
            }
        },

        startStatusChecking() {
            this.statusCheckInterval = setInterval(async () => {
                await this.loadStatus();
                
                if (this.status.status === 'completed' || this.status.status === 'failed') {
                    clearInterval(this.statusCheckInterval);
                    this.statusCheckInterval = null;
                }
            }, 2000); // Check every 2 seconds
        },

        getStatusTitle() {
            switch (this.status.status) {
                case 'processing':
                    return this.$t('words.processing-files');
                case 'sending':
                    return this.$t('words.sending-certificates');
                case 'completed':
                    return this.$t('words.processing-completed');
                case 'failed':
                    return this.$t('words.processing-failed');
                default:
                    return this.$t('words.processing-status');
            }
        },

        formatDateTime(dateTime) {
            if (!dateTime) return '-';
            return new Date(dateTime).toLocaleString();
        },

        async searchTrainees(index) {
            const trainee = this.status.unmatched[index];
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
                    trainee.searchResults = response.data.slice(0, 10);
                } catch (err) {
                    trainee.searchResults = [];
                }
            }, 300);
        },
        
        selectTraineeForUnmatched(index, trainee) {
            this.status.unmatched[index].selectedTrainee = trainee;
            this.status.unmatched[index].searchResults = [];
            this.status.unmatched[index].searchQuery = '';
        },
        
        removeLinkedTrainee(index) {
            this.status.unmatched[index].selectedTrainee = null;
        },

        async submitCertificatesImport() {
            const mappings = this.status.unmatched.map(t => ({
                filename: t.filename,
                trainee_id: t.selectedTrainee.id,
            }));
            
            try {
                const response = await axios.post('/back/uk-certificates/finalize', {
                    import_id: this.status.import_id,
                    mappings,
                });
                
                alert(this.$t('words.certificates-queued-for-sending'));
                this.goBack();
                
            } catch (err) {
                alert(this.$t('words.submission-failed'));
            }
        },

        goBack() {
            this.$inertia.visit(route('back.uk-certificates.index'));
        },

        confirmDelete() {
            if (confirm(this.$t('words.confirm-delete-uk-certificate'))) {
                this.deleteCertificate();
            }
        },

        async deleteCertificate() {
            this.isDeleting = true;
            
            try {
                const response = await axios.delete(`/back/uk-certificates/${this.import.id}`);
                
                if (response.data.success) {
                    alert(this.$t('words.uk-certificate-deleted-successfully'));
                    this.goBack();
                } else {
                    alert(this.$t('words.failed-to-delete-uk-certificate'));
                }
            } catch (err) {
                console.error('Delete failed:', err);
                alert(this.$t('words.failed-to-delete-uk-certificate'));
            } finally {
                this.isDeleting = false;
            }
        },
    }
}
</script>
