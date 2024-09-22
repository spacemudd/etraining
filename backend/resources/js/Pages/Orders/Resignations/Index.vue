<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'orders', link: route('orders.index')},
                    {title: 'resignations', link: route('orders.resignations.index')},
                ]"
            ></breadcrumb-container>


            <div class="flex justify-between">
                <h1 class="mb-8 font-bold text-3xl">{{ $t('words.resignations') }}</h1>
                <div class="mb-6 flex justify-between items-center">
                    <inertia-link class="btn-gray mx-3" :href="route('orders.resignations.create')">
                        <span>{{ $t('words.new') }}</span>
                    </inertia-link>
                </div>
            </div>

            <div class="grid md:grid-cols-4 grid-cols-1 gap-6">

            </div>
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
        props: {
        },
        data() {
            return {

            }
        },
        methods: {
        },
    }
</script>
