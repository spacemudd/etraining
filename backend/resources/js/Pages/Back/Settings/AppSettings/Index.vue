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
                    {title: 'app-settings'},
                ]"
            ></breadcrumb-container>

            <div class="px-6">
                <div class="col-span-1 bg-white shadow-lg rounded-lg p-5">
                    <h2>{{ $t('words.app-settings') }}</h2>

                    <div class="mt-5 text-xs">
                        <form @submit.prevent="updateRequest">

                            <p class="mt-5">Monthly GOSI Request Limit</p>

                            <div class="grid grid-cols-3 mt-2">
                                <div class="col-sm-1">
                                    <p class="text-gray-600 text-xs">Current value: {{ gosi_monthly_requests }}</p>
                                    <input
                                        type="number"
                                        class="input w-full"
                                        v-model="updateForm.gosi_monthly_requests"
                                        min="0"
                                        required
                                    />
                                </div>
                            </div>

                            <div class="mt-5">
                                <jet-button class="col-xs-3" :class="{ 'opacity-25': updateForm.processing }" :disabled="updateForm.processing">
                                    {{ $t('words.save') }}
                                </jet-button>
                            </div>
                        </form>
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
    props: ['gosi_monthly_requests'],
    data() {
        return {
            updateForm: this.$inertia.form({
                gosi_monthly_requests: '',
                required: false,
            }, {
                bag: 'updateForm',
                resetOnSuccess: false,
            })
        }
    },
    mounted() {
        this.updateForm.gosi_monthly_requests = this.gosi_monthly_requests;
    },
    methods: {
        updateRequest() {
            this.updateForm.put(route('back.settings.app.update'), {
                onSuccess: () => {
                    alert('Settings updated successfully.');
                },
            });
        },
    },
}
</script>
