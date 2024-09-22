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
                ]"
            ></breadcrumb-container>

            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex justify-between">
                    <h3 class="text-lg font-semibold text-gray-700 mb-6">{{ $t('words.global-messages') }}</h3>
                    <inertia-link class="btn btn-primary" :href="route('back.settings.global-messages.create')">{{ $t('words.new') }}</inertia-link>
                </div>

                <table class="text-right w-full whitespace-no-wrap">
                    <colgroup>
                        <col style="width:50%">
                    </colgroup>
                <thead>
                <tr>
                    <th class="px-6 pt-6 pb-4">{{ $t('words.message') }}</th>
                    <th class="px-6 pt-6 pb-4">{{ $t('words.company') }}</th>
                    <th class="px-6 pt-6 pb-4">{{ $t('words.starts-at') }}</th>
                    <th class="px-6 pt-6 pb-4">{{ $t('words.expires-at') }}</th>
                    <th class="px-6 pt-6 pb-4"></th>
                </tr>
                </thead>
                	<tbody>
                			<tr class="text-left" v-for="msg in global_messages">
                                <td class="border-t px-6 pt-6 pb-4 whitespace-normal"><p class="whitespace-pre" v-html="msg.body"></p></td>
                                <td class="border-t px-6 pt-6 pb-4">{{ msg.company ? msg.company.name_ar : $t('words.all') }}</td>
                                <td class="border-t px-6 pt-6 pb-4 text-left text-xs" dir="ltr">{{ msg.starts_at_timezone }}</td>
                                <td class="border-t px-6 pt-6 pb-4 text-left text-xs" dir="ltr">{{ msg.expires_at_timezone }}</td>
                                <td class="border-t px-6 pt-6 pb-4 text-left text-xs">
                                    <div class="flex justify-end">
                                        <form @submit.prevent="confirmDelete(msg.id)">
                                            <button type="submit" class="text-xs bg-red-500 text-white font-semibold p-2 text-center rounded my-1">
                                                {{ $t('words.delete') }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                			</tr>
                	</tbody>
                </table>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import IconNavigate from 'vue-ionicons/dist/ios-arrow-dropright'
import BreadcrumbContainer from "@/Components/BreadcrumbContainer";

export default {
    metaInfo: { title: 'Settings' },
    // layout: Layout,
    components: {
        IconNavigate,
        AppLayout,
        BreadcrumbContainer,
    },
    props: ['global_messages'],
    data() {
        return {
            //
        }
    },
    methods: {
        confirmDelete(id) {
            if (confirm(this.$t('words.are-you-sure'))) {
                this.$inertia.delete(route('back.settings.global-messages.destroy', id))
            }
        }
    },
}
</script>
