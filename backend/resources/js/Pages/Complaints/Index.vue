<template>
    <app-layout>
        <div class="container px-6 mx-auto grid">
            <div class="grid grid-cols-2 gap-6 mt-5">
                <div class="col">
                    <form @submit.prevent="submitForm">
                        <jet-label for="course_name" :value="$t('words.course')" />
                        <jet-input id="course_name" type="text" class="mt-1 block w-full" v-model="createComplaintForm.course_name" autocomplete="off" autofocus="on" />

                        <div class="mt-5">
                        <jet-label for="course_instructor" :value="$t('words.instructor')" />
                        <jet-input id="course_instructor" type="text" class="mt-1 block w-full" v-model="createComplaintForm.course_instructor" autocomplete="off" />
                        </div>

                        <div class="mt-5">
                            <jet-label for="message" :value="$t('words.message')" />
                            <jet-textarea id="message" type="textarea" class="mt-1 block w-full" v-model="createComplaintForm.message" />
                        </div>

                        <jet-button  class="mt-5" :class="{ 'opacity-25': createComplaintForm.processing }" :disabled="createComplaintForm.processing">
                            {{ $t('words.send') }}
                        </jet-button>
                    </form>
                </div>
                <div class="col">
                    <div class="px-20">
                        <img src="/img/ptc_complaints.jpg" class="shadow-lg rounded-lg" alt="PTCComplaints">
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
