<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'trainees', link: route('teaching.trainee-groups.index')},
                    {title_raw: traineeGroup.name},
                ]"
            ></breadcrumb-container>
            <div class="flex justify-between">
                <h1 class="mb-8 font-bold text-3xl">{{ $t('words.trainees') }}</h1>
                <div class="mb-6 flex justify-between items-center">
                    <!--<search-filter v-model="form.search" class="w-full max-w-md mr-4" @reset="reset">-->
                    <!--    <label class="block text-gray-700">Trashed:</label>-->
                    <!--    <select v-model="form.trashed" class="mt-1 w-full form-select">-->
                    <!--        <option :value="null" />-->
                    <!--        <option value="with">With Trashed</option>-->
                    <!--        <option value="only">Only Trashed</option>-->
                    <!--    </select>-->
                    <!--</search-filter>-->
                </div>
            </div>
            <div class="bg-white rounded shadow overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <tr class="text-left font-bold">
                        <th class="px-6 pt-6 pb-4">{{ $t('words.name') }}</th>
                        <th class="px-6 pt-6 pb-4">{{ $t('words.phone') }}</th>
                        <th class="px-6 pt-6 pb-4">{{ $t('words.company') }}</th>
                    </tr>
                    <tr v-for="trainee in trainees.data" :key="trainee.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('teaching.trainee-groups.trainees.show', {trainee_group_id: traineeGroup.id, id: trainee.id})">
                                {{ trainee.name }}<br/>
                            </inertia-link>
                        </td>
                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center" :href="route('teaching.trainee-groups.trainees.show', {trainee_group_id: traineeGroup.id, id: trainee.id})" tabindex="-1">
                                <div v-if="trainee.phone">
                                    {{ trainee.phone }}
                                </div>
                            </inertia-link>
                        </td>
                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center" :href="route('teaching.trainee-groups.trainees.show', {trainee_group_id: traineeGroup.id, id: trainee.id})" tabindex="-1">
                                {{ trainee.address }}
                            </inertia-link>
                        </td>
                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center" :href="route('teaching.trainee-groups.trainees.show', {trainee_group_id: traineeGroup.id, id: trainee.id})" tabindex="-1">
                                <span v-if="trainee.company">{{ trainee.company.name }}</span>
                            </inertia-link>
                        </td>
                        <td class="border-t w-px">
                            <inertia-link class="px-4 flex items-center" :href="route('teaching.trainee-groups.trainees.show', {trainee_group_id: traineeGroup.id, id: trainee.id})" tabindex="-1">
                                <ion-icon name="arrow-forward-outline" class="block w-6 h-6 fill-gray-400"></ion-icon>
                            </inertia-link>
                        </td>
                    </tr>
                    <tr v-if="trainees.data.length === 0">
                        <td class="border-t px-6 py-4" colspan="4">
                            <empty-slate/>
                        </td>
                    </tr>
                </table>
            </div>
            <pagination :links="trainees.links" />
        </div>
    </app-layout>
</template>

<script>
    import mapValues from 'lodash/mapValues'
    import Pagination from '@/Shared/Pagination'
    import pickBy from 'lodash/pickBy'
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
        props: {
            traineeGroup: Object,
            trainees: Object,
            filters: Object,
        },
        data() {
            return {

            }
        },
        watch: {
            form: {
                handler: throttle(function() {
                    let query = pickBy(this.form)
                    this.$inertia.replace(this.route('trainees', Object.keys(query).length ? query : { remember: 'forget' }))
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
