<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'reports', link: route('back.reports.index')},
                    {title: 'deleted-trainees-report'},
                ]"
            ></breadcrumb-container>

            <div class="w-full overflow-hidden rounded-lg shadow-xs mb-8">
                <div class="w-full overflow-x-auto bg-white p-4">
                    <form @submit.prevent="generateReport" class="flex flex-wrap gap-4 items-end">
                        <div class="w-full md:w-1/4">
                            <label class="block text-sm">
                                <span class="text-gray-700">{{ $t('words.date-from') }}</span>
                                <input
                                    type="date"
                                    v-model="form.date_from"
                                    class="block w-full mt-1 text-sm rounded border-gray-300 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple form-input"
                                />
                            </label>
                        </div>

                        <div class="w-full md:w-1/4">
                            <label class="block text-sm">
                                <span class="text-gray-700">{{ $t('words.date-to') }}</span>
                                <input
                                    type="date"
                                    v-model="form.date_to"
                                    class="block w-full mt-1 text-sm rounded border-gray-300 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple form-input"
                                />
                            </label>
                        </div>

                        <div class="w-full md:w-1/4">
                            <button
                                type="submit"
                                class="px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple"
                            >
                                {{ $t('words.export') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import BreadcrumbContainer from '@/Components/BreadcrumbContainer'

export default {
    components: {
        AppLayout,
        BreadcrumbContainer,
    },
    data() {
        return {
            form: this.$inertia.form({
                date_from: '',
                date_to: '',
            }),
        }
    },
    methods: {
        generateReport() {
            this.form.post(route('back.reports.deleted-trainees.generate'))
        },
    },
}
</script> 