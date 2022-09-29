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
<!--            HERE view the user information + can choose the selected companies-->
        </div>
    </app-layout>
</template>
<script>
import AppLayout from "../../../../Layouts/AppLayout";
export default {
    components: {AppLayout}
}
</script>
