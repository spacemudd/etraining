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

            <div class="grid md:grid-cols-4 grid-cols-1 gap-6">
                <div>
                    <div v-for="role in roles">
                        {{ role.display_name }}
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
        metaInfo: { title: 'Settings' },
        components: {
            IconNavigate,
            AppLayout,
            BreadcrumbContainer,
        },
        props: ['roles'],
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
