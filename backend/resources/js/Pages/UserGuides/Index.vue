<template>
    <app-layout>
        <div class="container px-6 mx-auto grid">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-5">
                <div class="col text-center h-20" v-if="is_trainee">
                    <a target="_blank"
                       rel="noopener noreferrer"
                       href="/GuideTrainee.pdf"
                       class="bg-red-500 px-2 py-1 rounded mx-auto text-white hover:bg-red-700">
                        دليل استخدام المنصة للمتدربين
                    </a>
                </div>
                <div class="col text-center h-20" v-else>
                    <a target="_blank"
                       rel="noopener noreferrer"
                       href="/InstructorGuide.pdf"
                       class="bg-red-500 px-2 py-1 rounded mx-auto text-white hover:bg-red-700">
                        دليل استخدام المنصة للمدربات
                    </a>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayoutTrainee'
import Welcome from '@/Jetstream/Welcome'
import LanguageSelector from "@/Shared/LanguageSelector";
import HeaderCard from "@/Components/HeaderCard";
import JetLabel from '@/Jetstream/Label';
import JetInput from '@/Jetstream/Input';
import JetInputError from '@/Jetstream/InputError';
import JetTextarea from '@/Jetstream/Textarea';
import JetButton from '@/Jetstream/Button';

export default {
    components: {
        AppLayout,
        Welcome,
        LanguageSelector,
        HeaderCard,
        JetLabel,
        JetInput,
        JetInputError,
        JetTextarea,
        JetButton,
    },
    props: ['is_trainee'],
    data() {
        return {
            createComplaintForm: this.$inertia.form({
                course_name: '',
                course_instructor: '',
                message: '',
            }, {
                bag: 'createComplaintForm',
            }),
        }
    },
    mounted() {

    },
    methods: {
        submitForm() {
            this.createComplaintForm.post(route('complaints.store'));
        }
    },
    beforeDestroy() {

    }
}
</script>
