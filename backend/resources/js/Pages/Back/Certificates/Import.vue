<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'certificates', link: route('back.certificates.import')},
                ]"
            ></breadcrumb-container>
            <div class="flex justify-between">
                <h1 class="mb-8 font-bold text-3xl">{{ $t('words.certificates') }}</h1>
            </div>
            <div class="w-1/3 mx-auto">
                <div class="w-full border-2 p-5 bg-gray-100 text-center">
                    <div class="mx-auto w-full">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mx-auto">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                        </svg>
                    </div>
                    <p class="text-weight-bold mt-2">الرجاء رفع ملف الأكسل (CSV)</p>
                    <div class="importFile mt-10">
                        <input type="file"
                               ref="import_file"
                               @change="importFileChanged">
                    </div>
                    <button class="btn btn-secondary mt-5" @click="submitForm">بدء الأصدار</button>
                </div>
                <ul class="list-disc p-5 text-sm">
                    <li>تتلقى المتدربة ملف الـPDF عبر الإيميل تلقائياً.</li>
                    <li>يجب على الأكسل ان يكون بصيغة الـCSV.</li>
                    <li>عنواين الأكسل يجب ان تكون (رقم الهوية، الأسم).</li>
                </ul>
            </div>
        </div>
    </app-layout>
</template>

<script>
    import Pagination from '@/Shared/Pagination'
    import AppLayout from '@/Layouts/AppLayout'
    import IconNavigate from 'vue-ionicons/dist/ios-arrow-dropright'
    import BreadcrumbContainer from "@/Components/BreadcrumbContainer";
    import EmptySlate from "@/Components/EmptySlate";
    export default {
        metaInfo: { title: 'Certificates' },
        components: {
            EmptySlate,
            BreadcrumbContainer,
            IconNavigate,
            AppLayout,
            Pagination,
        },
        data() {
            return {
                excelFile: null,
                formData: new FormData(),
            }
        },
        methods: {
            importFileChanged(e, filename) {
                this.excelFile = e.target.files[0];
                this.formData.append('excel_file', this.excelFile);
            },
            submitForm() {
                this.$wait.start('SENDING_FILE');
                axios.post(route('back.certificates.import.upload'), this.formData)
                    .then(response => {
                        this.$inertia.get(route('back.certificates.import.show', response.data.id));
                    }).catch(error => {
                        this.$wait.end('SENDING_FILE');
                        throw error;
                    })
            }
        },
    }
</script>
