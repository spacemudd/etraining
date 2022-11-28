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

            <form  v-for="quizzes in course.quizzes"
                   :key="course.quizzes.id">
                <div class="grid md:grid-cols-4 grid-cols-1 gap-6">
                    <div class="col-span-1 p-5 transition-all duration-500 ease-in-out hover:bg-gray-200">
                        <p class="text-2xl">{{ $t('words.choose') }}:</p>
<!--                        <div class="payment-options mt-2">-->
<!--                            <input type="radio" name="quizzes">-->
<!--                            <label>{{ quizzes.course_id }}</label>-->
<!--                        </div>-->
<!--                        <div class="payment-options">-->
<!--                            <input type="radio" name="quizzes">-->
<!--                            <label>{{ quizzes.name_ar }}</label>-->
<!--                        </div>-->
                        <div class="">
                            <input type="radio">
                            <label v-if="questions.id === 1">{{ quizzes.questions.id }}</label>
                        </div>
                    </div>
                </div>
            </form>
            <div class="">
                <input type="radio">
                <label v-if="questions.id === 1">{{ questions.id }}</label>
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
    props: ['sessions', 'course', 'quizzes', 'questions'],

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





<!--$array = [1101281010, 1090693209,1088712607,1095806178,1085015913,1097113508,1083488401,1080941857,1086804851,1091141299,1127705968,1110190764,1112078181,1113163131,1127957320,1091371672,1101291035,1096900574,1087346407,1095769830,1085563060,1064499716,1108285832,1096904550,1095349039,1088431505,1013817919,1099202333,1105392870,1097095580,1090907377,1068924826,1087154322,1003419791,1075686947,1093745550,1107361881,1085544748,1075050243,1114774175,1094497169,1098223207,1104369390,1073213694,1106245432,1125774354,1113633372,1040941559,1127213575,1090079342,1068683166,1091764454,1031230772,1114885427,1058386895,1089624413,1128492467,1098744376,1086996699,1127705943,1081529602,1058042274,1065788919,1099648840,1099064022,1086796214,1072186719,1094473178,1116264415,1114294992,1090015767,1129518666,1100255072,1128944939,1067926848,1097317554,1101374211,1043000429,1075123339,1074792902,1080264268,1081247429,1109056984,1097662819,1126127685,1082362144,1035458130,1098882044,1062519994,1014383341,1087466833,1074014174,1024748061,1080453622,1107320457,1089725905,1111276174,1052968284,1105764201];-->
