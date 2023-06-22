<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'orders', link: route('orders.index')},
                ]"
            ></breadcrumb-container>

            <div class="grid md:grid-cols-4 grid-cols-1 gap-6">
                <inertia-link :href="route('orders.it')"
                              class="col-span-1 bg-white shadow-lg rounded-lg p-5 transition-all duration-500 ease-in-out hover:bg-gray-200 text-center">
                    <center><img style="width: 78px;" src="https://i.ibb.co/b1bvfMb/1000-F-20889267z7-LAXfh69b-SQa7n-Qp-JY1ma-QBw-Ys90-Siw0u-1.png" alt="Rejected" class="w-56 mt-3"></center> <br>
                    {{ $t('words.it') }}
                </inertia-link>
                <inertia-link :href="route('orders.hr')"
                              class="col-span-1 bg-white shadow-lg rounded-lg p-5 transition-all duration-500 ease-in-out hover:bg-gray-200 text-center">
                    <center><img style="width: 78px;" src="https://i.ibb.co/cCmySj8/istockphotjj-1164384748-612x612.png" alt="Rejected" class="w-56 mt-3"></center> <br>
                    {{ $t('words.hr') }}
                </inertia-link>
                <inertia-link :href="route('orders.finance')"
                              class="col-span-1 bg-white shadow-lg rounded-lg p-5 transition-all duration-500 ease-in-out hover:bg-gray-200 text-center">
                    <center><img style="width: 78px;" src="https://i.ibb.co/7Q7mwgH/1000-F-20889267z7-LAXfh69b-SQa7n-Qp-JY1ma-QBw-Ys90-Siw0u-1.png" alt="Rejected" class="w-56 mt-3"></center> <br>
                    {{ $t('words.finance') }}
                </inertia-link>
                <inertia-link :href="route('orders.collection')"
                              class="col-span-1 bg-white shadow-lg rounded-lg p-5 transition-all duration-500 ease-in-out hover:bg-gray-200 text-center">
                    <center><img style="width: 78px;" src="https://i.ibb.co/PTqF4nk/1000-F-20889267z7-LAXfh69b-SQa7n-Qp-JY1ma-QBw-Ys90-Siw0u-1.png" alt="Rejected" class="w-56 mt-3"></center> <br>
                    {{ $t('words.collection') }}
                </inertia-link>
                <inertia-link :href="route('orders.resignations')"
                              class="col-span-1 bg-white shadow-lg rounded-lg p-5 transition-all duration-500 ease-in-out hover:bg-gray-200 text-center">
                    <center><img style="width: 78px;" src="/img/resignations.svg" alt="Rejected" class="w-56 mt-3"></center> <br>
                    {{ $t('words.resignations') }}
                </inertia-link>
            </div>
        </div>
    </app-layout>
</template>

<script>
    // import Icon from '@/Shared/Icon'
    // import Layout from '@/Shared/Layout'
    import mapValues from 'lodash/mapValues'
    import Pagination from '@/Shared/Pagination'
    import pickBy from 'lodash/pickBy'
    // import SearchFilter from '@/Shared/SearchFilter'
    import throttle from 'lodash/throttle'
    import AppLayout from '@/Layouts/AppLayout'
    import IconNavigate from 'vue-ionicons/dist/ios-arrow-dropright'
    import BreadcrumbContainer from "@/Components/BreadcrumbContainer";

    export default {
        // layout: Layout,
        components: {
            IconNavigate,
            AppLayout,
            // Icon,
            Pagination,
            // SearchFilter,
            BreadcrumbContainer,
        },
        props: {
        },
        data() {
            return {
                form: {
                    // search: this.filters.search,
                    // trashed: this.filters.trashed,
                },
            }
        },
        methods: {
            reset() {
                this.form = mapValues(this.form, () => null)
            },
        },
    }
</script>
