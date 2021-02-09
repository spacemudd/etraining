<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'instructors', link: route('back.instructors.index')},
                    {title_raw: instructor.name},
                ]"
            ></breadcrumb-container>

            <div class="grid grid-cols-6 gap-6">

                <div class="col-span-6 items-center justify-end bg-gray-50 text-right gap-6">

                    <button @click="blockInstructor" class=" items-center justify-start text-left float-left rounded-md px-4 py-2 bg-red-300 hover:bg-red-400 text-right">
                        {{ $t('words.block-instructor') }}
                    </button>

                    <button v-if="!editButton.editOption" @click="editInstructor" class=" items-center justify-end rounded-md px-4 py-2 bg-gray-200 hover:bg-gray-300 text-right">
                        {{ editButton.text }}
                    </button>

                    <button v-else @click="editInstructor" class=" items-center justify-end rounded-md px-4 py-2 bg-green-300 hover:bg-green-400 text-right">
                        {{ editButton.text }}
                    </button>

                    <button v-if="editButton.editOption" @click="cancelEdit" class=" items-center justify-end rounded-md px-4 py-2 bg-red-300 hover:bg-red-400 text-right">
                        {{ cancelButton.text }}
                    </button>


                    <button v-if="!instructor.user_id" @click="openInstructorAccount" class=" items-center justify-end rounded-md px-4 py-2 bg-yellow-200 hover:bg-yellow-300 text-right">
                        {{ $t('words.open-an-account') }}
                    </button>

                    <button v-if="instructor.is_pending_approval" @click="approveInstructor" class=" items-center justify-end rounded-md px-4 py-2 bg-yellow-200 hover:bg-yellow-300 text-right">
                        {{ $t('words.approve-instructor') }}
                    </button>
                </div>

                <div class="col-span-6 sm:col-span-2">
                    <jet-label for="name" :value="$t('words.name')" />
                    <jet-input id="name" type="text"  :class="editButton.inputClass" v-model="instructor.name" autocomplete="off" :disabled="!editButton.editOption"  />
                </div>

                <div class="col-span-6 sm:col-span-2">
                    <jet-label for="phone" :value="$t('words.phone')" />
                    <jet-input id="phone" type="text"  :class="editButton.inputClass" v-model="instructor.phone" autocomplete="off" :disabled="!editButton.editOption"  />
                </div>

                <div class="col-span-6 sm:col-span-2">
                    <jet-label for="identity_number" :value="$t('words.identity_number')" />
                    <jet-input id="identity_number" type="text"  :class="editButton.inputClass" v-model="instructor.identity_number" :disabled="!editButton.editOption"  />
                </div>

                <div class="col-span-6 sm:col-span-2">
                    <jet-label for="email" :value="$t('words.email')" />
                    <jet-input id="email" type="text"  :class="editButton.inputClass" v-model="instructor.email" :disabled="!editButton.editOption"  />
                </div>

                <div class="col-span-6 sm:col-span-2">
                    <jet-label for="city_id" :value="$t('words.city')" />

                    <select :class="editButton.selectInputClass"
                                        v-model="instructor.city_id"
                                        id="city_id"  :disabled="!editButton.editOption" >
                                    <option v-for="city in cities" :key="city.id" :value="city.id">{{ city.name_ar }}</option>
                    </select>


                </div>

                <div class="col-span-6 sm:col-span-2">
                    <jet-label for="twitter_link" :value="$t('words.twitter_link')" />
                    <jet-input id="twitter_link" type="text"  :class="editButton.inputClass" v-model="instructor.twitter_link" :disabled="!editButton.editOption"  />
                </div>

                <div class="col-span-6 sm:col-span-6">
                    <jet-label for="name" :value="$t('words.status')" />
                    <p>
                        <span v-if="instructor.is_pending_uploading_files" class="text-sm inline-block mt-2 p-1 px-2 bg-blue-300 rounded-lg">
                            {{ $t('words.incomplete-application') }}
                        </span>

                        <span v-if="instructor.is_pending_approval" class="text-sm inline-block mt-2 p-1 px-2 bg-yellow-200 rounded-lg">
                            {{ $t('words.nominated-instructor') }}
                        </span>

                        <span v-if="instructor.is_approved" class="text-sm inline-block mt-2 p-1 px-2 bg-green-300 rounded-lg">
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
                    <jet-label :value="$t('words.cv-full')" class="mb-2" />

                    <div class="bg-white border-2 rounder-lg flex flex-col justify-center items-center min-container-upload" v-if="instructor.cv_full_copy_url">
                        <a class="bg-gray-700 text-white font-semibold p-2 text-center w-1/2 rounded my-1" target="_blank" :href="instructor.cv_full_copy_url">{{ $t('words.view') }}</a>
                        <button class="bg-red-500 text-white font-semibold p-2 text-center w-1/2 rounded my-1" @click="deleteCvFull">{{ $t('words.delete') }}</button>
                    </div>
                    <vue-dropzone v-else
                                  id="dropzoneIdentity"
                                  @vdropzone-sending="sendingCsrf"
                                  :options="dropzoneOptionsCvFull"
                    ></vue-dropzone>
                </div>

                <div class="md:col-span-3 lg:col-span-1 sm:col-span-3">
                    <jet-label :value="$t('words.cv-summary')" class="mb-2" />

                    <div class="bg-white border-2 rounder-lg flex flex-col justify-center items-center min-container-upload" v-if="instructor.cv_summary_copy_url">
                        <a class="bg-gray-700 text-white font-semibold p-2 text-center w-1/2 rounded my-1" target="_blank" :href="instructor.cv_summary_copy_url">{{ $t('words.view') }}</a>
                        <button class="bg-red-500 text-white font-semibold p-2 text-center w-1/2 rounded my-1" @click="deleteCvSummary">{{ $t('words.delete') }}</button>
                    </div>
                    <vue-dropzone v-else
                                  id="dropzoneQualification"
                                  @vdropzone-sending="sendingCsrf"
                                  :options="dropzoneOptionsCvSummary"
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

    export default {
        props: ['sessions', 'instructor', 'cities'],

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
        },
        data() {
            return {
                cancelButton: {
                    text: this.$t('words.cancel'),
                },
                editButton: {
                    text: this.$t('words.edit'),
                    editOption: false,
                    inputClass: "mt-1 block w-full bg-gray-200",
                    selectInputClass: "mt-1 block w-full border border-gray-200 bg-gray-200 py-2.5 px-4 pr-8 rounded leading-tight focus:outline-none"
                },
                dropzoneOptionsCvFull: {
                    destroyDropzone: false,
                    url: route('back.instructors.attachments.cv-full', {instructor_id: this.instructor.id}),
                    dictDefaultMessage: "<ion-icon name='cloud-upload-outline' class='text-red-500' size='large'></ion-icon><br/> "+this.$t('words.upload-files-here'),
                    thumbnailWidth: 150,
                    maxFilesize: 20,
                },
                dropzoneOptionsCvSummary: {
                    destroyDropzone: false,
                    url: route('back.instructors.attachments.cv-summary', {instructor_id: this.instructor.id}),
                    dictDefaultMessage: "<ion-icon name='cloud-upload-outline' class='text-red-500' size='large'></ion-icon><br/> "+this.$t('words.upload-files-here'),
                    thumbnailWidth: 150,
                    maxFilesize: 20,
                },
            }
        },
        methods: {
            blockInstructor() {
                if (confirm(this.$t('words.are-you-sure'))) {
                    this.$inertia.delete(route('back.instructors.block', {instructor_id: this.instructor.id}));
                }
            },
            cancelEdit() {
                            this.editButton.editOption = false;
                            this.editButton.inputClass = 'mt-1 block w-full bg-gray-200';
                            this.editButton.selectInputClass = 'mt-1 block w-full border border-gray-200 bg-gray-200 py-2.5 px-4 pr-8 rounded leading-tight focus:outline-none';
                            this.editButton.text = this.$t('words.edit');
                            window.location.reload();
            },
            editInstructor() {
                if (!this.editButton.editOption) {
                    this.editButton.editOption = true;
                    this.editButton.inputClass = 'mt-1 block w-full bg-white';
                    this.editButton.selectInputClass = "mt-1 block w-full border border-gray-200 bg-white py-2.5 px-4 pr-8 rounded leading-tight focus:outline-none"
                    this.editButton.text = this.$t('words.save');
                } else {
                this.$inertia.put(route('back.instructors.update', this.instructor.id), {
                            instructor: this.instructor,
                    }).then(response => {
                            this.editButton.editOption = false;
                            this.editButton.inputClass = 'mt-1 block w-full bg-gray-200';
                            this.editButton.selectInputClass = 'mt-1 block w-full border border-gray-200 bg-gray-200 py-2.5 px-4 pr-8 rounded leading-tight focus:outline-none';
                            this.editButton.text = this.$t('words.edit');
                    }).catch(error => {
                            throw error;
                    })
                }
            },
            approveInstructor() {
                if (confirm(this.$t('words.are-you-sure'))) {
                    this.$inertia.post(route('back.instructors.approve-user', {instructor_id: this.instructor.id}));
                }
            },
            openInstructorAccount() {
                if (confirm(this.$t('words.are-you-sure'))) {
                    this.$inertia.post(route('back.instructors.create-user', {instructor_id: this.instructor.id}));
                }
            },
            sendingCsrf(file, xhr, formData) {
                xhr.setRequestHeader('X-CSRF-TOKEN', window.token ? window.token.content : '');
            },
            deleteCvFull() {
                if (confirm(this.$t('words.are-you-sure'))) {
                    this.$inertia.delete(route('back.instructors.attachments.cv-full.destroy', {instructor_id: this.instructor.id}));
                }
            },
            deleteCvSummary() {
                if (confirm(this.$t('words.are-you-sure'))) {
                    this.$inertia.delete(route('back.instructors.attachments.cv-summary.destroy', {instructor_id: this.instructor.id}));
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
