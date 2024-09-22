<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'trainees', link: route('teaching.trainee-groups.index')},
                    {title_raw: traineeGroup.name},
                    // {title_raw: $t('words.send-announcement')},
                ]"
            ></breadcrumb-container>

            <div class="mt-4">
                <jet-form-section @submitted="sendAnnouncement">
                    <template #title>
                        {{ $t('words.new-announcement') }}
                    </template>

                    <template #description>
                        {{ $t('words.send-announcement-to-all-trainees') }}
                    </template>

                    <template #form>
                        <div class="col-span-6 sm:col-span-2">
                            <div class="col-span-6 sm:col-span-4">
                                <jet-label for="course_id" :value="$t('words.course-name')" />

                                <div class="relative">
                                    <select class="block appearance-none w-full border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                            v-model="form.course_id"
                                            id="course_id">
                                        <option></option>
                                        <option v-for="course in courses" :key="course.id" :value="course.id">{{ course.name_ar + ' - (' + course.name_en + ')' }}</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                                    </div>
                                </div>

                                <jet-input-error :message="form.error('course_id')" class="mt-2" />
                            </div>
                        </div>

                        <div class="col-span-6 sm:col-span-4">
                            <jet-label for="message" :value="$t('words.message')" />
                            <jet-textarea id="message" class="mt-1 block w-full" v-model="form.message" autocomplete="off" />
                        </div>
                    </template>

                    <template #actions>
                        <jet-action-message :on="form.recentlySuccessful" class="mr-3">
                            {{ $t('words.saved-successfully') }}
                        </jet-action-message>

                        <inertia-link :href="route('teaching.trainee-groups.index')"
                                      class="flex items-center justify-start rtl:ml-4 ltr:mr-4 rounded-md px-4 py-2 bg-white hover:bg-gray-300 text-right">
                            {{ $t('words.cancel') }}
                        </inertia-link>

                        <jet-button :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            {{ $t('words.send-announcement') }}
                        </jet-button>
                    </template>
                </jet-form-section>
            </div>
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayoutInstructor'
    import JetSectionBorder from '@/Jetstream/SectionBorder'
    import JetDialogModal from '@/Jetstream/DialogModal'
    import JetInput from '@/Jetstream/Input'
    import JetInputError from '@/Jetstream/InputError'
    import JetActionMessage from '@/Jetstream/ActionMessage';
    import JetButton from '@/Jetstream/Button';
    import JetFormSection from '@/Jetstream/FormSection';
    import JetLabel from '@/Jetstream/Label';
    import JetTextarea from '@/Jetstream/Textarea';
    import BreadcrumbContainer from "@/Components/BreadcrumbContainer";

    export default {
        props: ['courses', 'traineeGroup'],

        components: {
            AppLayout,
            JetSectionBorder,
            JetDialogModal,
            JetInput,
            JetInputError,
            JetActionMessage,
            JetButton,
            JetFormSection,
            JetLabel,
            JetTextarea,
            BreadcrumbContainer,
        },
        data() {
            return {
                form: this.$inertia.form({
                    course_id: null,
                    training_group_id: null,
                    message: '',
                }, {
                    bag: 'sendAnnouncement',
                })
            }
        },
        mounted() {
            this.form.training_group_id = this.traineeGroup.id;
        },
        methods: {
            sendAnnouncement() {
                this.form.post(route('teaching.trainee-groups.announcements.send', this.traineeGroup.id), {
                    preserveScroll: true
                });
            },
        }
    }
</script>
