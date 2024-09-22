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
                    {title: 'payment-settings'}
                ]"
            ></breadcrumb-container>

            <div class="grid md:grid-cols-6 grid-cols-1 gap-6">
                <div class="col-span-3 bg-white shadow-xs rounded-lg p-5">
                    <form class="mt-2" @submit.prevent="submitForm">

                        <p>{{ $t('words.enabled') }}</p>
                        <div class="col-sm-1 relative">
                            <select class="block appearance-none w-full border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                    name="website_disabled"
                                    v-model="form.online_payment">
                                <option value="1">{{ $t('words.yes') }}</option>
                                <option value="0">{{ $t('words.no') }}</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                            </div>
                        </div>

                        <jet-button :class="{ 'opacity-25': $wait.is('SAVING_SETTING') }"
                                    class="mt-5"
                                    :disabled="$wait.is('SAVING_SETTING')">
                            {{ $t('words.save') }}
                        </jet-button>
                    </form>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import IconNavigate from 'vue-ionicons/dist/ios-arrow-dropright'
import BreadcrumbContainer from "@/Components/BreadcrumbContainer";
import JetInput from '@/Jetstream/Input'
import JetButton from '@/Jetstream/Button';

export default {
    metaInfo: { title: 'Settings' },
    components: {
        IconNavigate,
        AppLayout,
        BreadcrumbContainer,
        JetInput,
        JetButton,
    },
    props: [
        'online_payment',
    ],
    mounted() {
        this.form.online_payment = this.online_payment;
    },
    data() {
        return {
            form: {
                online_payment: false,
            }
        }
    },
    methods: {
        submitForm() {
            this.$inertia.put(route('back.settings.payment.update'), this.form);
        },
    }
}
</script>

<style lang="sass">
//
</style>
