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
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
            <div>
                <a href="/">
                    <img src="/img/logo-lg.png" alt="Logo" class="w-56">
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                <div v-if="is_pending_approval_prop || is_pending_approval">
                    <application-pending :user_email="instructor_email"/>
                </div>
                <form v-else
                      @submit.prevent="submitForm" enctype="multipart/form-data">
                    <div><h1 class="text-2xl text-center my-5 font-bold">{{ $t('words.hi-there') }}</h1></div>

                    <div class="flex py-5 px-10 rounded-lg text-2xl justify-center">
                        <img src="/img/teacher.svg" class="w-8 ml-5">
                        قم بتعبئة ملفك
                    </div>

                    <hr class="border-1 mb-5">

                    <div class="mt-4">
                        <label class="block font-medium text-sm text-gray-700 text-center">
                            {{ $t('words.cv-full') }}
                        </label>

                        <label class="mt-5 mx-auto w-64 flex flex-col items-center px-4 py-6 bg-white text-blue rounded-lg shadow-lg border border-blue cursor-pointer hover:bg-blue hover:text-red-500">
                            <svg class="w-8 h-8" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path d="M16.88 9.1A4 4 0 0 1 16 17H5a5 5 0 0 1-1-9.9V7a3 3 0 0 1 4.52-2.59A4.98 4.98 0 0 1 17 8c0 .38-.04.74-.12 1.1zM11 11h3l-4-4-4 4h3v3h2v-3z" />
                            </svg>

                            <span v-if="cv_full" class="mt-2 text-base leading-normal text-center">
                                {{ $t('words.file-selected') }} ✅<br/>
                                <div class="text-sm text-gray-500 mt-5" v-if="cv_full.name">{{ cv_full.name }}</div>
                            </span>
                            <span v-else class="mt-2 text-base leading-normal">{{ $t('words.select-file') }}</span>

                            <input type='file'
                                   class="hidden"
                                   :ref="$t('words.cv-full')"
                                   @change="uploadFile($event, 'cv_full')"
                                   :name="$t('words.cv-full')"
                                   required />
                        </label>

                        <!--<input class="form-input rounded-md shadow-sm block mt-1 w-full" type="text" required="required">-->
                    </div>

                    <div class="mt-4">
                        <label class="block font-medium text-sm text-gray-700 text-center">
                            {{ $t('words.cv-summary') }}
                        </label>

                        <label class="mt-5 mx-auto w-64 flex flex-col items-center px-4 py-6 bg-white text-blue rounded-lg shadow-lg border border-blue cursor-pointer hover:bg-blue hover:text-red-500">
                            <svg class="w-8 h-8" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path d="M16.88 9.1A4 4 0 0 1 16 17H5a5 5 0 0 1-1-9.9V7a3 3 0 0 1 4.52-2.59A4.98 4.98 0 0 1 17 8c0 .38-.04.74-.12 1.1zM11 11h3l-4-4-4 4h3v3h2v-3z" />
                            </svg>

                            <span v-if="cv_summary" class="mt-2 text-base leading-normal text-center">
                                {{ $t('words.file-selected') }} ✅<br/>
                                <div class="text-sm text-gray-500 mt-5" v-if="cv_summary.name">{{ cv_summary.name }}</div   >
                            </span>
                            <span v-else class="mt-2 text-base leading-normal">{{ $t('words.select-file') }}</span>

                            <input type='file'
                                   class="hidden"
                                   :ref="$t('words.cv-summary')"
                                   @change="uploadFile($event, ('cv_summary'))"
                                   :name="$t('words.cv-summary')"
                                   required />
                        </label>

                        <!--<input class="form-input rounded-md shadow-sm block mt-1 w-full" type="text" required="required">-->
                    </div>

                    <div class="flex items-center justify-center mt-4">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150 mx-4 tracking-normal">
                            {{ $t('words.submit') }}
                        </button>
                    </div>
                </form>
            </div>
            <div class="mt-4">
                <template>
                    <button style="background-color: #c81010; margin-bottom: 30px ;" @click="logoutUser" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150 mx-4 tracking-normal">
                        <svg class="w-4 h-4 ltr:mr-3 rtl:ml-3"
                             aria-hidden="true"
                             fill="none"
                             stroke-linecap="round"
                             stroke-linejoin="round"
                             stroke-width="2"
                             viewBox="0 0 24 24"
                             stroke="currentColor">
                            <path d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"
                            ></path>
                        </svg>
                        <span>{{ $t('words.logout') }}</span>
                    </button>
                </template>
            </div>
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/ZoomLayout'
    import ApplicationPending from "@/Components/ApplicationPending";
    export default {
        components: {
            AppLayout,
            ApplicationPending,
        },
        data() {
            return {
                is_pending_approval: false,

                success: false,
                error: false,
                loading: false,
                cv_full: null,
                cv_summary: null,
                formData: new FormData(),
            }
        },
        props: [
            'is_pending_approval_prop',
            'instructor_id',
            'instructor_email'
        ],
        methods: {
            logoutUser() {
                axios.post('/logout').then(response => {
                    window.location = '/';
                })
            },
            uploadFile(e, filename) {
               this.formData.append('instructor_email', this.instructor_email);
               this.formData.append('instructor_id', this.instructor_id);
               if (filename == "cv_full") {
                   this.cv_full = e.target.files[0];
                   this.formData.append('cv_full', this.cv_full);
                   } else {
                   this.cv_summary = e.target.files[0];
                   this.formData.append('cv_summary', this.cv_summary);
               }
            },
            submitForm() {
                axios.post(route('api.register.instructors.upload-cv'), this.formData)
                    .then(response => {
                        this.is_pending_approval = true;
                    });
            },
        }

    }
</script>
