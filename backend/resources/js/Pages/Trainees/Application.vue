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
                        <img src="/img/student.svg" class="w-8 ml-5">
                        قم بتعبئة ملفك
                    </div>

                    <hr class="border-1 mb-5">

                    <div class="mt-4">
                        <label class="block font-medium text-sm text-gray-700 text-center">
                            {{ $t('words.identity-card-photocopy') }}
                        </label>

                        <label class="mt-5 mx-auto w-64 flex flex-col items-center px-4 py-6 bg-white text-blue rounded-lg shadow-lg border border-blue cursor-pointer hover:bg-blue hover:text-red-500">
                            <svg class="w-8 h-8" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path d="M16.88 9.1A4 4 0 0 1 16 17H5a5 5 0 0 1-1-9.9V7a3 3 0 0 1 4.52-2.59A4.98 4.98 0 0 1 17 8c0 .38-.04.74-.12 1.1zM11 11h3l-4-4-4 4h3v3h2v-3z" />
                            </svg>

                            <span v-if="identity_card_copy" class="mt-2 text-base leading-normal text-center">
                                {{ $t('words.file-selected') }} ✅<br/>
                                <div class="text-sm text-gray-500 mt-5" v-if="identity_card_copy.name">{{ identity_card_copy.name }}</div>
                            </span>
                            <span v-else class="mt-2 text-base leading-normal">{{ $t('words.select-file') }}</span>

                            <input type='file'
                                   class="hidden"
                                   :ref="$t('words.identity-card-photocopy')"
                                   @change="uploadFile($event, 'identity_card_copy')"
                                   :name="$t('words.identity-card-photocopy')"
                                   required />
                        </label>
                    </div>

                    <div class="mt-4">
                        <label class="block font-medium text-sm text-gray-700 text-center">
                            {{ $t('words.qualification-photocopy') }}
                        </label>

                        <label class="mt-5 mx-auto w-64 flex flex-col items-center px-4 py-6 bg-white text-blue rounded-lg shadow-lg border border-blue cursor-pointer hover:bg-blue hover:text-red-500">
                            <svg class="w-8 h-8" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path d="M16.88 9.1A4 4 0 0 1 16 17H5a5 5 0 0 1-1-9.9V7a3 3 0 0 1 4.52-2.59A4.98 4.98 0 0 1 17 8c0 .38-.04.74-.12 1.1zM11 11h3l-4-4-4 4h3v3h2v-3z" />
                            </svg>

                            <span v-if="qualification_copy" class="mt-2 text-base leading-normal text-center">
                                {{ $t('words.file-selected') }} ✅<br/>
                                <div class="text-sm text-gray-500 mt-5" v-if="qualification_copy.name">{{ qualification_copy.name }}</div   >
                            </span>
                            <span v-else class="mt-2 text-base leading-normal">{{ $t('words.select-file') }}</span>

                            <input type='file'
                                   class="hidden"
                                   :ref="$t('words.qualification-photocopy')"
                                   @change="uploadFile($event, ('qualification_copy'))"
                                   :name="$t('words.qualification-photocopy')"
                                   required />
                        </label>
                    </div>

                    <div class="mt-4">
                        <label class="block font-medium text-sm text-gray-700 text-center">
                            {{ $t('words.bank-account-photocopy') }}
                        </label>

                        <label class="mt-5 mx-auto w-64 flex flex-col items-center px-4 py-6 bg-white text-blue rounded-lg shadow-lg border border-blue cursor-pointer hover:bg-blue hover:text-red-500">
                            <svg class="w-8 h-8" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path d="M16.88 9.1A4 4 0 0 1 16 17H5a5 5 0 0 1-1-9.9V7a3 3 0 0 1 4.52-2.59A4.98 4.98 0 0 1 17 8c0 .38-.04.74-.12 1.1zM11 11h3l-4-4-4 4h3v3h2v-3z" />
                            </svg>

                            <span v-if="bank_account_copy" class="mt-2 text-base leading-normal text-center">
                                {{ $t('words.file-selected') }} ✅<br/>
                                <div class="text-sm text-gray-500 mt-5" v-if="bank_account_copy.name">{{ bank_account_copy.name }}</div   >
                            </span>
                            <span v-else class="mt-2 text-base leading-normal">{{ $t('words.select-file') }}</span>

                            <input type='file'
                                   class="hidden"
                                   :ref="$t('words.bank-account-photocopy')"
                                   @change="uploadFile($event, ('bank_account_copy'))"
                                   :name="$t('words.bank-account-photocopy')"
                                   required />
                        </label>
                    </div>

                    <div class="flex items-center justify-center mt-4">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150 mx-4 tracking-normal">
                            {{ $t('words.submit') }}
                        </button>
                    </div>
                </form>
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
            identity_card_copy: null,
            qualification_copy: null,
            bank_account_copy: null,
            formData: new FormData(),
        }
    },
    props: [
        'is_pending_approval_prop',
        'trainee_id',
        'trainee_email'
    ],
    methods: {
        uploadFile(e, filename) {
            this.formData.append('trainee_email', this.trainee_email);
            this.formData.append('trainee_id', this.trainee_id);

            if (filename == "identity_card_copy") {
                this.identity_card_copy = e.target.files[0];
                this.formData.append('identity_card_copy', this.identity_card_copy);

            } else if (filename === 'qualification_copy') {
                this.qualification_copy = e.target.files[0];
                this.formData.append('qualification_copy', this.qualification_copy);

            } else if (filename === 'bank_account_copy') {
                this.bank_account_copy = e.target.files[0];
                this.formData.append('bank_account_copy', this.bank_account_copy);
            }
        },
        submitForm() {
            axios.post(route('api.register.trainees.upload-cv'), this.formData)
                .then(response => {
                    this.is_pending_approval = true;
                    window.location.reload();
                });

        },
    }

}
</script>
