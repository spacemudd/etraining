<template>
    <app-layout>
        <div v-if="!this.isArchive" class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'instructors', link: route('back.instructors.index')},
                ]"
            ></breadcrumb-container>

            <div class="flex justify-between">

                <h1 class="mb-8 font-bold text-3xl">{{ $t('words.instructors') }}</h1>

                <div class="mb-6 flex justify-between items-center">

                    <button v-if="!this.isArchive" @click="showBlocked" :class="archiveBtn.class">
                        {{ archiveBtn.text }}
                    </button>

                    <button v-else @click="hideBlocked" :class="archiveBtn.class">
                        {{ archiveBtn.text }}
                    </button>
                    <!--<search-filter v-model="form.search" class="w-full max-w-md mr-4" @reset="reset">-->
                    <!--    <label class="block text-gray-700">Trashed:</label>-->
                    <!--    <select v-model="form.trashed" class="mt-1 w-full form-select">-->
                    <!--        <option :value="null" />-->
                    <!--        <option value="with">With Trashed</option>-->
                    <!--        <option value="only">Only Trashed</option>-->
                    <!--    </select>-->
                    <!--</search-filter>-->
                    <inertia-link class="btn-gray" :href="route('back.instructors.create')">
                        <span>{{ $t('words.new') }}</span>
                    </inertia-link>
                </div>
            </div>
            <div class="bg-white rounded shadow overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <tr class="text-left font-bold">
                        <th class="px-6 pt-6 pb-4">{{ $t('words.name') }}</th>
                        <th class="px-6 pt-6 pb-4">{{ $t('words.phone') }}</th>
                        <th class="px-6 pt-6 pb-4">{{ $t('words.company') }}</th>
                    </tr>

                    <tr v-for="instructor in instructors.data" :key="instructor.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
                        <td class="border-t">
                            <div class="px-6 py-4 flex items-center focus:text-indigo-500">
                                <inertia-link :href="route('back.instructors.show', instructor.id)">
                                    {{ instructor.name }}
                                    <br/>

                                    <span v-if="instructor.is_pending_uploading_files" class="text-sm inline-block mt-2 p-1 px-2 bg-blue-300 rounded-lg">
                                        {{ $t('words.incomplete-application') }}
                                    </span>

                                    <span v-if="instructor.is_pending_approval" class="text-sm inline-block mt-2 p-1 px-2 bg-yellow-200 rounded-lg">
                                        {{ $t('words.nominated-instructor') }}
                                    </span>

                                    <span v-if="instructor.is_approved" class="text-sm inline-block mt-2 p-1 px-2 bg-green-300 rounded-lg">
                                        {{ $t('words.approved') }}
                                    </span>

                                </inertia-link>
                            </div>
                        </td>
                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center" :href="route('back.instructors.show', instructor.id)" tabindex="-1">
                                <div v-if="instructor.phone">
                                    {{ instructor.phone }}
                                </div>
                            </inertia-link>
                        </td>
                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center" :href="route('back.instructors.show', instructor.id)" tabindex="-1">
                                {{ instructor.address }}
                            </inertia-link>
                        </td>
                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center" :href="route('back.instructors.show', instructor.id)" tabindex="-1">
                                <span v-if="instructor.company">{{ instructor.company.name }}</span>
                            </inertia-link>
                        </td>
                        <td class="border-t w-px">
                            <inertia-link class="px-4 flex items-center" :href="route('back.instructors.show', instructor.id)" tabindex="-1">
                                <ion-icon name="arrow-forward-outline" class="block w-6 h-6 fill-gray-400"></ion-icon>
                            </inertia-link>
                        </td>
                    </tr>

                    <tr v-if="instructors.data.length === 0">
                        <td class="border-t px-6 py-4" colspan="4">
                            <empty-slate/>
                        </td>
                    </tr>
                </table>
            </div>
            <pagination :links="instructors.links" />
        </div>

         <div v-else class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'instructors', link: route('back.instructors.index')},
                ]"
            ></breadcrumb-container>

            <div class="flex justify-between">

                <h1 class="mb-8 font-bold text-3xl">{{ $t('words.blocked-instructors') }}</h1>

                <div class="mb-6 flex justify-between items-center">

                    <button v-if="!this.isArchive" @click="showBlocked" :class="archiveBtn.class">
                        {{ archiveBtn.text }}
                    </button>

                    <button v-else @click="hideBlocked" :class="archiveBtn.class">
                        {{ archiveBtn.text }}
                    </button>
                    <!--<search-filter v-model="form.search" class="w-full max-w-md mr-4" @reset="reset">-->
                    <!--    <label class="block text-gray-700">Trashed:</label>-->
                    <!--    <select v-model="form.trashed" class="mt-1 w-full form-select">-->
                    <!--        <option :value="null" />-->
                    <!--        <option value="with">With Trashed</option>-->
                    <!--        <option value="only">Only Trashed</option>-->
                    <!--    </select>-->
                    <!--</search-filter>-->
                    <inertia-link class="btn-gray" :href="route('back.instructors.create')">
                        <span>{{ $t('words.new') }}</span>
                    </inertia-link>
                </div>
            </div>
            <div class="bg-white rounded shadow overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <tr class="text-left font-bold">
                        <th class="px-6 pt-6 pb-4">{{ $t('words.name') }}</th>
                        <th class="px-6 pt-6 pb-4">{{ $t('words.phone') }}</th>
                        <th class="px-6 pt-6 pb-4">{{ $t('words.company') }}</th>
                    </tr>

                    <tr v-for="instructor in blocked_instructors.data" :key="instructor.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
                        <td class="border-t">
                            <div class="px-6 py-4 flex items-center focus:text-indigo-500">
                                <inertia-link :href="route('back.instructors.show.blocked', instructor.id)">
                                    {{ instructor.name }}
                                    <br/>

                                    <span class="text-sm inline-block mt-2 p-1 px-2 bg-red-300 rounded-lg">
                                        {{ $t('words.blocked') }}
                                    </span>

                                </inertia-link>
                            </div>
                        </td>
                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center" :href="route('back.instructors.show.blocked', instructor.id)" tabindex="-1">
                                <div v-if="instructor.phone">
                                    {{ instructor.phone }}
                                </div>
                            </inertia-link>
                        </td>
                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center" :href="route('back.instructors.show.blocked', instructor.id)" tabindex="-1">
                                {{ instructor.address }}
                            </inertia-link>
                        </td>
                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center" :href="route('back.instructors.show.blocked', instructor.id)" tabindex="-1">
                                <span v-if="instructor.company">{{ instructor.company.name }}</span>
                            </inertia-link>
                        </td>
                        <td class="border-t w-px">
                            <inertia-link class="px-4 flex items-center" :href="route('back.instructors.show.blocked', instructor.id)" tabindex="-1">
                                <ion-icon name="arrow-forward-outline" class="block w-6 h-6 fill-gray-400"></ion-icon>
                            </inertia-link>
                        </td>
                    </tr>

                    <tr v-if="blocked_instructors.data.length === 0">
                        <td class="border-t px-6 py-4" colspan="4">
                            <empty-slate/>
                        </td>
                    </tr>
                </table>
            </div>
            <pagination :links="blocked_instructors.links" />
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
    import EmptySlate from "@/Components/EmptySlate";

    export default {
        metaInfo: { title: 'Instructor' },
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
        props: {
            blocked_instructors: Object,
            instructors: Object,
            filters: Object,
        },
        data() {
            return {
                archiveBtn: {
                    text: this.$t('words.archive'),
                    class: "items-center justify-start mr-3 ml-3 float-left px-3 py-2.5 bg-yellow-200 hover:bg-yellow-300 text-left"
                },
                isArchive: false,
                showedInstructors: Object,
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
                    this.$inertia.replace(this.route('instructors', Object.keys(query).length ? query : { remember: 'forget' }))
                }, 150),
                deep: true,
            },
        },
        methods: {
            reset() {
                this.form = mapValues(this.form, () => null)
            },
            showBlocked() {
                console.log(this.blocked_instructors)
                this.isArchive = true;
                this.archiveBtn.text =  this.$t('words.active');
                this.archiveBtn.class = "items-center justify-start mr-3 ml-3 float-left px-3 py-2.5 bg-green-200 hover:bg-green-300 text-left";
                this.$forceUpdate();
            },
            hideBlocked() {
                this.isArchive = false;
                this.archiveBtn.text =  this.$t('words.archive');
                this.archiveBtn.class = "items-center justify-start mr-3 ml-3 float-left px-3 py-2.5 bg-yellow-200 hover:bg-yellow-300 text-left";
            },
        },
    }
</script>
