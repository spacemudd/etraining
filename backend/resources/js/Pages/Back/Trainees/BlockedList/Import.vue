<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'trainees', link: route('back.trainees.index')},
                    {title: 'blocked-list'},
                    {title: 'import'},
                ]"
            ></breadcrumb-container>

            <div class="mt-4">
                <jet-form-section @submitted="submitForm">
                    <template #title>
                        {{ $t('words.add-new-block-entry') }}
                    </template>

                    <template #form>

                        <div class="col-span-4 sm:col-span-4 text-center">
                            <h1 class="text-lg">{{ $t('words.please-use-the-excel-template-below') }}</h1>
                            <div class="mt-5 text-center">
                                <a href="/blocklist.csv" class="bg-blue-600 text-white font-semibold p-2 px-10 text-center rounded my-1">
                                    {{ $t('words.download-excel-file') }} (CSV, 1KB)
                                </a>
                            </div>
                            <hr class="my-10">
                        </div>

                        <div class="col-span-4 sm:col-span-4">
                            <div class="importFile bg-gray-200 p-2">
                                <input type="file"
                                       ref="import_file"
                                       @change="importFileChanged">
                            </div>
                        </div>
                    </template>

                    <template #actions>
                        <inertia-link href="/back/trainees/block-list" class="flex items-center justify-start rtl:ml-4 ltr:mr-4 rounded-md px-4 py-2 bg-white hover:bg-gray-300 text-right">
                            {{ $t('words.cancel') }}
                        </inertia-link>

                        <jet-button :class="{ 'opacity-25': $wait.is('SENDING_FILE') }" :disabled="$wait.is('SENDING_FILE')">
                            {{ $t('words.save') }}
                        </jet-button>
                    </template>
                </jet-form-section>
            </div>
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout'
    import JetSectionBorder from '@/Jetstream/SectionBorder'
    import JetDialogModal from '@/Jetstream/DialogModal'
    import JetInput from '@/Jetstream/Input'
    import JetInputError from '@/Jetstream/InputError'
    import JetActionMessage from '@/Jetstream/ActionMessage';
    import JetButton from '@/Jetstream/Button';
    import JetFormSection from '@/Jetstream/FormSection';
    import JetLabel from '@/Jetstream/Label';
    import BreadcrumbContainer from "@/Components/BreadcrumbContainer";
    import SelectTraineeGroup from "@/Components/SelectTraineeGroup";

    export default {
        props: [
            'sessions',
        ],

        components: {
            AppLayout,
            JetSectionBorder,
            JetDialogModal,
            JetInput,
            JetInputError,
            JetActionMessage,
            JetButton,
            JetFormSection,
            JetLabel,
            BreadcrumbContainer,
            SelectTraineeGroup,
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

                axios.post(route('back.trainees.block-list.import-csv'), this.formData)
                    .then(response => {
                        this.$inertia.get(route('back.trainees.block-list.index'));
                    }).catch(error => {
                        this.$wait.end('SENDING_FILE');
                        throw error;
                    })
            }
        }
    }
</script>
