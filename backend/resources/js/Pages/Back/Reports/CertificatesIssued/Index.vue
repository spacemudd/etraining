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

            <div class="w-full overflow-x-auto bg-white p-4">
                <div class="mb-4">
                    <h2 class="text-xl font-semibold text-gray-900">{{ $t('words.certificates-issued') }}</h2>
                    <p class="text-sm text-gray-600 mt-1">{{ $t('words.download-excel-report-for-all-trainees-with-certificates') }}</p>
                </div>

                <div class="flex justify-end">
                    <a 
                        :href="route('back.reports.certificates-issued.export')"
                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        {{ $t('words.download-excel') }}
                    </a>
                </div>

                <div class="mt-6 bg-blue-50 border border-blue-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">{{ $t('words.about-this-report') }}</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <p>{{ $t('words.certificates-issued-report-description') }}</p>
                                <ul class="list-disc list-inside mt-2">
                                    <li>{{ $t('words.trainee-name-and-details') }}</li>
                                    <li>{{ $t('words.certificate-title-and-issue-date') }}</li>
                                    <li>{{ $t('words.company-information') }}</li>
                                    <li>{{ $t('words.contact-information') }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import BreadcrumbContainer from "@/Components/BreadcrumbContainer";

export default {
    metaInfo() {
        return {
            title: this.$t('words.certificates-issued')
        }
    },
    components: {
        AppLayout,
        BreadcrumbContainer,
    },
    data() {
        return {
            //
        }
    },
}
</script> 