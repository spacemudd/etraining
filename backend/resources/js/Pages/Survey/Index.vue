<template>
    <app-layout>
        <div class="container px-6 mx-auto grid">
            <div class="flex content-center mt-20 text-center w-full" v-if="">
                <a target="_blank" rel="noopener noreferrer" href="https://forms.gle/wZtJ4n3kLRv3AVfH7" class="bg-red-500 px-2 py-1 rounded mx-auto text-white hover:bg-red-700">قياس رضا المتدرب</a>
            </div>
            <div class="flex content-center mt-20 text-center w-full">
                <a target="_blank" rel="noopener noreferrer" href="https://forms.gle/EqVJ6e29NHfQbQDY7" class="bg-red-500 px-2 py-1 rounded mx-auto text-white hover:bg-red-700">قياس رضا المدرب</a>
            </div>

            <div class="mt-20 bg-white shadow-lg rounded-lg p-5 text-center flex content-center">
                <img src="/img/ptc_survey_new.png" class="mx-auto" style="max-width:600px;">
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
    props: [
        'surveyLink',
    ],
    data() {
        return {

        }
    },
    mounted() {

    },
    methods: {

    },
    beforeDestroy() {

    }
}
</script>
