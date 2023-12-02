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
                ]"
            ></breadcrumb-container>

            <div class="grid grid-cols-1 md:grid-cols-3">
                <div class="col-1">
                    <div class="my-2">
                        <jet-label for="name" :value="$t('words.name')" />
                        <jet-input id="name" type="text" class="mt-2 block w-full" :value="trainee.name" disabled />
                    </div>
                    <div class="my-2">
                        <jet-label for="name" :value="$t('words.phone')" />
                        <jet-input id="name" type="text" class="mt-2 block w-full" :value="trainee.phone" dir="ltr" disabled />
                    </div>

                    <div class="bg-red-200 border-red-400 border rounded p-4 mt-10">
                        <div class="flex gap-2">
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mt-1 w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                                </svg>
                            </div>
                            <div>
                                {{ $t('words.withdraw-information') }}
                                <p class="mt-5 mb-2">
                                    <inertia-link v-if="!pending_withdraws.length"
                                                  :href="route('trainees.withdraws.create')"
                                                  class="bg-red-600 text-white rounded px-2 py-1 font-semibold hover:bg-red-700">
                                        {{ $t('words.withdraw-from-training-program') }}
                                    </inertia-link>
                                    <span v-else
                                          class="bg-gray-200 border border-gray-400 text-gray-500 rounded px-2 py-1 font-semibold hover:bg-red-700"
                                        >
                                        {{ $t('words.you-currently-have-a-pending-withdrawal') }} (<span dir="ltr">#{{ pending_withdraws[0].number }}</span>)
                                    </span>
                                </p>
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
export default {
    components: {
        AppLayoutTrainee,
        BreadcrumbContainer,
        JetInput,
        JetLabel,
    },
    props: [
        'user',
        'trainee',
        'pending_withdraws',
    ],
    data() {
        return {

        }
    },
    mounted() {
        //
    },
    methods: {
        //
    }
}
</script>
