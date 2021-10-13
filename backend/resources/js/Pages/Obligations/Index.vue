<template>
    <app-layout>
        <div class="container px-6 mx-auto grid">
            <div class="grid grid-cols-1 gap-6 mt-5">
                <div class="col text-center">
                    <div class="px-20 text-center mx-auto">
                        <img src="/img/obligations1.jpg" class="shadow-lg rounded-lg w-1/2 mx-auto" alt="PTCObligations">
                    </div>
                </div>
            </div>

            <div class="flex justify-center mt-5">
                    <a target="_blank"
                       rel="noopener noreferrer"
                       href="/obligations1.pdf"
                       class="bg-red-500 px-2 py-1 rounded mx-2 text-white hover:bg-red-700">
                        اقرار الالتزام
                    </a>
                    <a target="_blank"
                       rel="noopener noreferrer"
                       href="/obligations2.pdf"
                       class="bg-red-500 px-2 py-1 rounded mx-2 text-white hover:bg-red-700">
                        التعهد
                    </a>
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
