<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'certificates', link: route('back.certificates.import')},
                ]"
            ></breadcrumb-container>
            <div class="flex justify-between">
                <h1 class="mb-8 font-bold text-3xl">{{ $t('words.certificates') }}</h1>
            </div>
            <div class="bg-white rounded shadow overflow-x-auto">

            </div>
        </div>
    </app-layout>
</template>

<script>
    import Pagination from '@/Shared/Pagination'
    import AppLayout from '@/Layouts/AppLayout'
    import IconNavigate from 'vue-ionicons/dist/ios-arrow-dropright'
    import BreadcrumbContainer from "@/Components/BreadcrumbContainer";
    import EmptySlate from "@/Components/EmptySlate";
    export default {
        metaInfo: { title: 'Certificates' },
        components: {
            EmptySlate,
            BreadcrumbContainer,
            IconNavigate,
            AppLayout,
            Pagination,
        },
        data() {
            return {
                form: {

                },
            }
        },
        methods: {
        },
    }
</script>
