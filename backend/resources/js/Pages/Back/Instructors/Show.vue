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
                <div class="col-span-6 flex items-center justify-end bg-gray-50 text-right gap-6">
                    <!-- TODO: Edit instructor -->
                    <!--<inertia-link :href="route('back.instructors.edit', this.instructor.id)" class="flex items-center justify-start rounded-md px-4 py-2 bg-gray-200 hover:bg-gray-300 text-right">-->
                    <!--    {{ $t('words.edit') }}-->
                    <!--</inertia-link>-->
                    <button v-if="!instructor.user_id" @click="openInstructorAccount" class="flex items-center justify-start rounded-md px-4 py-2 bg-yellow-200 hover:bg-yellow-300 text-right">
                        {{ $t('words.open-an-account') }}
                    </button>

                    <button v-if="instructor.is_pending_approval" @click="approveInstructor" class="flex items-center justify-start rounded-md px-4 py-2 bg-yellow-200 hover:bg-yellow-300 text-right">
                        {{ $t('words.approve-instructor') }}
                    </button>
                </div>

                <div class="col-span-6 sm:col-span-2">
                    <jet-label for="name" :value="$t('words.name')" />
                    <jet-input id="name" type="text" class="mt-1 block w-full bg-gray-200" v-model="this.instructor.name" autocomplete="off" disabled />
                </div>

                <div class="col-span-6 sm:col-span-2">
                    <jet-label for="phone" :value="$t('words.phone')" />
                    <jet-input id="phone" type="text" class="mt-1 block w-full bg-gray-200" v-model="this.instructor.phone" autocomplete="off" disabled />
                </div>

                <div class="col-span-6 sm:col-span-2">
                    <jet-label for="identity_number" :value="$t('words.identity_number')" />
                    <jet-input id="identity_number" type="text" class="mt-1 block w-full bg-gray-200" v-model="this.instructor.identity_number" disabled />
                </div>

                <div class="col-span-6 sm:col-span-2">
                    <jet-label for="email" :value="$t('words.email')" />
                    <jet-input id="email" type="text" class="mt-1 block w-full bg-gray-200" v-model="this.instructor.email" disabled />
                </div>

                <div class="col-span-6 sm:col-span-2">
                    <jet-label for="city_id" :value="$t('words.city')" />
                    <jet-input id="city" type="text" class="mt-1 block w-full bg-gray-200" :value="this.instructor.city ? this.instructor.city.name_ar : ''" disabled />
                </div>

                <div class="col-span-6 sm:col-span-2">
                    <jet-label for="twitter_link" :value="$t('words.twitter_link')" />
                    <jet-input id="twitter_link" type="text" class="mt-1 block w-full bg-gray-200" v-model="this.instructor.twitter_link" disabled />
                </div>

                <div class="col-span-6 sm:col-span-6">
                    <jet-label for="name" :value="$t('words.status')" />
                    <p>
                        <span v-if="instructor.is_pending_uploading_files" class="text-sm inline-block mt-2 p-1 px-2 bg-red-300 rounded-lg">
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
        props: ['sessions', 'instructor'],

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
