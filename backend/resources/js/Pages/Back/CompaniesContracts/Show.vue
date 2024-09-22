<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'companies', link: route('back.companies.index')},
                    {title_raw: contract.company.name_ar, link: contract.company.show_url},
                    {title_raw: $t('words.contracts')},
                    {title_raw: contract.reference_number ? contract.reference_number : '-'},
                ]"
            ></breadcrumb-container>

            <div class="grid grid-cols-4 gap-6">
                <div class="col-span-4 flex items-center justify-end bg-gray-50 text-right">
                    <inertia-link :href="route('back.companies.contracts.edit', {company_id: contract.company_id, contract: contract.id})"
                                  class="flex items-center justify-start rounded-md mx-4 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-right">
                        {{ $t('words.edit') }}
                    </inertia-link>
                    <button @click="deleteContract"
                            v-can="'delete-company-contracts'"
                            class="bg-red-600 p-2 rounded-lg text-white inline-flex items-center">
                        <btn-loading-indicator v-if="$wait.is('DELETING_CONTRACT')" />
                        <span>{{ $t('words.delete') }}</span>
                    </button>
                </div>

                <div class="col-span-3">
                    <div class="grid grid-cols-4 gap-6">
                        <div class="col-span-4 sm:col-span-1">
                            <jet-label for="reference_number" :value="$t('words.reference_number')" />
                            <jet-input id="reference_number" type="text" class="mt-1 block w-full bg-gray-200" :value="contract.reference_number" disabled />
                        </div>

                        <div class="col-span-4 sm:col-span-1">
                            <jet-label for="company" :value="$t('words.company')" />
                            <jet-input id="company" type="text" class="mt-1 block w-full bg-gray-200" :value="contract.company.name_ar" disabled />
                        </div>

                        <div class="col-span-4 sm:col-span-1">
                            <jet-label for="contract_starts_at" :value="$t('words.contract_starts_at')" />
                            <jet-input id="contract_starts_at" type="text" class="mt-1 block w-full bg-gray-200" :value="contract.contract_starts_at_timezone" disabled />
                        </div>

                        <div class="col-span-4 sm:col-span-1">
                            <jet-label for="contract_ends_at" :value="$t('words.contract-end-date')" />
                            <jet-input id="contract_ends_at" type="text" class="mt-1 block w-full bg-gray-200" :value="contract.contract_ends_at_timezone" disabled />
                        </div>

                        <!--<div class="col-span-4 sm:col-span-1">-->
                        <!--    <jet-label for="auto_renewal" :value="$t('words.auto-renewal')" />-->
                        <!--    <jet-input id="auto_renewal" type="text" class="mt-1 block w-full bg-gray-200" :value="contract.auto_renewal" disabled />-->
                        <!--</div>-->

                        <div class="col-span-4 sm:col-span-1">
                            <jet-label for="trainees-count" :value="$t('words.trainees-count')" />
                            <jet-input id="trainees-count" type="text" class="mt-1 block w-full bg-gray-200" :value="contract.trainees_count" disabled />
                        </div>

                        <div class="col-span-4 sm:col-span-1">
                            <jet-label for="trainees-salary" :value="$t('words.trainee_salary')" />
                            <jet-input id="trainees-salary" type="text" class="mt-1 block w-full bg-gray-200" :value="contract.trainee_salary" disabled />
                        </div>

                        <div class="col-span-4 sm:col-span-1">
                            <jet-label for="instructor-cost" :value="$t('words.instructor_cost')" />
                            <jet-input id="instructor-cost" type="text" class="mt-1 block w-full bg-gray-200" :value="contract.instructor_cost" disabled />
                        </div>

                        <div class="col-span-4 sm:col-span-1">
                            <jet-label for="company-reimbursement" :value="$t('words.company_reimbursement')" />
                            <jet-input id="company-reimbursement" type="text" class="mt-1 block w-full bg-gray-200" :value="contract.company_reimbursement" disabled />
                        </div>

                        <div class="col-span-4 sm:col-span-1">
                            <jet-label for="notes" :value="$t('words.notes')" />
                            <jet-input id="notes" type="text" class="mt-1 block w-full bg-gray-200" :value="contract.notes" disabled />
                        </div>
                    </div>
                </div>

                <div class="col-span-3 md:col-span-1">
                    <h2 class="block font-medium text-sm text-gray-700">
                        {{ $t('words.copy') }}
                    </h2>
                    <div class="p-5 bg-cool-gray-100 rounded-lg">
                        <!-- Empty slate for uploading a contract -->
                        <empty-slate v-if="!contract.has_attachments && ! $wait.is('UPLOADING_CONTRACT_FILES')">
                            <template #actions>
                                <div class="flex justify-content-center flex-col text-center">
                                    <div class="inline text-xs">
                                        <input type="file"
                                               style="width:200px;"
                                               ref="contactCopy"
                                               @change="changedFile">
                                    </div>

                                    <div class="mt-5">
                                        <button @click="uploadFile()"
                                                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150">
                                            {{ $t('words.upload-files-here') }}
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </empty-slate>

                        <!-- When uploading the file. -->
                        <div v-if="$wait.is('UPLOADING_CONTRACT_FILES')">
                            <div class="text-xs flex flex-col"
                                 style="margin: 50px auto auto;width:150px;">
                                <progress v-if="progressValue"
                                          class="progress is-info"
                                          :value="progressValue"
                                          max="100">{{ progressValue }}%</progress>
                                <p class="text-center">{{ progressValue ? progressValue : 0 }}%</p>
                            </div>
                        </div>

                        <a v-if="contract.has_attachments"
                           target="_blank"
                           class="bg-gray-500 h-10 text-white text-sm rounded-sm mt-2 flex justify-center items-center"
                           :href="route('back.companies.contracts.attachments', {company_id: contract.company_id, contract_id: contract.id})">
                            <span class="inline-block">
                                {{ $t('words.download-scan') }}
                            </span>
                        </a>

                        <div class="flex justify-items-end mt-5 grid">
                            <button @click="deleteContractAttachment"
                                    v-if="contract.has_attachments"
                                    :class="{'opacity-50': $wait.is('DELETING_ATTACHMENTS')}"
                                    class="bg-red-600 p-2 rounded-lg text-white text-xs inline-flex items-center">
                                <btn-loading-indicator v-if="$wait.is('DELETING_ATTACHMENTS')" />
                                <span>{{ $t('words.delete') }}</span>
                            </button>
                        </div>
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
    import BreadcrumbContainer from "@/Components/BreadcrumbContainer";
    import BtnLoadingIndicator from "@/Components/BtnLoadingIndicator";
    import EmptySlate from "@/Components/EmptySlate";
    import NProgress from 'nprogress'

    export default {
        props: [
            'contract',
        ],

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
            BreadcrumbContainer,
            BtnLoadingIndicator,
            EmptySlate,
        },
        data() {
            return {
                contractCopyFile: null,
                contractFormData: new FormData(),
                progressValue: null,
                form: this.$inertia.form({
                    name_ar: '',
                    name_en: '',
                    cr_number: '',
                    contact_number: '',
                    company_rep: '',
                    company_rep_mobile: '',
                    address: '',
                    email: '',
                }, {
                    bag: 'createCompany',
                })
            }
        },
        mounted() {
            this.$wait.end('UPLOADING_CONTRACT_FILES');
        },
        methods: {
            changedFile(e, filename) {
                this.contractFormData = new FormData();
                this.contractCopyFile = e.target.files[0];
                this.contractFormData.append('files[]', this.contractCopyFile);
            },
            uploadFile() {
                if (!this.contractCopyFile) {
                    alert('الرجاء اختيار ملف');
                    return;
                }

                let vm = this;
                var config = {
                    onUploadProgress: function(progressEvent) {
                        var percentCompleted = Math.round( (progressEvent.loaded * 100) / progressEvent.total );
                        if(vm.onProgress) vm.onProgress(percentCompleted);
                        return percentCompleted;
                    },
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    },
                };
                this.$wait.start('UPLOADING_CONTRACT_FILES');
                axios.post(route('back.companies.contracts.attachments.store', {
                    company_id: this.contract.company_id,
                    contract: this.contract.id,
                }), this.contractFormData, config)
                .then(response => {
                    this.$inertia.visit(route('back.companies.contracts.show', {
                        company_id: this.contract.company_id,
                        contract: this.contract.id,
                    }), {method: 'get'});
                }).catch(error => {
                    this.$wait.end('UPLOADING_CONTRACT_FILES');
                });
            },
            onProgress(percent) {
                this.progressValue = percent;
            },
            deleteContractAttachment() {
                if (confirm(this.$t('words.are-you-sure'))) {
                    this.$wait.start('DELETING_ATTACHMENTS');
                    this.$inertia.delete(route('back.companies.contracts.attachments.delete', {
                        company_id: this.contract.company_id,
                        contract: this.contract.id,
                    })).then(() => {
                        this.$wait.end('DELETING_ATTACHMENTS');
                    }).catch(error => {
                        this.$wait.end('DELETING_ATTACHMENTS');
                        alert(error.response.data.message);
                        throw error;
                    });
                }
            },
            deleteContract() {
                if (confirm(this.$t('words.are-you-sure'))) {
                    this.$inertia.delete(route('back.companies.contracts.destroy', {
                        company_id: this.contract.company_id,
                        contract: this.contract.id,
                    }));
                }
            }
        }
    }
</script>

