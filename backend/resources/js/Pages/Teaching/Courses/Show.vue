<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'courses', link: route('teaching.courses.index')},
                    {title_raw: course.name_ar},
                ]"
            ></breadcrumb-container>

            <div class="grid grid-cols-6 gap-6">
                <div class="col-span-6 flex items-center justify-end bg-gray-50 text-right">
                    <inertia-link :href="route('teaching.courses.edit', course.id)" class="flex items-center justify-start rounded-md px-4 py-2 bg-gray-200 hover:bg-gray-300 text-right">
                        {{ $t('words.edit') }}
                    </inertia-link>
                </div>

                <div class="col-span-6 sm:col-span-2">
                    <jet-label for="name_ar" :value="$t('words.course-name-ar')" />
                    <jet-input id="name_ar" type="text" class="mt-1 block w-full bg-gray-200" v-model="course.name_ar" autocomplete="off" disabled />
                </div>

                <div class="col-span-6 sm:col-span-2">
                    <jet-label for="name_en" :value="$t('words.course-name-en')" />
                    <jet-input id="name_en" type="text" class="mt-1 block w-full bg-gray-200" v-model="course.name_en" autocomplete="off" disabled />
                </div>

                <div class="col-span-6 sm:col-span-2">
                    <jet-label for="course-approval-code" :value="$t('words.course-approval-code')" />
                    <jet-input id="course-approval-code" type="text" class="mt-1 block w-full bg-gray-200" v-model="course.approval_code" autocomplete="off" disabled />
                </div>

                <div class="col-span-6 sm:col-span-2">
                    <jet-label for="days_duration" :value="$t('words.course-duration-days')" />
                    <jet-input id="days_duration" type="text" class="mt-1 block w-full bg-gray-200" v-model="course.days_duration" autocomplete="off" disabled />
                </div>

                <div class="col-span-6 sm:col-span-2">
                    <jet-label for="hours_duration" :value="$t('words.course-duration-hours')" />
                    <jet-input id="hours_duration" type="text" class="mt-1 block w-full bg-gray-200" v-model="course.hours_duration" autocomplete="off" disabled />
                </div>

                <div class="col-span-6 sm:col-span-2">
                    <jet-label for="description" :value="$t('words.description')" />
                    <jet-textarea id="description" type="text" class="mt-1 block w-full bg-gray-200" v-model="course.description" autocomplete="off" disabled />
                </div>

                <div class="col-span-6 sm:col-span-6">
                    <jet-label for="name" :value="$t('words.status')" />
                    <p>
                        <span v-if="course.is_pending_approval" class="text-sm inline-block mt-2 p-1 px-2 bg-yellow-200 rounded-lg">
                            {{ $t('words.pending-approval') }}
                        </span>

                        <span v-if="course.is_approved" class="text-sm inline-block mt-2 p-1 px-2 bg-green-300 rounded-lg">
                            {{ $t('words.approved') }}
                        </span>
                    </p>
                </div>

            </div>

            <jet-section-border></jet-section-border>

            <div class="grid grid-cols-1 md:grid-cols-6 gap-6 my-2">
                <div class="md:col-span-5 lg:col-span-5 sm:col-span-3">
                    <div class="px-4 sm:px-0">
                        <h3 class="text-lg font-medium text-gray-900">
                            {{ $t('words.documents') }}
                        </h3>

                        <p class="mt-1 text-sm text-gray-600">
                            {{ $t('words.documents-help') }}
                        </p>
                    </div>
                </div>

                <div class="md:col-span-6 lg:col-span-1 sm:col-span-3">
                    <jet-label :value="$t('words.training-package')" class="mb-2" />

                    <div class="bg-white border-2 rounder-lg flex flex-col justify-center items-center min-container-upload" v-if="course.training_package_url">
                        <a class="bg-gray-700 text-white font-semibold p-2 text-center w-1/2 rounded my-1" target="_blank" :href="course.training_package_url">{{ $t('words.download') }}</a>
                        <button class="bg-red-500 text-white font-semibold p-2 text-center w-1/2 rounded my-1" @click="deleteTrainingPackage">{{ $t('words.delete') }}</button>
                    </div>
                    <vue-dropzone v-else
                                  id="dropzoneIdentity"
                                  @vdropzone-sending="sendingCsrf"
                                  :options="dropzoneOptionsTrainingPackage"
                    ></vue-dropzone>
                </div>
            </div>

            <jet-section-border></jet-section-border>

            <div class="grid grid-cols-1 md:grid-cols-6 gap-6 my-2">
                <div class="md:col-span-4 lg:col-span-3 sm:col-span-3">
                    <div class="px-4 sm:px-0">
                        <h3 class="text-lg font-medium text-gray-900">
                            {{ $t('words.course-schedule') }}
                        </h3>

                        <p class="mt-1 text-sm text-gray-600">
                            {{ $t('words.course-schedule-help') }}
                        </p>
                    </div>
                </div>

                <div class="md:col-span-3 sm:col-span-1">
                    <course-batches-pagination :course-id="course.id" />
                </div>
            </div>

        </div>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayoutInstructor'
import JetSectionBorder from '@/Jetstream/SectionBorder'
import Breadcrumb from "@/Components/Breadcrumb";
import JetDialogModal from '@/Jetstream/DialogModal'
import JetInput from '@/Jetstream/Input'
import JetInputError from '@/Jetstream/InputError'
import JetActionMessage from '@/Jetstream/ActionMessage';
import JetButton from '@/Jetstream/Button';
import JetFormSection from '@/Jetstream/FormSection';
import JetLabel from '@/Jetstream/Label';
import JetTextarea from '@/Jetstream/Textarea';
import CompanyContractsPagination from "@/Components/CompanyContractsPagination";
import BreadcrumbContainer from "@/Components/BreadcrumbContainer";
import VueDropzone from 'vue2-dropzone'
import 'vue2-dropzone/dist/vue2Dropzone.min.css'
import CourseBatchesPagination from "@/Components/CourseBatchesPagination";

export default {
    props: ['sessions', 'course'],

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
        CompanyContractsPagination,
        BreadcrumbContainer,
        VueDropzone,
        CourseBatchesPagination,
    },
    data() {
        return {
            dropzoneOptionsTrainingPackage: {
                destroyDropzone: false,
                url: route('teaching.courses.training-package', {course_id: this.course.id}),
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
        deleteTrainingPackage() {
            if (confirm(this.$t('words.are-you-sure'))) {
                this.$inertia.delete(route('teaching.courses.training-package.destroy', {course_id: this.course.id}));
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
