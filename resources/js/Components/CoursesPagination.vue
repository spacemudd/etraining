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
    <div>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'courses', link: route('back.courses.index')},
                ]"
            ></breadcrumb-container>
            <div class="flex justify-between">
                <h1 class="mb-8 font-bold text-3xl">{{ $t('words.courses') }}</h1>
                <div class="mb-6 flex justify-between items-center">
                    <!--<search-filter v-model="form.search" class="w-full max-w-md mr-4" @reset="reset">-->
                    <!--    <label class="block text-gray-700">Trashed:</label>-->
                    <!--    <select v-model="form.trashed" class="mt-1 w-full form-select">-->
                    <!--        <option :value="null" />-->
                    <!--        <option value="with">With Trashed</option>-->
                    <!--        <option value="only">Only Trashed</option>-->
                    <!--    </select>-->
                    <!--</search-filter>-->
                    <inertia-link class="btn-gray" :href="route('back.courses.create')">
                        <span>{{ $t('words.new') }}</span>
                    </inertia-link>
                </div>
            </div>
            <div class="bg-white rounded shadow overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <tr class="text-left font-bold">
                        <th class="px-6 pt-6 pb-4">{{ $t('words.name') }}</th>
                        <th class="px-6 pt-6 pb-4">{{ $t('words.course-approval-code') }}</th>
                        <th class="px-6 pt-6 pb-4">{{ $t('words.instructor') }}</th>
                        <th class="px-6 pt-6 pb-4">{{ $t('words.recommended-trainees-count') }}</th>
                    </tr>
                    <tr v-for="course in courses.data" :key="course.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('back.courses.show', course.id)">
                                {{ course.name_ar }}
                            </inertia-link>
                        </td>
                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('back.courses.show', course.id)">
                                {{ course.approval_code }}
                            </inertia-link>
                        </td>
                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center" :href="route('back.courses.show', course.id)" tabindex="-1">
                                <div v-if="course.instructor">
                                    {{ course.instructor.name }}
                                </div>
                            </inertia-link>
                        </td>
                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center" :href="route('back.courses.show', course.id)" tabindex="-1">
                                {{ course.classroom_count }}
                            </inertia-link>
                        </td>
                        <td class="border-t w-px">
                            <inertia-link class="px-4 flex items-center" :href="route('back.courses.show', course.id)" tabindex="-1">
                                <ion-icon name="arrow-forward-outline" class="block w-6 h-6 fill-gray-400"></ion-icon>
                            </inertia-link>
                        </td>
                    </tr>
                    <tr v-if="courses.data.length === 0">
                        <td class="border-t px-6 py-4" colspan="4">
                            <empty-slate>
                                <template #actions>
                                    <inertia-link class="btn-gray mt-2 block" :href="route('back.courses.create')">
                                        <span>{{ $t('words.new') }}</span>
                                    </inertia-link>
                                </template>
                            </empty-slate>
                        </td>
                    </tr>
                </table>
            </div>
            <pagination :links="courses.links" />
        </div>
    </div>
</template>

<script>
    import JetButton from '@/Jetstream/Button';
    import { Skeleton } from 'vue-loading-skeleton';
    import Logrocket from 'logrocket';
    import EmptySlate from "@/Components/EmptySlate";
    import JetDialogModal from '@/Jetstream/DialogModal'
    import JetSecondaryButton from '@/Jetstream/SecondaryButton'

    export default {
        components: {
            JetButton,
            Skeleton,
            EmptySlate,
        },
        props: ['companyId', 'instructors'],
        name: "CompanyContractsPagination",
        data() {
            return {
                choosingInstructor: false,
                updateInstructorForm: this.$inertia.form({
                    instructor_id: null,
                    contract_id: null,
                }, {
                    resetOnSuccess: false,
                    bag: 'updateInstructorForm',
                }),
                contracts: [],
            }
        },
        mounted() {
            this.$wait.start('GETTING_CONTRACTS');
            axios.get('/back/companies/'+this.companyId+'/contracts')
                .then(response => {
                    this.contracts = response.data;
                    this.$wait.end('GETTING_CONTRACTS')
                }).catch(error => {
                    Logrocket.captureException(error);
                    throw error;
                }).finally(() => {
                    this.$wait.end('GETTING_CONTRACTS')
                })
        },
        methods: {
            toDate(timestamp) {
                return moment(timestamp).local().format('YYYY-MM-DD');
            },
            openChoosingInstructorForContract(contract_id) {
                this.updateInstructorForm.contract_id = contract_id;
                this.choosingInstructor = true;
            },
            updateInstructor(contract) {
                this.updateInstructorForm.put(route('back.companies.contracts.update', {company_id: this.companyId, contract: contract.id}), {
                    preserveScroll: true,
                    preserveState: true,
                }).then(response => {
                    this.choosingInstructor = null
                })
            },
            toggleChoosingInstructor() {
                this.choosingInstructor = ! this.choosingInstructor;
            }
        }
    }
</script>

<style scoped>

</style>
