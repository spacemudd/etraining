<template>
    <app-layout>
        <div class="container px-6 mx-auto grid">
            <!--<breadcrumb/>-->

            <div class="mt-20" v-if="company">
                <jet-form-section @submitted="createContract">
                    <template #title>
                        {{ company.name_ar }}<br/>
                        {{ company.name_en }}
                    </template>

                    <template #description>
                    {{ $t('words.upload-new-contract') }}
                    </template>

                    <template #form>
                        <template v-for="fieldName in [
                            'reference_number',
                            'contract_starts_at',
                            'contract_period_in_months',
                            'trainees_count',
                            'trainee_salary',
                            'trainer_cost',
                            'company_reimbursement',
                            ]">
                            <div class="col-span-2 sm:col-span-2">
                                <jet-label :for="fieldName" :value="$t('words.'+fieldName)" />
                                <jet-input :id="fieldName"
                                           :type="fieldName==='contract_starts_at' ? 'date' : 'text'"
                                           class="mt-1 block w-full"
                                           v-model="form[fieldName]"
                                           autocomplete="off"
                                           :autofocus="fieldName==='reference_number'"
                                           required
                                           :required="fieldName==='reference_number' || fieldName === 'contract_starts_at'"
                                />
                                <jet-input-error :message="form.error(fieldName)" class="mt-2" />
                            </div>
                        </template>
                        <div class="col-span-4 sm:col-span-4">
                            <jet-label for="notes" :value="$t('words.notes')" />
                            <jet-textarea id="notes" type="textarea" class="mt-1 block w-full" v-model="form.notes" />
                            <jet-input-error :message="form.error('notes')" class="mt-2" />
                        </div>

                        <div class="col-span-4 sm:col-span-4">
                            <vue-dropzone ref="dropZoneContainer"
                                          id="dropzone"
                                          @vdropzone-file-added="fileAdded"
                                          :options="dropzoneOptions"
                            ></vue-dropzone>
                        </div>
                    </template>

                    <template #actions>
                        <jet-action-message :on="form.recentlySuccessful" class="mr-3">
                            {{ $t('words.created-successfully') }}
                        </jet-action-message>

                        <inertia-link :href="`/back/companies/${company.id}`" class="flex items-center justify-start rtl:ml-4 ltr:mr-4 rounded-md px-4 py-2 bg-white hover:bg-gray-300 text-right">
                            {{ $t('words.cancel') }}
                        </inertia-link>

                        <jet-button :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
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
    import Breadcrumb from "@/Components/Breadcrumb";
    import JetDialogModal from '@/Jetstream/DialogModal'
    import JetInput from '@/Jetstream/Input'
    import JetInputError from '@/Jetstream/InputError'
    import JetActionMessage from '@/Jetstream/ActionMessage';
    import JetButton from '@/Jetstream/Button';
    import JetFormSection from '@/Jetstream/FormSection';
    import JetLabel from '@/Jetstream/Label';
    import JetTextarea from '@/Jetstream/Textarea'
    import VueDropzone from 'vue2-dropzone'
    import 'vue2-dropzone/dist/vue2Dropzone.min.css'

    export default {
        props: ['sessions', 'company'],

        components: {
            AppLayout,
            JetSectionBorder,
            Breadcrumb,
            JetDialogModal,
            JetInput,
            JetInputError,
            JetActionMessage,
            JetButton,
            JetFormSection,
            JetLabel,
            JetTextarea,
            VueDropzone
        },
        data() {
            return {
                dropzoneOptions: {
                    addRemoveLinks: true,
                    destroyDropzone: false,
                    autoProcessQueue: false,
                    manuallyAddFile: true,
                    url: 'https://getShafiq.com', // Just required to initiate DropZone.
                    dictDefaultMessage: "<ion-icon name='cloud-upload-outline' class='text-red-500' size='large'></ion-icon><br/> "+this.$t('words.upload-files-here'),
                    dictRemoveFile: this.$t('words.delete'),
                    thumbnailWidth: 150,
                    maxFilesize: 20,
                    // headers: this.jwtHeaders,
                },
                form: this.$inertia.form({
                    reference_number: '',
                    contract_starts_at: '',
                    contract_period_in_months: '',
                    trainees_count: '',
                    trainee_salary: '',
                    trainer_cost: '',
                    company_reimbursement: '',
                    files: [],
                }, {
                    bag: 'createContract',
                })
            }
        },
        mounted() {

        },
        methods: {
            fileAdded(file) {
                this.form.files.push(file);
            },
            createContract() {
                this.form.post('/back/companies/'+this.company.id+'/contracts', {
                    preserveScroll: true
                });
            },
        }
    }
</script>

<style>
    .dropzone .dz-preview .dz-progress {
        display: none;
    }
    .vue-dropzone > .dz-preview .dz-remove {
        margin: 0 10px;
    }

    .vue-dropzone > .dz-preview .dz-details {
        background-color: rgb(37, 47, 63);
    }

    .dropzone.dz-clickable .dz-message, .dropzone.dz-clickable .dz-message * {
        font-family: Nunito, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
        line-height: 1.5;
    }
</style>
