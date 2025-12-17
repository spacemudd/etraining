<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'reports', link: route('back.reports.index')},
                    {title: 'certificates-issued'},
                ]"
            ></breadcrumb-container>

            <div class="w-full overflow-x-auto bg-white p-4 rounded-lg shadow">
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-900">{{ $t('words.certificates-issued') }}</h2>
                    <p class="text-sm text-gray-600 mt-1">{{ $t('words.download-excel-report-for-all-trainees-with-certificates') }}</p>
                </div>

                <form @submit.prevent="exportReport" class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            اختر الشركة
                        </label>
                        <company-search-select
                            v-model="selectedCompany"
                            @change="onCompanyChange"
                            placeholder="ابحث عن شركة..."
                        ></company-search-select>
                        <p v-if="errors.company_id" class="mt-1 text-sm text-red-600">{{ errors.company_id }}</p>
                    </div>

                    <div class="flex justify-end">
                        <button
                            type="submit"
                            :disabled="!selectedCompany || loading"
                            class="inline-flex items-center px-6 py-3 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-50 disabled:cursor-not-allowed transition"
                        >
                            <svg v-if="loading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <svg v-else class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            {{ loading ? 'جاري التحميل...' : 'إصدار' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import BreadcrumbContainer from "@/Components/BreadcrumbContainer";
import CompanySearchSelect from "@/Components/CompanySearchSelect";

export default {
    metaInfo() {
        return {
            title: this.$t('words.certificates-issued')
        }
    },
    components: {
        AppLayout,
        BreadcrumbContainer,
        CompanySearchSelect,
    },
    props: {
        companies: {
            type: Array,
            default: () => []
        }
    },
    data() {
        return {
            selectedCompany: null,
            loading: false,
            errors: {}
        }
    },
    methods: {
        onCompanyChange(company) {
            this.selectedCompany = company;
            this.errors = {};
        },
        exportReport() {
            if (!this.selectedCompany) {
                this.errors = { company_id: 'يجب اختيار شركة' };
                return;
            }

            this.loading = true;
            this.errors = {};

            // Create a form and submit it to download the file
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = route('back.reports.certificates-issued.export');
            
            // Add CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (csrfToken) {
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken.getAttribute('content');
                form.appendChild(csrfInput);
            }

            // Add company_id
            const companyInput = document.createElement('input');
            companyInput.type = 'hidden';
            companyInput.name = 'company_id';
            companyInput.value = this.selectedCompany.id;
            form.appendChild(companyInput);

            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);

            // Reset loading after a delay (file download should start)
            setTimeout(() => {
                this.loading = false;
            }, 1000);
        }
    }
}
</script> 