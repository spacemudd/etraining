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


                    <button @click="blockTrainee" class=" items-center justify-start text-left float-left rounded-md px-4 py-2 bg-red-300 hover:bg-red-400 text-right">
                        {{ $t('words.block-trainee') }}
                    </button>

                   <button v-if="!editButton.editOption" @click="editTrainee" class=" items-center justify-end rounded-md px-4 py-2 bg-gray-200 hover:bg-gray-300 text-right">
                        {{ editButton.text }}
                    </button>

                    <button v-else @click="editTrainee" class=" items-center justify-end rounded-md px-4 py-2 bg-green-300 hover:bg-green-400 text-right">
                        {{ editButton.text }}
                    </button>

                    <button v-if="editButton.editOption" @click="cancelEdit" class=" items-center justify-end rounded-md px-4 py-2 bg-red-300 hover:bg-red-400 text-right">
                        {{ cancelButton.text }}
                    </button>


                    <button v-if="!trainee.user_id"
                            @click="openTraineeAccount"
                            class=" items-center justify-end rounded-md px-4 py-2 bg-yellow-200 hover:bg-yellow-300 text-right">
                        {{ $t('words.open-an-account') }}
                    </button>
                    <button v-if="trainee.is_pending_approval" @click="approveTrainee"
                            class=" items-center justify-end rounded-md px-4 py-2 bg-yellow-200 hover:bg-yellow-300 text-right">
                        {{ $t('words.approve-trainee') }}
                    </button>
                </div>

                <div v-if="!editButton.editOption" class="col-span-6 sm:col-span-2">

                    <jet-label for="trainee_group_name" :value="$t('words.group-name')" />
                    <jet-input id="group-name" type="text" :class="editButton.inputClass" v-model="trainee.trainee_group_object.name" autocomplete="off" :disabled="!editButton.editOption" />

                </div>

                <div v-else class="col-span-6 sm:col-span-2">
                            <jet-label for="trainee_group_name" :value="$t('words.group-name')" />
                            <select-trainee-group
                                                class="mt-1.5"
                                                  :selectedItem="trainee.trainee_group_object"
                                                  @input="selectGroupName"
                                                  v-model="trainee.trainee_group_object"
                                                  :disabled="!editButton.editOption"
                            />
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
                                        id="city_id"  :disabled="!editButton.editOption" >
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

                <div class="col-span-6 sm:col-span-6">
                    <jet-label for="name" :value="$t('words.status')" />
                    <p>
                        <span v-if="trainee.is_pending_uploading_files" class="text-sm inline-block mt-2 p-1 px-2 bg-blue-300 rounded-lg">
                            {{ $t('words.incomplete-application') }}
                        </span>

                        <span v-if="trainee.is_pending_approval" class="text-sm inline-block mt-2 p-1 px-2 bg-yellow-200 rounded-lg">
                            {{ $t('words.nominated-instructor') }}
                        </span>

                        <span v-if="trainee.is_approved" class="text-sm inline-block mt-2 p-1 px-2 bg-green-300 rounded-lg">
                            {{ $t('words.approved') }}
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
    import CompanyContractsPagination from "@/Components/CompanyContractsPagination";
    import BreadcrumbContainer from "@/Components/BreadcrumbContainer";
    import VueDropzone from 'vue2-dropzone'
    import 'vue2-dropzone/dist/vue2Dropzone.min.css'
    import SelectTraineeGroup from "@/Components/SelectTraineeGroup";


    export default {
        props: ['sessions', 'trainee', 'cities', 'marital_statuses', 'educational_levels', 'trainee_groups', 'trainee_group_trainees'],

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
            if(!this.trainee.trainee_group_object) {
                this.trainee.trainee_group_object = this.new_trainee_group;
            }
        },
        methods: {
            selectGroupName(input) {

                this.trainee.trainee_group_name = input.name;

            },
            blockTrainee() {
                    this.$inertia.get(route('back.trainees.block', {trainee_id: this.trainee.id}));
            },
            cancelEdit() {
                            this.editButton.editOption = false;
                            this.editButton.inputClass = 'mt-1 block w-full bg-gray-200';
                            this.editButton.selectInputClass = 'mt-1 block w-full border border-gray-200 bg-gray-200 py-2.5 px-4 pr-8 rounded leading-tight focus:outline-none';
                            this.editButton.text = this.$t('words.edit');
                            window.location.reload();
            },
            editTrainee() {
                console.log(this.trainee.trainee_group_object);
                if (!this.editButton.editOption) {
                    this.editButton.editOption = true;
                    this.editButton.inputClass = 'mt-1 block w-full bg-white';
                    this.editButton.selectInputClass = "mt-1 block w-full border border-gray-200 bg-white py-2.5 px-4 pr-8 rounded leading-tight focus:outline-none"
                    this.editButton.text = this.$t('words.save');
                } else {
                this.$inertia.post(route('back.trainees.edit', this.trainee.id), this.trainee).then(response => {
                            this.editButton.editOption = false;
                            this.editButton.inputClass = 'mt-1 block w-full bg-gray-200';
                            this.editButton.selectInputClass = 'mt-1 block w-full border border-gray-200 bg-gray-200 py-2.5 px-4 pr-8 rounded leading-tight focus:outline-none';
                            this.editButton.text = this.$t('words.edit');
                    }).catch(error => {
                            throw error;
                    })
                }
            },
            approveTrainee() {
                if (confirm(this.$t('words.are-you-sure'))) {
                    this.$inertia.post(route('back.trainees.approve-user', {trainee_id: this.trainee.id})).then(response => {
                        this.$inertia.get(route('back.trainees.show', this.trainee.id))
                    });
                }
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
            openTraineeAccount() {
                if (confirm(this.$t('words.are-you-sure'))) {
                    this.$inertia.post(route('back.trainees.create-user', {trainee_id: this.trainee.id}));
                }
            },
        }
    }
</script>

<style>
    .min-container-upload {
        min-height: 168px;
    }
</style>
