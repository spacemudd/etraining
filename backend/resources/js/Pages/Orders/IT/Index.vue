<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'orders', link: route('orders.index')},
                    {title: 'it', link: route('orders.it')},
                ]"
            ></breadcrumb-container>

            <div class="grid md:grid-cols-4 grid-cols-1 gap-6">
                <inertia-link :href="route('new_email.index')"
                              class="col-span-1 bg-white shadow-lg rounded-lg p-5 transition-all duration-500 ease-in-out hover:bg-gray-200 text-center">
                    <center><img style="width: 65px;" src="https://i.ibb.co/LgTNbGm/email.png" alt="Rejected" class="w-56 mt-3"></center> <br>
                    {{ $t('words.create_new_email_order') }}
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
