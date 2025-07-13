<template>
    <app-layout>
        <div class="container px-6 mx-auto grid">
            <div class="grid grid-cols-1 gap-6 mt-5">
                <div class="col text-center">
                    <div class="px-20 text-center mx-auto">
<!--                        <img src="https://i.ibb.co/0Br7Fpx/new-suggestions.jpg" class="shadow-lg rounded-lg w-1/2 mx-auto" alt="PTCComplaints">-->
                    </div>
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
