<!--
  - Copyright (c) 2020 - Clarastars, LLC  - All Rights Reserved.
  -
  - Unauthorized copying of this file via any medium is strictly prohibited.
  - This file is a proprietary of Clarastars LLC and is confidential / educational purpose only.
  -
  - https://clarastars.com - info@clarastars.com
  - @author Shafiq al-Shaar <shafiqalshaar@gmail.com>
  -->

<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'settings', link: route('back.settings')},
                    {title: 'trainee-application-settings'},
                ]"
            ></breadcrumb-container>

            <div class="container px-6 mx-auto grid">
                <div class="col-span-1 bg-white shadow-lg rounded-lg p-5">
                    <h2>{{ $t('words.here-you-can-specify-the-required-documents-by-trainees-to-complete-their-application') }}</h2>

                    <div class="mt-5">
                        <form @submit.prevent="storeNewFileRequest">
                            <input class="input"
                                   v-model="createRequirementForm.name_ar"
                                   type="text"
                                   name="fileRequestTitleAr"
                                   placeholder="عنوان الملف"
                                   required>
                            <input class="input"
                                   v-model="createRequirementForm.name_en"
                                   type="text"
                                   name="fileRequestTitleEn"
                                   placeholder="File title"
                                   required>

                            <jet-button :class="{ 'opacity-25': createRequirementForm.processing }" :disabled="createRequirementForm.processing">
                                {{ $t('words.add') }}
                            </jet-button>
                        </form>
                    </div>

                    <!-- List of currently required files -->
                    <div class="mt-14">
                            <div v-for="file in requiredFiles"
                                 class="my-5 block border-2 border-gray-300 p-3">
                                {{ file.name_ar }} - {{ file.name_en }}<br/>
                                <button style="font-family:Tahoma;"
                                        class="text-xs text-red-500"
                                        @click="deleteRequirement(file.id)"
                                >
                                    ({{ $t('words.delete') }})
                                </button>
                                <!--<br/>-->
                                <!--_____________________-->
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout'
    import IconNavigate from 'vue-ionicons/dist/ios-arrow-dropright'
    import BreadcrumbContainer from "@/Components/BreadcrumbContainer";
    import JetButton from '@/Jetstream/Button'

    export default {
        metaInfo: { title: 'Settings' },
        // layout: Layout,
        components: {
            IconNavigate,
            AppLayout,
            BreadcrumbContainer,
            JetButton,
        },
        props: {
            companies: Object,
            filters: Object,
        },
        data() {
            return {
                requiredFiles: [],
                createRequirementForm: this.$inertia.form({
                    name_ar: '',
                    name_en: '',
                    required: false,
                }, {
                    bag: 'createRequirementForm',
                    resetOnSuccess: true,
                })
            }
        },
        mounted() {
            this.getRequiredFiles();
        },
        methods: {
            storeNewFileRequest() {
                this.createRequirementForm.post(route('back.settings.trainees-application.required-files'), {
                    preserveScroll: true
                }).then(response => {
                    this.getRequiredFiles();
                });
            },
            getRequiredFiles() {
                axios.get(route('back.settings.trainees-application.required-files'))
                    .then(response => {
                        this.requiredFiles = response.data;
                    })
            },
            deleteRequirement(id) {
                axios.delete(route('back.settings.trainees-application.required-files.delete', {id: id}))
                .then(response => {
                    this.getRequiredFiles();
                })
            },
        },
    }
</script>
