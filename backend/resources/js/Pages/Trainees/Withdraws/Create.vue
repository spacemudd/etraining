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
    <app-layout-trainee>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'profile'},
                    {title: 'withdraw'},
                ]"
            ></breadcrumb-container>

            <div class="grid grid-cols-1 xl:grid-cols-3">
                <div class="col-1">
                    <div class="bg-white border-red-400 border rounded p-4">
                        <div class="flex gap-2">
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mt-1 w-6 h-6">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-xl font-bold">{{ $t('words.withdraw-application') }}</h1>
                                <ul class="list-decimal mt-5">
                                    <li><a href="#" class="text-blue-500 hover:text-blue-600">{{ $t('words.download-the-withdraw-application') }}</a>. <span dir="ltr" class="text-xs">(.doc)</span></li>
                                    <li>{{ $t('words.complete-the-form') }}.</li>
                                    <li>{{ $t('words.upload-the-form-below') }}.</li>
                                </ul>
                                <form @submit.prevent="save">
                                    <div class="mt-3">
                                        <jet-label for="reason" :value="$t('words.reason')"/>
                                        <jet-input
                                            id="reason"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="form.reason"
                                            autocomplete="off" />
                                        <jet-input-error :message="form.error('reason')" :placeholder="$t('words.optional')" class="mt-2" />
                                    </div>
                                    <div class="mt-3">
                                        <jet-label for="files">
                                            {{ $t('words.completed-withdrawal-application-copy') }} <span class="text-red-500">*</span>
                                        </jet-label>
                                        <div class="flex justify-center">
                                            <div class="mb-3 w-full">
                                                <input class="form-control block w-full mt-2 px-3 py-1.5 text-base font-normal text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none"
                                                       @input="form.files = $event.target.files"
                                                       type="file"
                                                       id="formFileMultiple"
                                                       required
                                                       multiple>
                                            </div>
                                        </div>
                                        <jet-input-error :message="form.error('files')" class="mt-2" />
                                    </div>
                                    <p class="text-xs text-gray-600"><span class="text-red-500">*</span> {{ $t('words.responsibility-disclaimer') }}</p>
                                    <button class="btn-primary w-full text-center justify-center mt-3">{{ $t('words.send') }}</button>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </app-layout-trainee>
</template>

<script>
import AppLayoutTrainee from '@/Layouts/AppLayoutTrainee'
import BreadcrumbContainer from "@/Components/BreadcrumbContainer";
import JetInput from '@/Jetstream/Input'
import JetLabel from '@/Jetstream/Label'
import JetInputError from '@/Jetstream/InputError'

export default {
    components: {
        AppLayoutTrainee,
        BreadcrumbContainer,
        JetInput,
        JetLabel,
        JetInputError
    },
    props: [
        'user',
    ],
    data() {
        return {
            form: this.$inertia.form({
                reason: '',
                files: '',
            }, {
                bag: 'upload',
            })
        }
    },
    mounted() {
        //
    },
    methods: {
        save() {
            if (confirm(this.$t('words.are-you-sure'))) {
                this.form.post(route('trainees.withdraws.store'));
            }
        }
    }
}
</script>
