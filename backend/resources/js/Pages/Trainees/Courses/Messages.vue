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
                <inertia-link class="border p-2 bg-red-500 text-white font-bold" :href="route('trainees.courses.messages', course.id)">{{ $t('words.messages') }}</inertia-link>
                <inertia-link class="border p-2 bg-gray-200" :href="route('trainees.courses.resources', course.id)">{{ $t('words.resources') }}</inertia-link>
                <inertia-link class="border p-2 bg-gray-200" :href="route('trainees.courses.grades', course.id)">{{ $t('words.grades') }}</inertia-link>
            </div>
            <jet-section-border></jet-section-border>

            <div class="grid grid-cols-3 gap-6 mt-4 pr-12">
                <div class="inline-flex">
                    <svg width="40" height="40" class="mx-0.5">
                        <image class=inline xlink:href="https://img.icons8.com/material-outlined/512/whatsapp.png" src="https://img.icons8.com/material-outlined/512/whatsapp.png" width="25" height="25"/>
                    </svg>
                    <h6 class="pr-2 text-xl font-bold">للتواصل واتساب</h6>
                </div>
                <div class="inline-flex">
                    <svg width="40" height="40" class="mx-0.5">
                        <image class=inline xlink:href="https://img.icons8.com/fluency-systems-regular/512/filled-message.png" src="https://img.icons8.com/fluency-systems-regular/512/filled-message.png" width="25" height="25"/>
                    </svg>
                    <h6 class="pr-2 text-xl font-bold">البريد الالكتروني</h6>
                </div>
                <div class="inline-flex">
                    <svg width="40" height="40" class="mx-0.5">
                        <image class=inline xlink:href="https://img.icons8.com/material/512/end-call-male.png" src="https://img.icons8.com/material/512/end-call-male.png" width="25" height="25"/>
                    </svg>
                    <h6 class="pr-2 text-xl font-bold">رقم المدرب</h6>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-6 pr-12">
                <div class="inline-flex">
                    <h6 class="pr-12 text-l">966553139979</h6>
                </div>
                <div class="inline-flex">
                    <h6 class="pr-12 text-l">info@ptc-ksa.com</h6>
                </div>
                <div class="inline-flex" v-if="course.instructor_id">
                    <h6 class="pr-12 text-l">{{ course.instructor.phone }}</h6>
                </div>
            </div>

            <jet-section-border></jet-section-border>
            <div class="" v-if="course.id === 'abedda89-eb23-42ec-9a03-a19097523eb5'">
                <iframe src="/file/1/pdf/timeline.pdf" width="100%" height="800"></iframe>
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
        var app = new Vue({
            el: '#app',
            data: {
                isHidden: false
            }
        })
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
