<template>
    <div class="relative">
        <!-- Input Field -->
        <div class="relative">
            <input
                type="text"
                :placeholder="placeholder"
                v-model="searchQuery"
                @input="handleSearch"
                @focus="showDropdown = true"
                @blur="handleBlur"
                class="w-full pr-12 pl-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-right"
                :class="{ 'border-blue-500': showDropdown }"
                :disabled="disabled"
            />
            
            <!-- Loading Indicator -->
            <div v-if="loading" class="absolute right-3 top-1/2 transform -translate-y-1/2">
                <svg class="animate-spin h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            
            <!-- Clear Button -->
            <button
                v-if="selectedCompany && !disabled"
                @click="clearSelection"
                type="button"
                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            
            <!-- Dropdown Arrow (when no company selected and not loading) -->
            <div v-if="!selectedCompany && !loading && !disabled" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </div>
        </div>

        <!-- Dropdown -->
        <div
            v-if="showDropdown && (companies.length > 0 || searchQuery.length > 0)"
            class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-y-auto"
        >
            <!-- No Results -->
            <div v-if="companies.length === 0 && searchQuery.length > 0 && !loading" class="px-4 py-3 text-gray-500 text-center">
                لا توجد شركات تطابق البحث
            </div>
            
            <!-- Results -->
            <div v-else>
                <div
                    v-for="company in companies"
                    :key="company.id"
                    @click="selectCompany(company)"
                    @mouseenter="hoveredIndex = companies.indexOf(company)"
                    @mouseleave="hoveredIndex = -1"
                    class="px-4 py-3 cursor-pointer transition-colors duration-150"
                    :class="{
                        'bg-blue-50 text-blue-900': hoveredIndex === companies.indexOf(company),
                        'bg-gray-50': hoveredIndex !== companies.indexOf(company)
                    }"
                >
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="font-medium text-gray-900">{{ company.name_ar }}</div>
                            <div v-if="company.name_en" class="text-sm text-gray-500">{{ company.name_en }}</div>
                            <div v-if="company.code" class="text-xs text-gray-400">كود: {{ company.code }}</div>
                        </div>
                        <div v-if="selectedCompany && selectedCompany.id === company.id" class="ml-2">
                            <svg class="h-5 w-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Selected Company Display -->
        <div v-if="selectedCompany && !showDropdown" class="mt-2 p-3 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex items-center justify-between">
                <div>
                    <div class="font-medium text-blue-900">{{ selectedCompany.name_ar }}</div>
                    <div v-if="selectedCompany.name_en" class="text-sm text-blue-700">{{ selectedCompany.name_en }}</div>
                    <div v-if="selectedCompany.code" class="text-xs text-blue-600">كود: {{ selectedCompany.code }}</div>
                </div>
                <button
                    @click="clearSelection"
                    type="button"
                    class="text-blue-600 hover:text-blue-800"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import _ from 'lodash';

export default {
    props: {
        value: {
            type: Object,
            default: null
        },
                       placeholder: {
                   type: String,
                   default: 'اكتب للبحث عن شركة...'
               },
        disabled: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            searchQuery: '',
            companies: [],
            selectedCompany: this.value,
            showDropdown: false,
            loading: false,
            hoveredIndex: -1
        }
    },
    watch: {
        value(newVal) {
            this.selectedCompany = newVal;
            if (newVal) {
                this.searchQuery = newVal.name_ar;
            }
        }
    },
    mounted() {
        if (this.selectedCompany) {
            this.searchQuery = this.selectedCompany.name_ar;
        }
    },
    methods: {
        handleSearch: _.debounce(function() {
            if (this.searchQuery.length < 2) {
                this.companies = [];
                return;
            }
            
            this.loading = true;
            this.showDropdown = true;
            
            axios.get(route('back.companies.index'), {
                params: {
                    search: this.searchQuery
                }
            })
            .then(response => {
                this.companies = response.data.filter(company => 
                    company.name_ar.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                    (company.name_en && company.name_en.toLowerCase().includes(this.searchQuery.toLowerCase())) ||
                    (company.code && company.code.toLowerCase().includes(this.searchQuery.toLowerCase()))
                ).slice(0, 10); // Limit to 10 results
            })
            .catch(error => {
                console.error('Error searching companies:', error);
                this.companies = [];
            })
            .finally(() => {
                this.loading = false;
            });
        }, 300),
        
        selectCompany(company) {
            this.selectedCompany = company;
            this.searchQuery = company.name_ar;
            this.showDropdown = false;
            this.$emit('input', company);
            this.$emit('change', company);
        },
        
        clearSelection() {
            this.selectedCompany = null;
            this.searchQuery = '';
            this.companies = [];
            this.showDropdown = false;
            this.$emit('input', null);
            this.$emit('change', null);
        },
        
        handleBlur() {
            // Delay hiding dropdown to allow for clicks
            setTimeout(() => {
                this.showDropdown = false;
            }, 200);
        }
    }
}
</script>

<style scoped>
/* Custom scrollbar for dropdown */
.overflow-y-auto::-webkit-scrollbar {
    width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>
