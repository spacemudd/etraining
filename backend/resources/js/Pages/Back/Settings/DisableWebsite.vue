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
                    {title: 'disable-website'},
                ]"
            ></breadcrumb-container>

            <div class="px-6">
                <div class="col-span-1 bg-white shadow-lg rounded-lg p-5">
                    <h2>{{ $t('words.here-you-can-disable-the-website-and-specify-a-message') }}</h2>

                    <div class="mt-5 text-xs">
                        <form @submit.prevent="updateRequest">

                            <p>{{ $t('words.disable-website') }}</p>

                            <div class="grid grid-cols-3 mt-2">
                                <div class="col-sm-1 relative">
                                    <select class="block appearance-none w-full border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                            name="website_disabled"
                                            v-model="updateForm.website_disabled">
                                        <option value="1">{{ $t('words.yes') }}</option>
                                        <option value="0">{{ $t('words.no') }}</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                                    </div>
                                </div>
                            </div>


                            <p class="mt-5">{{ $t('words.disable-website-notice') }}</p>

                            <div class="grid grid-cols-3 mt-2">
                                <div class="col-sm-1">
                                    <textarea class="col-xs-3 input w-full"
                                              v-model="updateForm.website_disabled_notice"
                                              required />
                                </div>
                            </div>


                            <div class="mt-5">
                                <jet-button class="col-xs-3" :class="{ 'opacity-25': updateForm.processing }" :disabled="updateForm.processing">
                                    {{ $t('words.save') }}
                                </jet-button>
                            </div>
                        </form>
                    </div>

                    <!-- List of currently required files -->
                    <div class="mt-14">
                        <div v-for="file in requiredFiles"
                             class="my-5 block border-2 border-gray-300 p-3">
                            {{ file.name_ar }} - {{ file.name_en }}<br/>
                            <button style="font-family:Tahoma;"
                                    class="text-xs text-red-500"
                                    @click="deleteRequirement(file.id)">
                                ({{ $t('words.delete') }})
                            </button>
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
        props: ['website_disabled', 'website_disabled_notice'],
        data() {
            return {
                requiredFiles: [],
                updateForm: this.$inertia.form({
                    website_disabled: '',
                    website_disabled_notice: '',
                    required: false,
                }, {
                    bag: 'updateForm',
                    resetOnSuccess: true,
                })
            }
        },
        mounted() {
            this.updateForm.website_disabled = this.website_disabled;
            this.updateForm.website_disabled_notice = this.website_disabled_notice;
        },
        methods: {
            updateRequest() {
                this.updateForm.put(route('back.settings.disable-website.update'));
            },
        },
    }
</script>
