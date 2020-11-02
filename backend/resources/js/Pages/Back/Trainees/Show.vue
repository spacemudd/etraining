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
                <div class="col-span-6 flex items-center justify-end bg-gray-50 text-right gap-6">
                    <inertia-link :href="route('back.trainees.edit', this.trainee.id)" class="flex items-center justify-start rounded-md px-4 py-2 bg-gray-200 hover:bg-gray-300 text-right">
                        {{ $t('words.edit') }}
                    </inertia-link>
                    <button v-if="!trainee.user_id"
                            @click="openTraineeAccount"
                            class="flex items-center justify-start rounded-md px-4 py-2 bg-yellow-200 hover:bg-yellow-300 text-right">
                        {{ $t('words.open-an-account') }}
                    </button>
                </div>

                <div class="col-span-6 sm:col-span-2">
                    <jet-label for="name" :value="$t('words.name')" />
                    <jet-input id="name" type="text" class="mt-1 block w-full bg-gray-200" v-model="this.trainee.name" autocomplete="off" disabled />
                </div>

                <div class="col-span-6 sm:col-span-2">
                    <jet-label for="identity_number" :value="$t('words.identity_number')" />
                    <jet-input id="identity_number" type="text" class="mt-1 block w-full bg-gray-200" v-model="this.trainee.identity_number" disabled />
                </div>

                <div class="col-span-6 sm:col-span-2">
                    <jet-label for="birthday" :value="$t('words.birthday')" />
                    <jet-input id="birthday" type="date" class="mt-1 block w-full bg-gray-200" v-model="this.trainee.birthday" disabled />
                </div>

                <div class="col-span-6 sm:col-span-2">
                    <jet-label for="phone" :value="$t('words.phone')" />
                    <jet-input id="phone" type="text" class="mt-1 block w-full bg-gray-200" v-model="this.trainee.phone" placeholder="9665XXXXXXXX" disabled />
                </div>

                <div class="col-span-6 sm:col-span-2">
                    <jet-label for="phone_additional" :value="$t('words.phone_additional')" />
                    <jet-input id="phone_additional" type="text" class="mt-1 block w-full bg-gray-200" v-model="this.trainee.phone_additional" disabled />
                </div>

                <div class="col-span-6 sm:col-span-2">
                    <jet-label for="email" :value="$t('words.email')" />
                    <jet-input id="email" type="text" class="mt-1 block w-full bg-gray-200" :value="this.trainee.email" disabled />
                </div>

                <div class="col-span-6 sm:col-span-2">
                    <jet-label for="educational_level" :value="$t('words.educational_level')" />
                    <jet-input id="educational_level" type="text" class="mt-1 block w-full bg-gray-200" :value="this.trainee.educational_level ? this.trainee.educational_level.name_ar : ''" disabled />
                </div>

                <div class="col-span-6 sm:col-span-2">
                    <jet-label for="city_id" :value="$t('words.city')" />
                    <jet-input id="city" type="text" class="mt-1 block w-full bg-gray-200" :value="this.trainee.city ? this.trainee.city.name_ar : ''" disabled />
                </div>

                <div class="col-span-6 sm:col-span-1">
                    <jet-label for="marital_status" :value="$t('words.marital_status')" />
                    <jet-input id="marital_status" type="text" class="mt-1 block w-full bg-gray-200" :value="this.trainee.marital_status ? this.trainee.marital_status.name_ar : ''" disabled />
                </div>

                <div class="col-span-6 sm:col-span-1">
                    <jet-label for="children_count" :value="$t('words.children_count')" />
                    <jet-input id="children_count" type="text" class="mt-1 block w-full bg-gray-200" v-model="this.trainee.children_count" disabled />
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

    export default {
        props: ['sessions', 'trainee'],

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
        methods: {
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
