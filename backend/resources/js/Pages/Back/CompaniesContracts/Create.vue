<template>
    <app-layout>
        <div class="container px-6 mx-auto grid">
            <div class="mt-20" v-if="company">
                <div class="md:grid md:grid-cols-3 md:gap-6">
                    <div class="md:col-span-1">
                        <div class="px-4 sm:px-0">
                            <h3 class="text-lg font-medium text-gray-900">
                                {{ company.name_ar }}<br/>
                                {{ company.name_en }}
                            </h3>
                            <p class="mt-1 text-sm text-gray-600">
                                {{ $t('words.upload-new-contract') }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-5 md:mt-0 md:col-span-2">
                        <form @submit.prevent="createContract" enctype="multipart/form-data">
                            <div class="shadow overflow-hidden sm:rounded-md">
                                <div class="px-4 py-5 bg-white sm:p-6">
                                    <div class="grid grid-cols-4 gap-6">
                                        <template v-for="fieldName in [
                                                'reference_number',
                                                'contract_starts_at',
                                                'contract_period_in_months',
                                                'trainees_count',
                                                'trainee_salary',
                                                'instructor_cost',
                                                'company_reimbursement',
                                                ]">
                                            <div class="col-span-2 sm:col-span-2">
                                                <jet-label :for="fieldName" :value="$t('words.'+fieldName)" />
                                                <jet-input :id="fieldName"
                                                           :type="fieldName==='contract_starts_at' ? 'date' : 'text'"
                                                           class="mt-1 block w-full"
                                                           v-model="contractForm[fieldName]"
                                                           autocomplete="off"
                                                           :placeholder="fieldName==='reference_number' ? $t('words.leave-blank-to-be-automatically-generated') : ''"
                                                           :autofocus="fieldName==='reference_number'"
                                                           :required="fieldName === 'contract_starts_at'"
                                                />
                                                <!--<jet-input-error :message="contractForm.error(fieldName)" class="mt-2" />-->
                                            </div>
                                        </template>

                                        <div class="col-span-4 sm:col-span-4">
                                            <jet-label for="notes" :value="$t('words.notes')" />
                                            <jet-textarea id="notes" type="textarea" class="mt-1 block w-full" v-model="contractForm.notes" />
                                            <!--<jet-input-error :message="contractForm.error('notes')" class="mt-2" />-->
                                        </div>

                                        <div class="col-span-4 sm:col-span-4">
                                            <!--<vue-dropzone ref="dropZoneContainer"-->
                                            <!--              id="dropzone"-->
                                            <!--              @vdropzone-file-added="fileAdded"-->
                                            <!--              :options="dropzoneOptions"-->
                                            <!--&gt;</vue-dropzone>-->
                                            <jet-label for="contractCopy" :value="$t('words.contract')" />
                                            <input type="file"
                                                   ref="contactCopy"
                                                   @change="contractCopyChanged">
                                        </div>
                                        <!--Done form-->

                                        <div class="flex items-center justify-start px-4 py-3 bg-gray-50 text-right">
                                            <!--<jet-action-message :on="contractForm.recentlySuccessful" class="mr-3">-->
                                            <!--    {{ $t('words.created-successfully') }}-->
                                            <!--</jet-action-message>-->

                                            <inertia-link :href="`/back/companies/${company.id}`" class="flex items-center justify-start rtl:ml-4 ltr:mr-4 rounded-md px-4 py-2 bg-white hover:bg-gray-300 text-right">
                                                {{ $t('words.cancel') }}
                                            </inertia-link>

                                            <jet-button :class="{ 'opacity-25': $wait.is('SAVING_CONTRACT') }" :disabled="$wait.is('SAVING_CONTRACT')">
                                                {{ $t('words.save') }}
                                            </jet-button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
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
    import Logrocket from 'logrocket';

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
                contractFormData: new FormData(),
                contractCopyFile: null,
                contractForm: {
                    reference_number: '',
                    contract_starts_at: '',
                    contract_period_in_months: '',
                    trainees_count: '',
                    trainee_salary: '',
                    instructor_cost: '',
                    company_reimbursement: '',
                }
            }
        },
        mounted() {

        },
        methods: {
            contractCopyChanged(e, filename) {
                this.contractCopyFile = e.target.files[0];
                this.contractFormData.append('files[]', this.contractCopyFile);
            },
            createContract() {
                this.$wait.start('SAVING_CONTRACT');
                let formToSend = new FormData();
                formToSend.append('reference_number', this.contractForm.reference_number);
                formToSend.append('contract_starts_at', this.contractForm.contract_starts_at);
                formToSend.append('contract_period_in_months', this.contractForm.contract_period_in_months);
                formToSend.append('trainee_salary', this.contractForm.trainee_salary);
                formToSend.append('trainees_count', this.contractForm.trainees_count);
                formToSend.append('instructor_cost', this.contractForm.instructor_cost);
                formToSend.append('company_reimbursement', this.contractForm.company_reimbursement);
                formToSend.append('notes', this.contractForm.notes);
                formToSend.append('files[]', this.contractCopyFile)
                axios.post('/back/companies/'+this.company.id+'/contracts', formToSend)
                .then(response => {
                    window.location = '/back/companies/'+this.company.id;
                }).catch(error => {
                    this.$wait.end('SAVING_CONTRACT');
                    alert(error.response.data.message+' - '+'قد يمكن ان الرقم المرجعي مستخدم من قبل');
                    Logrocket.captureException(error);
                    throw error;
                })
            },
        }
    }
</script>
