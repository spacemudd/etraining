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

            <div class="overflow-x-auto">
                <div class="bg-white rounded shadow overflow-x-auto my-5 p-4 flex flex-col justify-between">
                    <div v-for="permission in permissions">
                        <toggle-permission class="my-5"
                                           :role-id.number="role.id"
                                           :enabled-prop.number="isEnabled(permission.name)"
                                           :permission-name="permission.name">
                            {{ permission.display_name }}
                        </toggle-permission>
                        <hr>
                    </div>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout';
    import IconNavigate from 'vue-ionicons/dist/ios-arrow-dropright';
    import BreadcrumbContainer from "@/Components/BreadcrumbContainer";
    import TogglePermission from "@/Components/TogglePermission";

    export default {
        metaInfo: { title: 'Role' },
        components: {
            IconNavigate,
            AppLayout,
            BreadcrumbContainer,
            TogglePermission,
        },
        props: ['role', 'permissions'],
        data() {
            return {
                //
            }
        },
        computed: {

        },
        methods: {
            isEnabled(permissionName) {
                return (this.role.permissions.filter(pm => pm.name === permissionName).length > 0)
            }
        },
    }
</script>
