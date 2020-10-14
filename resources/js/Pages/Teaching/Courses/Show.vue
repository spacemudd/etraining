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
                    <button @click="startCourse" class="flex items-center justify-start rounded-md px-4 py-2 bg-gray-200 hover:bg-gray-300 text-right">
                        {{ $t('words.start-course') }}
                    </button>
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
    import CourseBatchesPagination from "@/Components/CourseBatchesPagination";
    import {ZoomMtg} from '@zoomus/websdk';

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
            CompanyContractsPagination,
            BreadcrumbContainer,
            VueDropzone,
            CourseBatchesPagination,
        },
        data() {
            return {
                dropzoneOptionsTrainingPackage: {
                    destroyDropzone: false,
                    url: route('back.courses.training-package', {course_id: this.course.id}),
                    dictDefaultMessage: "<ion-icon name='cloud-upload-outline' class='text-red-500' size='large'></ion-icon><br/> "+this.$t('words.upload-files-here'),
                    thumbnailWidth: 150,
                    maxFilesize: 20,
                },
            }
        },
        mounted() {
            console.log('checkSystemRequirements');
            console.log(JSON.stringify(ZoomMtg.checkSystemRequirements()));
            ZoomMtg.preLoadWasm();
            ZoomMtg.prepareJssdk()
        },
        methods: {
            sendingCsrf(file, xhr, formData) {
                xhr.setRequestHeader('X-CSRF-TOKEN', window.token ? window.token.content : '');
            },
            deleteTrainingPackage() {
                if (confirm(this.$t('words.are-you-sure'))) {
                    this.$inertia.delete(route('back.courses.training-package.destroy', {course_id: this.course.id}));
                }
            },
            startCourse() {

            },
        }
    }
</script>

<style>
    .min-container-upload {
        min-height: 168px;
    }
</style>
