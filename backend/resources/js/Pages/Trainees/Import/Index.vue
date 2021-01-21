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
                    <div class="my-2">
                        <jet-label for="company_id" :value="$t('words.company')" />

                        <div class="relative mt-2">
                            <select class="block appearance-none w-full border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                    v-model="company_id"
                                    id="city_id">
                                <option v-for="company in companies" :key="company.id" :value="company.id">{{ company.name_ar ? company.name_ar : company.name_en }}</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                            </div>
                        </div>
                    </div>

                    <div class="importFile mt-10">
                        <input type="file"
                               ref="import_file"
                               @change="importFileChanged">
                    </div>
                    <div class="flex mt-8">
                        <button type="submit"
                                :disabled="$wait.is('SENDING_TRAINEES')"
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
    import JetLabel from '@/Jetstream/Label'

    export default {
        metaInfo: { title: 'Import' },
        props: ['companies'],
        components: {
            BreadcrumbContainer,
            IconNavigate,
            AppLayout,
            JetLabel,
        },
        data() {
            return {
                company_id: '',
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
                this.$wait.start('SENDING_TRAINEES');
                this.formData.append('company_id', this.company_id);
                axios.post(route('back.trainees.import.store'), this.formData)
                    .then(response => {
                        this.$inertia.get(route('back.trainees.index'));
                    }).catch(error => {
                        throw error;
                    }).finally(() => {
                        this.$wait.end('SENDING_TRAINEES');
                    });
            }
        },
    }
</script>
