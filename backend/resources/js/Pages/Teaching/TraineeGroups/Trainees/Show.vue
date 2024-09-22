<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'trainees', link: route('teaching.trainee-groups.index')},
                    {title_raw: traineeGroup.name},
                    {title_raw: trainee.name },
                ]"
            ></breadcrumb-container>

            <div class="grid grid-cols-6 gap-6">
                <div class="col-span-6 flex items-center justify-end bg-gray-50 text-right gap-6">
                    <new-private-message-trainee :trainee="trainee" :traineeGroup="traineeGroup"></new-private-message-trainee>
                </div>

                <div class="col-span-6 sm:col-span-2">
                    <jet-label for="name" :value="$t('words.name')" />
                    <jet-input id="name" type="text" class="mt-1 block w-full bg-gray-200" v-model="this.trainee.name" autocomplete="off" disabled />
                </div>

                <div class="col-span-6 sm:col-span-2">
                    <jet-label for="email" :value="$t('words.email')" />
                    <jet-input id="email" type="text" class="mt-1 block w-full bg-gray-200" :value="this.trainee.email" disabled />
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayoutInstructor'
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
    import NewPrivateMessageTrainee from "@/Components/NewPrivateMessageTrainee";

    export default {
        props: ['sessions', 'trainee', 'traineeGroup'],

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
            NewPrivateMessageTrainee,
        },
        data() {
            return {
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
