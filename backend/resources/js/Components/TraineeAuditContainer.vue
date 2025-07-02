<template>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 rounded-t-lg">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    {{ $t('words.audit') }}
                </h3>
                
                <!-- Filter Section -->
                <div class="flex flex-col sm:flex-row gap-3">
                    <!-- User Filter -->
                    <div class="relative">
                        <select v-model="selectedUser" 
                                @change="filterAudits"
                                class="appearance-none bg-white border border-gray-300 rounded-md px-4 py-2 pr-8 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 min-w-[200px]">
                            <option value="">{{ $t('words.all-users') || 'All Users' }}</option>
                            <option v-for="user in uniqueUsers" :key="user.id || 'system'" :value="user.id || 'system'">
                                {{ user.name }}
                            </option>
                        </select>
                        <svg class="absolute right-2 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                    
                    <!-- Clear Filter Button -->
                    <button v-if="selectedUser || searchQuery" 
                            @click="clearFilters"
                            class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        {{ $t('words.clear') || 'Clear' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Loading State -->
        <div v-if="!loaded" class="p-8 text-center">
            <div class="inline-flex items-center justify-center w-12 h-12 mb-4 bg-blue-100 rounded-full">
                <svg class="w-6 h-6 text-blue-600 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            <p class="text-gray-600">{{ $t('words.loading') || 'Loading audit records...' }}</p>
        </div>

        <!-- Empty State -->
        <div v-else-if="!filteredAudits.length" class="p-8 text-center">
            <div class="inline-flex items-center justify-center w-12 h-12 mb-4 bg-gray-100 rounded-full">
                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <p class="text-gray-500 font-medium mb-1">
                {{ (selectedUser || searchQuery) ? ($t('words.no-matching-records') || 'No matching records found') : ($t('words.no-audit-records') || 'No audit records found') }}
            </p>
            <p class="text-sm text-gray-400">
                {{ (selectedUser || searchQuery) ? ($t('words.try-different-filter') || 'Try adjusting your filters to see more results.') : ($t('words.no-audit-records-description') || 'There are no changes recorded for this trainee yet.') }}
            </p>
        </div>

        <!-- Audit Records -->
        <div v-else class="divide-y divide-gray-200">
            <div v-for="(audit, key) in filteredAudits" :key="audit.id" 
                 class="p-6 hover:bg-gray-50 transition-colors duration-150">
                
                <!-- Audit Header -->
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">
                                {{ audit.user ? audit.user.name : 'System' }}
                            </p>
                            <p class="text-xs text-gray-500" dir="ltr">
                                {{ audit.created_at_human }}
                            </p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                          :class="{
                              'bg-green-100 text-green-800': audit.event === 'created',
                              'bg-yellow-100 text-yellow-800': audit.event === 'updated',
                              'bg-red-100 text-red-800': audit.event === 'deleted',
                              'bg-gray-100 text-gray-800': !['created', 'updated', 'deleted'].includes(audit.event)
                          }">
                        {{ audit.event }}
                    </span>
                </div>

                <!-- Changes Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Old Values -->
                    <div v-if="audit.old_values && Object.keys(audit.old_values).length">
                        <h4 class="text-sm font-medium text-gray-700 mb-3 flex items-center">
                            <svg class="w-4 h-4 mr-1 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                            </svg>
                            {{ $t('words.old-values') }}
                        </h4>
                        <div class="bg-red-50 rounded-lg border border-red-200 overflow-hidden">
                            <div class="divide-y divide-red-200">
                                <div v-for="(old_value, attribute) in audit.old_values" :key="`old-${attribute}`"
                                     class="px-4 py-3 flex justify-between items-start">
                                    <span class="text-sm font-medium text-gray-700 min-w-0 flex-1">
                                        {{ attribute === 'company_id' ? `${attribute} - الشركة` : attribute }}
                                    </span>
                                    <div class="text-sm text-gray-900 ml-4 min-w-0 flex-1 text-right">
                                        <template v-if="attribute === 'company_id' && old_value">
                                            <inertia-link class="text-blue-600 hover:text-blue-800 underline font-medium"
                                                          :href="route('back.companies.show', {company: old_value})">
                                                {{ old_value.substring(0, 8) }}
                                            </inertia-link>
                                        </template>
                                        <template v-else>
                                            <span class="break-words">{{ old_value || '-' }}</span>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- New Values -->
                    <div v-if="audit.new_values && Object.keys(audit.new_values).length">
                        <h4 class="text-sm font-medium text-gray-700 mb-3 flex items-center">
                            <svg class="w-4 h-4 mr-1 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            {{ $t('words.new-values') }}
                        </h4>
                        <div class="bg-green-50 rounded-lg border border-green-200 overflow-hidden">
                            <div class="divide-y divide-green-200">
                                <div v-for="(new_value, attribute) in audit.new_values" :key="`new-${attribute}`"
                                     class="px-4 py-3 flex justify-between items-start">
                                    <span class="text-sm font-medium text-gray-700 min-w-0 flex-1">
                                        {{ attribute === 'company_id' ? `${attribute} - الشركة` : attribute }}
                                    </span>
                                    <div class="text-sm text-gray-900 ml-4 min-w-0 flex-1 text-right">
                                        <template v-if="attribute === 'company_id' && new_value">
                                            <inertia-link class="text-blue-600 hover:text-blue-800 underline font-medium"
                                                          :href="route('back.companies.show', {company: new_value})">
                                                {{ new_value.substring(0, 8) }}
                                            </inertia-link>
                                        </template>
                                        <template v-else>
                                            <span class="break-words">{{ new_value || '-' }}</span>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'TraineeAuditContainer',
    props: [
        'trainee_id',
    ],
    data() {
        return {
            audits: [],
            loaded: false,
            selectedUser: '',
            searchQuery: '',
        }
    },
    computed: {
        uniqueUsers() {
            const users = [];
            const userMap = new Map();
            
            this.audits.forEach(audit => {
                const user = audit.user || { id: null, name: 'System' };
                const userId = user.id || 'system';
                
                if (!userMap.has(userId)) {
                    userMap.set(userId, user);
                    users.push(user);
                }
            });
            
            return users.sort((a, b) => a.name.localeCompare(b.name));
        },
        filteredAudits() {
            let filtered = [...this.audits];
            
            // Filter by selected user
            if (this.selectedUser) {
                filtered = filtered.filter(audit => {
                    const userId = audit.user ? audit.user.id : 'system';
                    return userId === this.selectedUser;
                });
            }
            
            return filtered;
        }
    },
    mounted() {
        let vm = this;
        setTimeout(function() {
            vm.getAuditRecords();
        }, 200)
    },
    methods: {
        getAuditRecords() {
            axios.get(route('back.trainees.audit', this.trainee_id))
            .then(response => {
                this.audits = response.data;
                this.loaded = true;
            }).catch(error => {
                console.error('Error loading audit records:', error);
                this.loaded = true;
            })
        },
        filterAudits() {
            // This method is called when the filter changes
            // The actual filtering is handled by the computed property
        },
        clearFilters() {
            this.selectedUser = '';
            this.searchQuery = '';
        }
    }
}
</script>

<style scoped>
/* Custom animations */
@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

.animate-spin {
    animation: spin 1s linear infinite;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .grid-cols-1.lg\:grid-cols-2 {
        grid-template-columns: 1fr;
    }
}
</style>

