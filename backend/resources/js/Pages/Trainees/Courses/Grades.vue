<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'courses', link: route('trainees.courses.index')},
                    {title_raw: course.name_ar},
                ]"
            ></breadcrumb-container>

            <div class="flex justify-center gap-10">
                <inertia-link class="border p-2 bg-gray-200" :href="route('trainees.courses.show', course.id)">{{ $t('words.course-info') }}</inertia-link>
                <inertia-link class="border p-2 bg-gray-200" :href="route('trainees.courses.timeline', course.id)">{{ $t('words.timeline') }}</inertia-link>
                <inertia-link class="border p-2 bg-red-500 text-white font-bold" :href="route('trainees.courses.grades', course.id)">{{ $t('words.grades') }}</inertia-link>
                <inertia-link class="border p-2 bg-gray-200" :href="route('trainees.courses.messages', course.id)">{{ $t('words.messages') }}</inertia-link>
                <inertia-link class="border p-2 bg-gray-200" :href="route('trainees.courses.resources', course.id)">{{ $t('words.resources') }}</inertia-link>
            </div>

            <div class="grid grid-cols-6 gap-6">
                <div class="col-span-6 flex items-center justify-end bg-gray-50 text-right">
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
                    </div>
                </div>

            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayoutTrainee'
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
                url: route('trainees.courses.training-package', {course_id: this.course.id}),
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
    }
}
</script>

<style>
.min-container-upload {
    min-height: 168px;
}
</style>
