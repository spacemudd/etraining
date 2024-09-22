<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'trainees', link: route('back.trainees.index')},
                    {title_raw: trainee.name},
                ]"
            ></breadcrumb-container>

            <div class="grid grid-cols-6 gap-6">

                <div class="col-span-6 items-center justify-end bg-gray-50 text-right gap-6">

                    <a :href="route('back.trainees.admin.attendance-sheet.pdf', trainee.id)"
                       target="_blank"
                       class="items-center justify-start text-left float-left rounded-md px-4 py-2 bg-gray-200 hover:bg-gray-300 text-right">
                        {{ $t('words.attendance-sheet') }}
                    </a>

                    <button @click="unblock" class="items-center justify-end rounded-md px-4 py-2 bg-red-600 hover:bg-red-600 text-right text-white">
                        {{ $t('words.unblock') }}
                    </button>

                </div>

                <div class="col-span-6 items-center justify-end bg-gray-50 text-right flex gap-6" v-can="'view-gosi'">
                    <gosi-container :nin-or-iqama="trainee.identity_number"></gosi-container>
                </div>

                <div class="col-span-6 sm:col-span-2">
                    <jet-label for="trainee_group_name" :value="$t('words.group-name')" />
                    <jet-input id="trainee_group_name"
                               type="text"
                               :class="editButton.inputClass"
                               :value="trainee.trainee_group ? trainee.trainee_group.name : ''"
                               autocomplete="off"
                               :disabled="!editButton.editOption" />
                </div>

                <div class="col-span-6 sm:col-span-2">
                    <jet-label for="company_id" :value="$t('words.company')" />
                    <jet-input id="company_id"
                               type="text"
                               :class="editButton.inputClass"
                               :value="trainee.company ? trainee.company.name_ar : ''"
                               autocomplete="off"
                               :disabled="!editButton.editOption" />
                </div>

                <div class="col-span-6 sm:col-span-2">
                    <jet-label for="name" :value="$t('words.name')" />
                    <jet-input id="name" type="text" :class="editButton.inputClass" v-model="trainee.name" autocomplete="off" :disabled="!editButton.editOption" />
                </div>

                <div class="col-span-6 sm:col-span-2">
                    <jet-label for="identity_number" :value="$t('words.identity_number')" />
                    <jet-input id="identity_number" type="text" :class="editButton.inputClass" v-model="trainee.identity_number" :disabled="!editButton.editOption" />
                </div>

                <div class="col-span-6 sm:col-span-2">
                    <jet-label for="birthday" :value="$t('words.birthday')" />
                    <jet-input id="birthday" type="date" :class="editButton.inputClass" v-model="trainee.birthday" :disabled="!editButton.editOption" />
                </div>

                <div class="col-span-6 sm:col-span-2">
                    <jet-label for="phone" :value="$t('words.phone')" />
                    <jet-input id="phone" type="text" :class="editButton.inputClass" v-model="trainee.phone" placeholder="9665XXXXXXXX" :disabled="!editButton.editOption" />
                </div>

                <div class="col-span-6 sm:col-span-2">
                    <jet-label for="phone_additional" :value="$t('words.phone_additional')" />
                    <jet-input id="phone_additional" type="text" :class="editButton.inputClass" v-model="trainee.phone_additional" :disabled="!editButton.editOption" />
                </div>

                <div class="col-span-6 sm:col-span-2">
                    <jet-label for="national_address" :value="$t('words.national-address')" />
                    <jet-input id="national_address" type="text" :class="editButton.inputClass" v-model="trainee.national_address" :disabled="!editButton.editOption" />
                </div>

                <div class="col-span-6 sm:col-span-2">
                    <jet-label for="email" :value="$t('words.email')" />
                    <jet-input id="email" type="text" :class="editButton.inputClass" v-model="trainee.email" :disabled="!editButton.editOption" />
                </div>

                <div class="col-span-6 sm:col-span-2" v-if="this.lang=='ar'">
                    <jet-label for="educational_level" :value="$t('words.educational_level')" />

                    <select :class="editButton.selectInputClass"
                                        v-model="trainee.educational_level_id"
                                        id="educational_level_id"  :disabled="!editButton.editOption" >
                                    <option v-for="educational_level in educational_levels" :key="educational_level.id" :value="educational_level.id">{{ educational_level.name_ar }}</option>
                    </select>

                </div>

                <div class="col-span-6 sm:col-span-2" v-else>
                    <jet-label for="educational_level" :value="$t('words.educational_level')" />

                    <select :class="editButton.selectInputClass"
                                        v-model="trainee.educational_level_id"
                                        id="educational_level_id"  :disabled="!editButton.editOption" >
                                    <option v-for="educational_level in educational_levels" :key="educational_level.id" :value="educational_level.id">{{ educational_level.name_en }}</option>
                    </select>


                </div>

                <div class="col-span-6 sm:col-span-2">
                    <jet-label for="city_id" :value="$t('words.city')" />

                    <select :class="editButton.selectInputClass"
                            v-model="trainee.city_id"
                            id="city_id"  :disabled="!editButton.editOption">
                        <option v-for="city in cities" :key="city.id" :value="city.id">{{ city.name_ar }}</option>
                    </select>
                </div>

                <div class="col-span-6 sm:col-span-1" v-if="this.lang=='ar'">
                    <jet-label for="marital_status" :value="$t('words.marital_status')" />

                    <select :class="editButton.selectInputClass"
                                        v-model="trainee.marital_status_id"
                                        id="city_id"  :disabled="!editButton.editOption" >
                                    <option  v-for="marital_status in marital_statuses" :key="marital_status.id" :value="marital_status.id">{{ marital_status.name_ar }}</option>
                    </select>


                </div>

                <div class="col-span-6 sm:col-span-1" v-else>

                    <jet-label for="marital_status" :value="$t('words.marital_status')" />

                    <select :class="editButton.selectInputClass"
                            v-model="trainee.marital_status_id"
                            id="city_id"  :disabled="!editButton.editOption" >
                        <option  v-for="marital_status in marital_statuses" :key="marital_status.id" :value="marital_status.id">{{ marital_status.name_en }}</option>
                    </select>

                </div>

                <div class="col-span-6 sm:col-span-1">
                    <jet-label for="children_count" :value="$t('words.children_count')" />
                    <jet-input id="children_count" type="text" :class="editButton.inputClass" v-model="trainee.children_count" :disabled="!editButton.editOption" />
                </div>

                <div class="col-span-6 sm:col-span-2">
                    <jet-label for="deleted-at" :value="$t('words.blocked-at')" />
                    <jet-input id="deleted-at" type="text" :class="editButton.inputClass" v-model="trainee.deleted_at_timezone" :disabled="!editButton.editOption" />
                </div>

                <div class="col-span-6 sm:col-span-2">
                    <jet-label for="reason" :value="$t('words.delete-remark')" />
                    <input id="reason"
                            autocomplete="off"
                               type="text"
                               class="mt-1 block w-full border-2 border-gray-200 bg-white py-2.5 px-4 pr-8 rounded leading-tight focus:outline-none focus:border-blue-300"
                               @blur="saveDeletedRemark(trainee)"
                               v-model="trainee.deleted_remark"/>
                </div>

                <div class="col-span-6 sm:col-span-6">
                    <jet-label for="name" :value="$t('words.status')" />
                    <p>

                        <span class="text-sm inline-block mt-2 p-1 px-2 bg-red-300 rounded-lg">
                            {{ $t('words.blocked') }}
                        </span>

                    </p>
                </div>
            </div>


            <jet-section-border></jet-section-border>

            <div class="grid grid-cols-1 md:grid-cols-6 gap-6 my-2">
                <div class="md:col-span-4 lg:col-span-3 sm:col-span-3">
                    <div class="px-4 sm:px-0">
                        <h3 class="text-lg font-medium text-gray-900">
                            {{ $t('words.documents') }}
                        </h3>

                        <p class="mt-1 text-sm text-gray-600">
                            {{ $t('words.documents-help') }}
                        </p>

                        <div class="mt-3">
                            <inertia-link class="text-blue-600 border-2 border-blue-600 text-xs p-1" :href="route('back.trainees.files.index', trainee.id)">
                                {{ $t('words.other-files') }} ({{ trainee.general_files_count }})
                            </inertia-link>
                        </div>
                    </div>
                </div>

                <div class="md:col-span-3 lg:col-span-1 sm:col-span-3">
                    <jet-label :value="$t('words.identity-card-photocopy')" class="mb-2" />

                    <div class="bg-white border-2 rounder-lg flex flex-col justify-center items-center min-container-upload" v-if="trainee.identity_copy_url">
                        <a class="bg-gray-700 text-white font-semibold p-2 text-center w-1/2 rounded my-1" target="_blank" :href="trainee.identity_copy_url">{{ $t('words.download') }}</a>
                        <button class="bg-red-500 text-white font-semibold p-2 text-center w-1/2 rounded my-1" @click="deleteIdentity">{{ $t('words.delete') }}</button>
                    </div>
                    <vue-dropzone v-else
                                  id="dropzoneIdentity"
                                  @vdropzone-sending="sendingCsrf"
                                  :options="dropzoneOptionsIdentity"
                    ></vue-dropzone>
                </div>

                <div class="md:col-span-3 lg:col-span-1 sm:col-span-3">
                    <jet-label :value="$t('words.qualification-photocopy')" class="mb-2" />

                    <div class="bg-white border-2 rounder-lg flex flex-col justify-center items-center min-container-upload" v-if="trainee.qualification_copy_url">
                        <a class="bg-gray-700 text-white font-semibold p-2 text-center w-1/2 rounded my-1" target="_blank" :href="trainee.qualification_copy_url">{{ $t('words.download') }}</a>
                        <button class="bg-red-500 text-white font-semibold p-2 text-center w-1/2 rounded my-1" @click="deleteQualification">{{ $t('words.delete') }}</button>
                    </div>
                    <vue-dropzone v-else
                                  id="dropzoneQualification"
                                  @vdropzone-sending="sendingCsrf"
                                  :options="dropzoneOptionsQualification"
                    ></vue-dropzone>
                </div>

                <div class="md:col-span-3 lg:col-span-1 sm:col-span-3">
                    <jet-label :value="$t('words.bank-account-photocopy')" class="mb-2" />

                    <div class="bg-white border-2 rounder-lg flex flex-col justify-center items-center min-container-upload" v-if="trainee.bank_account_copy_url">
                        <a class="bg-gray-700 text-white font-semibold p-2 text-center w-1/2 rounded my-1" target="_blank" :href="trainee.bank_account_copy_url">{{ $t('words.download') }}</a>
                        <button class="bg-red-500 text-white font-semibold p-2 text-center w-1/2 rounded my-1" @click="deleteBankAccount">{{ $t('words.delete') }}</button>
                    </div>
                    <vue-dropzone v-else
                                  id="dropzoneBankAccount"
                                  @vdropzone-sending="sendingCsrf"
                                  :options="dropzoneOptionsBankAccount"
                    ></vue-dropzone>
                </div>
            </div>

            <jet-section-border></jet-section-border>

            <div
                class="grid grid-cols-1 md:grid-cols-2 gap-6 my-2"
            >
                <div class="md:col-span-4 lg:col-span-1 sm:col-span-3">
                    <div class="px-4 sm:px-0">
                        <h3 class="text-lg font-medium text-gray-900">
                            {{ $t('words.invoices') }}
                        </h3>

                        <p class="mt-1 text-sm text-gray-600">
                            {{ $t('words.invoices-help') }}
                        </p>
                    </div>
                </div>
                <div class="md:col-span-3 lg:col-span-1 sm:col-span-3">
                    <div class="flex w-full justify-end">
                        <inertia-link :href="route('back.trainees.invoices.create', trainee.id)">
                            <jet-button type="button">
                                {{ $t('words.issue-invoice') }}
                            </jet-button>
                        </inertia-link>
                    </div>
                    <table class="w-full whitespace-no-wrap bg-white rounded-lg my-5 p-5 shadow text-sm">
                        <tr class="text-left font-bold text-center">
                            <th class="px-6 pt-6 pb-4 text-left">{{ $t('words.invoice-no') }}</th>
                            <th class="px-6 pt-6 pb-4">{{ $t('words.amount') }}</th>
                            <th class="px-6 pt-6 pb-4">{{ $t('words.status') }}</th>
                        </tr>
                        <tr
                            v-for="invoice in trainee.invoices"
                            :key="invoice.id"
                            class="border-t hover:bg-gray-100 focus-within:bg-gray-100 text-center"
                        >
                            <td class="px-4 py-4 text-left text-blue-500">
                                <inertia-link :href="route('back.finance.invoices.show', invoice.id)">
                                    {{ invoice.number_formatted }}
                                </inertia-link>
                            </td>

                            <td class="px-4 py-4">
                                {{ invoice.grand_total }}
                            </td>

                            <td class="px-4 py-4">
                                {{ invoice.status_formatted }}
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <jet-section-border></jet-section-border>

        <trainee-audit-container :trainee_id="trainee.id"></trainee-audit-container>
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
    import CompanyContractsPagination from "@/Components/CompanyContractsPagination";
    import BreadcrumbContainer from "@/Components/BreadcrumbContainer";
    import VueDropzone from 'vue2-dropzone'
    import 'vue2-dropzone/dist/vue2Dropzone.min.css'
    import SelectTraineeGroup from "@/Components/SelectTraineeGroup";
    import EmptySlate from "@/Components/EmptySlate";
    import ValidationErrors from "@/Components/ValidationErrors";
    import NProgress from 'nprogress'
    import TraineeAuditContainer from "@/Components/TraineeAuditContainer";
    import GosiContainer from "@/Components/GosiContainer";

    export default {
        props: [
            'sessions',
            'trainee',
            'cities',
            'marital_statuses',
            'educational_levels',
            'trainee_groups',
            'trainee_group_trainees',
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
            CompanyContractsPagination,
            BreadcrumbContainer,
            VueDropzone,
            SelectTraineeGroup,
            ValidationErrors,
            EmptySlate,
            TraineeAuditContainer,
            GosiContainer,
        },
        data() {
            return {
                new_trainee_group: {
                    name: '',
                    id: '',
                },
                cancelButton: {
                    text: this.$t('words.cancel'),
                },
                lang: (this.$t('words.edit') == "Edit") ? 'en':'ar',
                editButton: {
                    text: this.$t('words.edit'),
                    editOption: false,
                    inputClass: "mt-1 block w-full bg-gray-200",
                    selectInputClass: "mt-1 block w-full border border-gray-200 bg-gray-200 py-2.5 px-4 pr-8 rounded leading-tight focus:outline-none"
                },
                dropzoneOptionsIdentity: {
                    destroyDropzone: false,
                    url: route('back.trainees.attachments.identity', {trainee_id: this.trainee.id}),
                    dictDefaultMessage: "<ion-icon name='cloud-upload-outline' class='text-red-500' size='large'></ion-icon><br/> "+this.$t('words.upload-files-here'),
                    thumbnailWidth: 150,
                    maxFilesize: 20,
                },
                dropzoneOptionsQualification: {
                    destroyDropzone: false,
                    url: route('back.trainees.attachments.qualification', {trainee_id: this.trainee.id}),
                    dictDefaultMessage: "<ion-icon name='cloud-upload-outline' class='text-red-500' size='large'></ion-icon><br/> "+this.$t('words.upload-files-here'),
                    thumbnailWidth: 150,
                    maxFilesize: 20,
                },
                dropzoneOptionsBankAccount: {
                    destroyDropzone: false,
                    url: route('back.trainees.attachments.bank-account', {trainee_id: this.trainee.id}),
                    dictDefaultMessage: "<ion-icon name='cloud-upload-outline' class='text-red-500' size='large'></ion-icon><br/> "+this.$t('words.upload-files-here'),
                    thumbnailWidth: 150,
                    maxFilesize: 20,
                },
            }
        },
        mounted() {
            // if(!this.trainee.trainee_group_object) {
            //     this.trainee.trainee_group_object = this.new_trainee_group;
            // }
        },
        methods: {
            saveDeletedRemark(trainee) {
                NProgress.start()
                axios.put(route('back.trainees.update-deleted-remark', trainee.id), {
                    deleted_remark: trainee.deleted_remark,
                }).then(response => {
                    NProgress.stop();
                }).catch(error => {
                    throw error;
                })
            },
            sendingCsrf(file, xhr, formData) {
                xhr.setRequestHeader('X-CSRF-TOKEN', window.token ? window.token.content : '');
            },
            deleteIdentity() {
                if (confirm(this.$t('words.are-you-sure'))) {
                    this.$inertia.delete(route('back.trainees.attachments.identity.destroy', {trainee_id: this.trainee.id}));
                }
            },
            deleteQualification() {
                if (confirm(this.$t('words.are-you-sure'))) {
                    this.$inertia.delete(route('back.trainees.attachments.qualification.destroy', {trainee_id: this.trainee.id}));
                }
            },
            deleteBankAccount() {
                if (confirm(this.$t('words.are-you-sure'))) {
                    this.$inertia.delete(route('back.trainees.attachments.bank-account.destroy', {trainee_id: this.trainee.id}));
                }
            },
            unblock() {
                if (confirm(this.$t('words.are-you-sure') + ' ' + this.trainee.deleted_remark)) {
                    this.$inertia.post(route('back.trainees.unblock', {trainee_id: this.trainee.id}));
                }
            },
        }
    }
</script>

<style>
    .min-container-upload {
        min-height: 168px;
    }
    .vue-dropzone {
        pointer-events: none;
    }
</style>
