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
                ]"
            ></breadcrumb-container>
<h1>Index</h1>
            <div class="overflow-x-auto">
                <div>
                    <div v-for="role in rolesOrdered"
                         class="bg-white rounded shadow overflow-x-auto my-5 p-4 flex justify-between">
                        <div>
                            <p class="font-bold">({{ role.users_count }}) {{ role.display_name }}</p>
                            <p class="text-sm text-gray-400 mt-2">
                                <ion-icon name="alert-circle-outline" class="inline-block align-middle"></ion-icon>
                                <span>{{ role.role_description }}</span>
                            </p>
                        </div>
                        <div class="flex flex-col" v-if="role.can_manage_users">
                            <inertia-link
                                v-can="'edit-permissions'"
                                :href="route('back.settings.roles.permissions.index', {id: role.id})"
                                style="min-width:120px;"
                                class="bg-gray-600 py-1 px-2 rounded text-white text-sm hover:bg-gray-800 mt-2 w-full text-center">
                                {{ $t('words.manage-permissions') }}
                            </inertia-link>

                            <inertia-link
                                :href="route('back.settings.roles.show', {id: role.id})"
                                style="min-width:120px;"
                                class="bg-blue-600 py-1 px-2 rounded text-white text-sm hover:bg-blue-800 mt-2 w-full text-center">
                                {{ $t('words.manage-users') }}
                            </inertia-link>
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

    export default {
        metaInfo() {
            return {
                title: this.$t('words.settings')
            }
        },
        components: {
            IconNavigate,
            AppLayout,
            BreadcrumbContainer,
        },
        props: ['roles'],
        computed: {
            rolesOrdered() {
                return _.sortBy(this.roles, 'order');
            }
        },
        data() {
            return {
                //
            }
        },
        methods: {
            //
        },
    }
</script>
