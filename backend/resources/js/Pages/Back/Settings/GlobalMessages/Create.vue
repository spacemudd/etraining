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
                    {title: 'global-messages'},
                    {title: 'new'},
                ]"
            ></breadcrumb-container>

            <div class="mt-4">
                <jet-form-section @submitted="createGlobalMessage">
                    <template #title>
                        {{ $t('words.global-message') }}
                    </template>

                    <template #description>

                    </template>

                    <template #form>

                        <div class="col-span-4 sm:col-span-4">
                            <jet-label for="company" :value="$t('words.company')" />
                            <div class="relative mt-2">
                                <select v-model="form.company_id"
                                        id="company_id">
                                    <option v-for="company in companies"
                                            :key="company.id"
                                            :value="company.id">
                                        {{ company.name_ar ? company.name_ar : company.name_en }}
                                    </option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                                </div>
                            </div>
                            <jet-input-error :message="form.error('company_id')" class="mt-2" />
                        </div>

                        <div class="col-span-2 sm:col-span-2">
                            <jet-label for="starts_at" :value="$t('words.starts-at')" />
                            <jet-input id="starts_at" type="datetime-local" class="mt-1 block w-full" v-model="form.starts_at" dir="ltr" autocomplete="off" />
                            <jet-input-error :message="form.error('starts_at')" class="mt-2" />
                        </div>

                        <div class="col-span-2 sm:col-span-2">
                            <jet-label for="expires_at" :value="$t('words.expires-at')" />
                            <jet-input id="expires_at" type="datetime-local" class="mt-1 block w-full" v-model="form.expires_at" dir="ltr" autocomplete="off" />
                            <jet-input-error :message="form.error('expires_at')" class="mt-2" />
                        </div>

                        <div class="col-span-2 sm:col-span-2">
                            <jet-label for="body" :value="$t('words.message')" />
                            <jet-textarea id="body" type="text" class="mt-1 block w-full" v-model="form.body">
                            </jet-textarea>
                            <jet-input-error :message="form.error('body')" class="mt-2" />
                        </div>

                        <div class="col-span-2 sm:col-span-2">
                            <jet-label for="channels" :value="$t('words.channels')" />
                            <label class="flex items-center mt-2">
                                <input type="checkbox" class="form-checkbox" value="web" checked="on" disabled="disabled">
                                <span class="mr-2 text-sm text-gray-600">web</span>
                            </label>
                            <label class="flex items-center mt-2">
                                <input type="checkbox" class="form-checkbox bg-gray-400" value="web" disabled="disabled">
                                <span class="mr-2 text-sm text-gray-600">email</span>
                            </label>
                            <label class="flex items-center mt-2">
                                <input type="checkbox" class="form-checkbox bg-gray-400" value="web" disabled="disabled">
                                <span class="mr-2 text-sm text-gray-600">whatsapp</span>
                            </label>
                            <label class="flex items-center mt-2">
                                <input type="checkbox" class="form-checkbox bg-gray-400" value="web" disabled="disabled">
                                <span class="mr-2 text-sm text-gray-600">sms</span>
                            </label>
                            <jet-input-error :message="form.error('body')" class="mt-2" />
                        </div>
                    </template>

                    <template #actions>
                        <jet-action-message :on="form.recentlySuccessful" class="mr-3">
                            {{ $t('words.saved-successfully') }}
                        </jet-action-message>

                        <inertia-link :href="route('back.settings.global-messages.index')" class="flex items-center justify-start rtl:ml-4 ltr:mr-4 rounded-md px-4 py-2 bg-white hover:bg-gray-300 text-right">
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
    import IconNavigate from 'vue-ionicons/dist/ios-arrow-dropright'
    import BreadcrumbContainer from "@/Components/BreadcrumbContainer";
    import JetFormSection from '@/Jetstream/FormSection';
    import JetLabel from '@/Jetstream/Label';
    import JetInput from '@/Jetstream/Input'
    import JetInputError from '@/Jetstream/InputError'
    import JetActionMessage from '@/Jetstream/ActionMessage';
    import JetButton from '@/Jetstream/Button';
    import JetSectionBorder from '@/Jetstream/SectionBorder'
    import JetTextarea from '@/Jetstream/Textarea'
    import 'selectize/dist/js/standalone/selectize.min';

    export default {
        metaInfo: { title: 'Settings' },
        // layout: Layout,
        components: {
            IconNavigate,
            AppLayout,
            BreadcrumbContainer,
            JetFormSection,
            JetLabel,
            JetInput,
            JetInputError,
            JetActionMessage,
            JetButton,
            JetSectionBorder,
            JetTextarea,
        },
        props: ['companies'],
        data() {
            return {
                form: this.$inertia.form({
                    company_id: '',
                    body: '',
                    starts_at: null,
                    expires_at: null,
                }, {
                    bag: 'createTrainee',
                })
            }
        },
        methods: {
            createGlobalMessage() {
                this.form.post(route('back.settings.global-messages.store'), {
                    preserveScroll: true
                }).catch(error => {
                    this.form.processing = false;
                }).finally(() => {
                    this.form.processing = false;
                });
            },
        },
        mounted() {
            let vm = this;
            $(document).ready(function () {
                vm.companySelector = $('#company_id').selectize({
                    sortField: 'text',
                    maxOptions: 9999,
                    onChange: function (value) {
                        vm.form.company_id = value;
                    }
                })
            });
        },
        beforeDestroy() {
            $(document).ready(function () {
                $('#company_id').selectize()[0].selectize.destroy();
            });
        },
    }
</script>
