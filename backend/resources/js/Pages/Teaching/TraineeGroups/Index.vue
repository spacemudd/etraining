<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'trainees', link: route('teaching.trainee-groups.index')},
                ]"
            ></breadcrumb-container>
            <div class="flex justify-between">
                <h1 class="mb-8 font-bold text-3xl">{{ $t('words.groups') }}</h1>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-6 gap-6 my-5">
                <div class="p-2 rounded bg-white shadow" v-for="group in traineeGroups">
                    <div class="my-3 mt-5">
                        <p class="text-center">
                            <!--<span class="border-gray-500 border-2 py-1 px-2 rounded">{{ group.name }}</span>-->
                            <span class="border-gray-500 py-1 px-2 rounded font-bold">{{ group.name }}</span>
                        </p>
                    </div>

                    <div class="my-7">
                        <p class="text-center mb-2 text-gray-500"><b>{{ $t('words.trainees-count') }}</b></p>
                        <p class="text-center"><span class="rounded-xl bg-gray-200 p-2">{{ group.trainees_count }}</span></p>
                    </div>

                    <div class="my-7 text-center">

                        <inertia-link :href="route('teaching.trainee-groups.trainees.index', group.id)"
                                      class="bg-gray-200 font-semibold text-black hover:bg-gray-300 active:bg-gray-300 inline-flex items-center px-4 py-2 border border-transparent rounded-md text-xs focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150">
                            {{ $t('words.view-trainees') }}
                        </inertia-link>
                    </div>

                    <div class="my-7 text-center">
                        <inertia-link :href="route('teaching.trainee-groups.announcements.create', group.id)"
                                      class="bg-gray-800 font-semibold text-white hover:bg-gray-700 active:bg-gray-900 inline-flex items-center px-4 py-2 border border-transparent rounded-md text-xs focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150">
                            {{ $t('words.send-announcement') }}
                        </inertia-link>
                    </div>
                </div>
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
    import AppLayout from '@/Layouts/AppLayoutInstructor'
    import IconNavigate from 'vue-ionicons/dist/ios-arrow-dropright'
    import BreadcrumbContainer from "@/Components/BreadcrumbContainer";
    import EmptySlate from "@/Components/EmptySlate";

    export default {
        metaInfo: { title: 'Trainees' },
        // layout: Layout,
        components: {
            EmptySlate,
            BreadcrumbContainer,
            IconNavigate,
            AppLayout,
            // Icon,
            Pagination,
            // SearchFilter,
        },
        props: ['traineeGroups'],
        data() {
            return {
                form: {
                    // search: this.filters.search,
                    // trashed: this.filters.trashed,
                },
            }
        },
        watch: {
            form: {
                handler: throttle(function() {
                    let query = pickBy(this.form)
                    this.$inertia.replace(this.route('courses', Object.keys(query).length ? query : { remember: 'forget' }))
                }, 150),
                deep: true,
            },
        },
        methods: {
            reset() {
                this.form = mapValues(this.form, () => null)
            },
        },
    }
</script>
