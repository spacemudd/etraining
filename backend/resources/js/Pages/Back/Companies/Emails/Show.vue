<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[  {title: 'dashboard', link: route('dashboard')},  ]"
            ></breadcrumb-container>
            <div v-html="company_mails.body_html"></div><br>

        </div>
    </app-layout>
</template>
<script>
import Pagination from '@/Shared/Pagination'
import AppLayout from '@/Layouts/AppLayout'
import IconNavigate from 'vue-ionicons/dist/ios-arrow-dropright'
import BreadcrumbContainer from "@/Components/BreadcrumbContainer";

export default {
    components: {
        IconNavigate,
        AppLayout,
        Pagination,
        BreadcrumbContainer,
    },
    props: ['company_mails'],

    data() {
        return {
        }
    },
    methods: {
    },
}
</script>
