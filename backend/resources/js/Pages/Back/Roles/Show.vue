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
                    {title: 'roles', link: route('back.settings.roles.index')},
                    {title_raw: role.display_name},
                ]"
            ></breadcrumb-container>
<h1>Summer</h1>
            <div class="flex justify-between">
                <h1 class="mb-8 font-bold text-3xl">{{ role.display_name }}</h1>
                <div class="mb-6 flex justify-between items-center">
                    <inertia-link class="btn-gray"
                                  :href="route('back.settings.roles.users.invite', {id: role.id})">
                        <span>{{ $t('words.invite') }}</span>
                    </inertia-link>
                </div>
            </div>
            <div class="bg-white rounded shadow overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <tr class="text-left font-bold">
                        <th class="px-6 pt-6 pb-4">{{ $t('words.name') }}</th>
                        <th class="px-6 pt-6 pb-4">{{ $t('words.email') }}</th>
                    </tr>
                    <tr v-for="user in role.users" :key="user.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
                        <td class="border-t">
                            <div class="px-6 py-4 flex items-center focus:text-indigo-500">
                                {{ user.name }}
                            </div>
                        </td>
                        <td class="border-t">
                            <div class="px-6 py-4 flex items-center focus:text-indigo-500">
                                {{ user.email }}
                            </div>
                        </td>
                        <td class="border-t">
                            <div class="flex justify-end">
                                <button
                                    v-if="user.id != $page.props.user.id"
                                    @click.prevent="deleteUserPerma(user.id)"
                                    class="bg-red-500 text-white font-semibold p-2 text-center rounded my-1 mx-3">
                                    {{ $t('words.delete') }}
                                </button>
                            </div>
                        </td>
                    </tr>
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
        components: {
            IconNavigate,
            AppLayout,
            BreadcrumbContainer,
        },
        props: ['role'],
        data() {
            return {
                //
            }
        },
        methods: {
            deleteUserPerma(userId) {
                this.$inertia.delete(route('back.settings.roles.users.delete', {role_id: this.role.id, user_id: userId}));
            },
        },
    }
</script>
