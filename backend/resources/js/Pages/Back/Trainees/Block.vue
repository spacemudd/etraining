<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'trainees', link: route('back.trainees.index')},
                    {title_raw: trainee.name},
                ]"
            ></breadcrumb-container>

            <div class="grid grid-cols-6 gap-6">
                <h1>
                            {{ $t('words.block-trainee') }}
                </h1>

                <div class="col-span-6 sm:col-span-2">
                    <jet-textarea id="description" type="text" class="block w-full bg-white" v-model="deleted_remark" autocomplete="off" requried/>
                    <button @click="blockTrainee" class="mt-5 items-center justify-start float-left rounded-md px-4 py-2 bg-red-300 hover:bg-red-400">
                        {{ $t('words.block-trainee') }}
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
    import JetButton from '@/Jetstream/Button';
    import JetFormSection from '@/Jetstream/FormSection';
    import JetTextarea from '@/Jetstream/Textarea';
    import JetLabel from '@/Jetstream/Label';
    import BreadcrumbContainer from "@/Components/BreadcrumbContainer";

    export default {
        props: ['sessions', 'trainee'],

        components: {
            AppLayout,
            JetSectionBorder,
            Breadcrumb,
            JetDialogModal,
            JetInput,
            JetButton,
            JetFormSection,
            JetLabel,
            BreadcrumbContainer,
            JetTextarea,
        },
        data() {
            return {
                deleted_remark: null,
            }
        },
        methods: {
            blockTrainee() {
                if (confirm(this.$t('words.are-you-sure'))) {
                    this.$inertia.post(route('back.trainees.block', {trainee_id: this.trainee.id}), {
                        deleted_remark: this.deleted_remark,
                    });
                }
            },
        }
    }
</script>
