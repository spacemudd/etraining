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

            <jet-section-border></jet-section-border>

            <h3></h3>

            <div v-if="course.id === 'abedda89-eb23-42ec-9a03-a19097523eb5'">
                <form>
                    <div class="grid md:grid-cols-4 grid-cols-1 gap-6">
                        <div class="col-span-1 p-5 transition-all duration-500 ease-in-out hover:bg-gray-200">
                            <div  v-for="question in questions"
                                  :key="questions.id"
                                  v-if="question.course_id === course.id">
                                <p class="text-2xl">{{ question.description }}</p>
                                <div class="payment-options mt-2" v-for="answer in answers"
                                     :key="answers.id"
                                     v-if="answer.question_id === question.id">
                                    <div>
                                        <input type="radio" name="answers.id">
                                        <label >{{ answer.value }}</label>
                                    </div>
                                </div>
                                <br>
                                <br>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">إرسال</button>
                </form>
            </div>

            <jet-section-border></jet-section-border>
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
    props: ['sessions', 'course', 'quizzes', 'questions', 'answers'],

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
