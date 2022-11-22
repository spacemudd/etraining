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
                <inertia-link class="border p-2 bg-gray-200" :href="route('trainees.courses.messages', course.id)">{{ $t('words.messages') }}</inertia-link>
                <inertia-link class="border p-2 bg-gray-200" :href="route('trainees.courses.resources', course.id)">{{ $t('words.resources') }}</inertia-link>
                <inertia-link class="border p-2 bg-red-500 text-white font-bold" :href="route('trainees.courses.grades', course.id)">{{ $t('words.grades') }}</inertia-link>
            </div>

            <div class="grid grid-cols-6 gap-6">
                <div class="col-span-6 flex items-center justify-end bg-gray-50 text-right">
                </div>

                <div class="col-span-6 sm:col-span-2">
                    <jet-label for="name_ar" :value="$t('words.course-name-ar')" />
                    <jet-input id="name_ar" type="text" class="mt-1 block w-full bg-gray-200" v-model="course.name_ar" autocomplete="off" disabled />
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
    props: ['sessions', 'course', 'quiz'],

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
