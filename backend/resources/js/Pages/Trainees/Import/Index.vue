<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'trainees', link: route('back.trainees.index')},
                    {title_raw: $t('words.import')},
                ]"
            ></breadcrumb-container>

            <div class="bg-white rounded shadow overflow-x-auto my-5 p-5">
                <form @submit.prevent="submitForm" enctype="multipart/form-data">
                    <div class="importFile">
                        <input type="file"
                               ref="import_file"
                               @change="importFileChanged">
                    </div>
                    <div class="flex mt-8">
                        <button type="submit"
                                class="inline-flex px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150 mx-4 tracking-normal">
                            {{ $t('words.submit') }}
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

    export default {
        metaInfo: { title: 'Import' },
        components: {
            BreadcrumbContainer,
            IconNavigate,
            AppLayout,
        },
        data() {
            return {
                excelFile: '',
                formData: new FormData(),
            }
        },
        methods: {
            importFileChanged(e, filename) {
                this.excelFile = e.target.files[0];
                this.formData.append('excel_file', this.excelFile);
            },
            submitForm() {
                axios.post(route('back.trainees.import.store'), this.formData)
                    .then(response => {
                        this.$inertia.get(route('back.trainees.index'));
                    });
            }
        },
    }
</script>
