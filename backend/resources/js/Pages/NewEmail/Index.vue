<template>
    <app-layout>
        <div class="container px-6 mx-auto grid">
                <breadcrumb-container
                    :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'new_email', link: route('new_email.index')},
                ]"
                ></breadcrumb-container>
                <div class="grid grid-cols-2 gap-6 mt-5">
                <div class="col">
                    <form @submit.prevent="submitForm">
                        <jet-label :value="$t('words.applicant')" />
                        <jet-input type="text" class="mt-1 block w-full" v-model="form.applicant" autocomplete="off" autofocus="on" />

                        <div class="mt-5">
                            <jet-label :value="$t('words.personal_email')" />
                            <jet-input type="email" class="mt-1 block w-full" v-model="form.personal_email" autocomplete="off" />
                        </div>

                        <div class="mt-5">
                            <jet-label :value="$t('words.phone')" />
                            <jet-input type="text" class="mt-1 block w-full" v-model="form.phone" autocomplete="off" />
                        </div>

                        <div class="mt-5">
                            <jet-label :value="$t('words.job_title')" />
                            <jet-input type="text" class="mt-1 block w-full" v-model="form.job_title" autocomplete="off" />
                        </div>

                        <div class="mt-5">
                            <jet-label :value="$t('words.manager_name')" />
                            <jet-input type="text" class="mt-1 block w-full" v-model="form.manager_name" autocomplete="off" />
                        </div>

                        <div class="mt-5">
                            <jet-label :value="$t('words.manager_email')" />
                            <jet-input type="email" class="mt-1 block w-full" v-model="form.manager_email" autocomplete="off" />
                        </div>

                        <div class="mt-5">
                            <jet-label :value="$t('words.new_email')" />
                            <jet-input type="email" class="mt-1 block w-full" v-model="form.new_email" autocomplete="off" />
                        </div>

                        <jet-button  class="mt-5" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            {{ $t('words.send') }}
                        </jet-button>
                    </form>
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
import BreadcrumbContainer from "@/Components/BreadcrumbContainer";


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
        BreadcrumbContainer,
    },
    data() {
        return {
            form: this.$inertia.form({
                applicant: '',
                personal_email: '',
                phone: '',
                job_title: '',
                manager_name: '',
                manager_email: '',
                new_email: '',
            }),
        }
    },
    mounted() {

    },
    methods: {
        submitForm() {
            this.form.post(route('new_email.store'));
        }
    },
}
</script>
