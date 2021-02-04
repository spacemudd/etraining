<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'courses', link: route('back.courses.index')},
                    {title: 'new'},
                ]"
            ></breadcrumb-container>

            <div class="mt-4">
                <jet-form-section @submitted="createCourse">
                    <template #title>
                        {{ $t('words.open-new-course-file') }}
                    </template>

                    <template #description>
                        {{ $t('words.open-new-course-file-description') }}
                    </template>

                    <template #form>
                        <div class="col-span-6 sm:col-span-2">
                            <jet-label for="name_ar" :value="$t('words.course-name-ar')" />
                            <jet-input id="name_ar" type="text" class="mt-1 block w-full" v-model="form.name_ar" autocomplete="off" autofocus />
                        </div>

                        <div class="col-span-6 sm:col-span-2">
                            <jet-label for="name_en" :value="$t('words.course-name-en')" />
                            <jet-input id="name_en" type="text" class="mt-1 block w-full" v-model="form.name_en" autocomplete="off" />
                        </div>

                        <div class="col-span-6 sm:col-span-4">
                            <jet-label for="instructor_id" :value="$t('words.instructor')" />

                            <div class="relative">
                                <select class="block appearance-none w-full border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                        v-model="form.instructor_id"
                                        id="instructor_id">
                                    <option v-for="instructor in instructors"
                                            :key="instructor.id"
                                            :value="instructor.id">
                                        {{ instructor.name }}
                                    </option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                                </div>
                            </div>

                            <jet-input-error :message="form.error('instructor_id')" class="mt-2" />
                        </div>

                        <div class="col-span-6 sm:col-span-4">
                            <jet-label for="description" :value="$t('words.description')" />
                            <jet-textarea id="description" class="mt-1 block w-full" v-model="form.description" autocomplete="off" />
                        </div>

                        <div class="col-span-6 sm:col-span-2">
                            <jet-label for="course-approval-code" :value="$t('words.course-approval-code')" />
                            <jet-input id="course-approval-code" type="text" class="mt-1 block w-full" v-model="form.approval_code" autocomplete="off" />
                        </div>

                        <div class="col-span-6 sm:col-span-2">
                            <jet-label for="days_duration" :value="$t('words.course-duration-days')" />
                            <jet-input id="days_duration" type="text" class="mt-1 block w-full" v-model="form.days_duration" autocomplete="off" />
                        </div>

                        <div class="col-span-6 sm:col-span-2">
                            <jet-label for="hours_duration" :value="$t('words.course-duration-hours')" />
                            <jet-input id="hours_duration" type="text" class="mt-1 block w-full" v-model="form.hours_duration" autocomplete="off" />
                        </div>

                        <div class="col-span-6 sm:col-span-2">
                            <jet-label for="training-bag" :value="$t('words.training-package')" />
                            <vue-dropzone ref="dropZoneContainer"
                                          class="mt-1"
                                          id="dropzone"
                                          @vdropzone-file-added="fileAdded"
                                          :options="dropzoneOptions"
                            ></vue-dropzone>
                        </div>
                    </template>

                    <template #actions>
                        <jet-action-message :on="form.recentlySuccessful" class="mr-3">
                            {{ $t('words.saved-successfully') }}
                        </jet-action-message>

                        <inertia-link href="/back/courses" class="flex items-center justify-start rtl:ml-4 ltr:mr-4 rounded-md px-4 py-2 bg-white hover:bg-gray-300 text-right">
                            {{ $t('words.cancel') }}
                        </inertia-link>

                        <jet-button :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            {{ $t('words.save') }}
                        </jet-button>
                    </template>
                </jet-form-section>
            </div>
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout'
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
    import VueDropzone from "vue2-dropzone";
    import 'vue2-dropzone/dist/vue2Dropzone.min.css'

    export default {
        props: ['sessions', 'instructors'],

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
            VueDropzone,
        },
        data() {
            return {
                dropzoneOptions: {
                    addRemoveLinks: true,
                    destroyDropzone: false,
                    autoProcessQueue: false,
                    manuallyAddFile: true,
                    url: 'https://getShafiq.com', // Just required to initiate DropZone.
                    dictDefaultMessage: "<ion-icon name='cloud-upload-outline' class='text-red-500' size='large'></ion-icon><br/> "+this.$t('words.upload-files-here'),
                    dictRemoveFile: this.$t('words.delete'),
                    thumbnailWidth: 150,
                    maxFilesize: 20,
                },
                form: this.$inertia.form({
                    name_ar: '',
                    name_en: '',
                    instructor_id: '',
                    description: '',
                    approval_code: '',
                    days_duration: '',
                    hours_duration: '',
                    training_package: '',
                }, {
                    bag: 'createCourse',
                })
            }
        },
        methods: {
            fileAdded(file) {
                this.form.training_package = file;
            },
            createCourse() {
                this.form.post('/back/courses', {
                    preserveScroll: true
                });
            },
        }
    }
</script>

<style>
    .dropzone .dz-preview .dz-progress {
        display: none;
    }
    .vue-dropzone > .dz-preview .dz-remove {
        margin: 0 10px;
    }

    .vue-dropzone > .dz-preview .dz-details {
        background-color: rgb(37, 47, 63);
    }

    .dropzone.dz-clickable .dz-message, .dropzone.dz-clickable .dz-message * {
        font-family: Nunito, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
        line-height: 1.5;
    }
</style>
