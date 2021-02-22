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
        <!-- Header -->
        <div class="flex justify-between items-center">
            <p class="text-gray-500 text-sm font-semibold">{{ $t('words.view-all') }} ({{ contracts.length }})</p>
            <inertia-link v-can="'create-company-contracts'"
                          :href="`/back/companies/${companyId}/contracts/create`"
                          class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150">
                {{ $t('words.add-new-contract') }}
            </inertia-link>
        </div>

        <!-- All contracts -->
        <Skeleton class="mt-3 block" height="150px" v-if="$wait.is('GETTING_CONTRACTS')" />
        <template v-else>
            <div v-if="contracts.length===0">
                <empty-slate class="border-2 rounded-lg mt-3">
                    <template #actions>
                        <inertia-link :href="`/back/companies/${companyId}/contracts/create`" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150">
                            {{ $t('words.add-new-contract') }}
                        </inertia-link>
                    </template>
                </empty-slate>
            </div>
            <div class="bg-white rounded-lg my-5 p-5 shadow" v-for="contract in contracts" :key="contract.id">
                <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-6 gap-12">
                    <div class="col-span-6 md:col-span-3">
                        <table class="table text-sm w-full">
                            <colgroup>
                                <col class="w-1/2">
                            </colgroup>
                            <tbody>
                            <tr>
                                <td class="font-semibold">{{ $t('words.contract-number') }}</td>
                                <td class="text-gray-700">{{ contract.reference_number }}</td>
                            </tr>
                            <tr>
                                <td class="font-semibold">{{ $t('words.contract_starts_at') }}</td>
                                <td class="text-gray-700">{{ toDate(contract.contract_starts_at) }}</td>
                            </tr>
                            <tr>
                                <td class="font-semibold">{{ $t('words.contract-end-date') }}</td>
                                <td class="text-gray-700">
                                    <span v-if="contract.contract_ends_at">{{ toDate(contract.contract_ends_at) }}</span>
                                    <span v-else>-</span>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <inertia-link v-f="contract" class="bg-gray-500 h-10 text-white text-sm rounded-sm mt-2 flex justify-center items-center"
                           :href="route('back.companies.contracts.show', {company_id: contract.company_id, contract: contract.id})">
                            <span class="inline-block">
                                {{ $t('words.view') }}
                            </span>
                        </inertia-link>
                    </div>
                    <div class="col-span-6 md:col-span-3">
                        <!-- Attaching instructors -->
                        <div class="w-full h-full border-2 border-gray-100 rounded p-2 items-center flex-col">
                            <div v-if="contract.instructors.length">
                                <div v-for="instructor in contract.instructors" :key="instructor.id" class="flex py-2 justify-between">
                                    <div class="flex flex-grid gap-2">
                                        <div><button @click="unlinkInstructor(instructor.id, contract.id)" class="bg-red-500 px-2 rounded text-white inline-block">{{ $t('words.delete') }}</button></div>
                                        <div>{{ instructor.name }} ({{ instructor.trainees_count }})</div>
                                    </div>
                                    <button class="text-sm bg-gray-200 text-black px-3 py-1 rounded block right-0"
                                            @click="openChoosingTrainees(instructor)"
                                            :title="$t('words.attach-trainees-help')">
                                        {{ $t('words.attach-trainees') }}
                                    </button>
                                </div>
                                <hr class="border-2 w-full">
                            </div>
                            <button class="text-sm bg-green-600 text-white px-3 py-1 rounded mx-auto block mt-5"
                                    @click="openChoosingInstructorForContract(contract.id)"
                                    :title="$t('words.attach-instructors-help')">
                                {{ $t('words.attach-instructors') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <portal to="app-modal-container">
            <modal name="selectingTraineesModal" classes="force-overflow-auto">
                <div class="bg-white block h-5 p-10">
                    <h1 class="text-lg font-bold">{{ $t('words.attach-trainees') }}</h1>
                    <div>
                        <div class="mt-5" v-if="treeSelectOptions.length">
                            <treeselect v-model="selectingTraineesForm.trainees"
                                        value-consists-of="BRANCH_PRIORITY"
                                        name="instructors"
                                        :multiple="true"
                                        :show-count="true"
                                        :options="treeSelectOptions"
                                        :normalizer="normalizer"
                                        :placeholder="$t('words.please-select')"
                                        :no-children-text="$t('words.nothing-is-here')"
                                        :no-results-text="$t('words.nothing-is-here')"
                            >
                            </treeselect>
                        </div>
                    </div>

                    <div class="mt-5">
                        <jet-secondary-button @click.native="toggleChoosingTrainees">
                            {{ $t('words.cancel') }}
                        </jet-secondary-button>

                        <jet-button class="rtl:mr-5 ltr:ml-5" @click.native="assignInstructorsTrainees" :class="{ 'opacity-25': updateInstructorForm.processing }" :disabled="updateInstructorForm.processing">
                            {{ $t('words.save') }}
                        </jet-button>
                    </div>
                </div>
            </modal>

            <modal name="selectingInstructorModal">
                <div class="bg-white block h-5 p-10">
                    <h1 class="text-lg font-bold">{{ $t('words.attach-instructors') }}</h1>

                    <div class="mt-5">
                        <jet-label class="mb-2" for="instructor_id" :value="$t('words.instructor')" />
                        <div class="relative">
                            <select class="block appearance-none w-full border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                    v-model="updateInstructorForm.instructor_id"
                                    required
                                    id="instructor_id">
                                <option v-for="instructor in instructors" :key="instructor.id" :value="instructor.id">{{ instructor.name }}</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                            </div>
                        </div>
                        <jet-input-error :message="updateInstructorForm.error('instructor_id')" class="mt-2" />
                    </div>

                    <div class="mt-5">
                        <jet-secondary-button @click.native="toggleChoosingInstructor">
                            {{ $t('words.cancel') }}
                        </jet-secondary-button>

                        <jet-button class="rtl:mr-5 ltr:ml-5" @click.native="attachInstructor" :class="{ 'opacity-25': updateInstructorForm.processing }" :disabled="updateInstructorForm.processing">
                            {{ $t('words.save') }}
                        </jet-button>
                    </div>
                </div>
            </modal>
        </portal>

    </div>
</template>

<script>
    import JetButton from '@/Jetstream/Button';
    import { Skeleton } from 'vue-loading-skeleton';
    import Logrocket from 'logrocket';
    import EmptySlate from "@/Components/EmptySlate";
    import 'vue-js-modal/dist/styles.css';
    import JetLabel from '@/Jetstream/Label';
    import JetSecondaryButton from '@/Jetstream/SecondaryButton';
    import JetInputError from '@/Jetstream/InputError';
    import Treeselect from '@riophae/vue-treeselect';
    import '@riophae/vue-treeselect/dist/vue-treeselect.css'

    export default {
        components: {
            JetButton,
            Skeleton,
            EmptySlate,
            JetLabel,
            JetSecondaryButton,
            JetInputError,
            Treeselect,
        },
        props: ['companyId', 'instructors'],
        name: "CompanyContractsPagination",
        data() {
            return {
                selectingTraineesForm: this.$inertia.form({
                    instructor_id: null,
                    trainees: null,
                }, {
                    resetOnSuccess: true,
                    bag: 'selectingTraineesForm',
                }),
                updateInstructorForm: this.$inertia.form({
                    instructor_id: null,
                    contract_id: null,
                }, {
                    resetOnSuccess: false,
                    bag: 'updateInstructorForm',
                }),
                contracts: [],
                treeSelectOptions: [],
                availableInstructorsForThisCompany: [],
                normalizer: (node) => {
                    return {
                        id: node.id,
                        label: node.name_selectable,
                        children: node.trainees,
                    }
                },
            }
        },
        mounted() {
            this.getContracts();
            this.getInstructorGroups();
        },
        methods: {
            // This actually load the instructors and their trainees.
            // We need that when we call 'openChoosingTrainees', the function makes sure
            // that if all the trainees under a group is selected, then add the group ID to the selection too.
            getContracts() {
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
            getInstructorGroups() {
                axios.get(route('back.trainee-groups.index'), {
                        params: {
                            load_trainees: true,
                        }
                    })
                    .then(response => {
                        let vm = this;
                        _.forEach(response.data, function(group) {
                            vm.treeSelectOptions.push(group);
                        });
                    })
                    .catch(error => {
                        throw error;
                    })
                    .finally(() => {
                        this.loading = false;
                    })
            },
            toDate(timestamp) {
                return moment(timestamp).local().format('YYYY-MM-DD');
            },
            openChoosingInstructorForContract(contract_id) {
                this.$modal.show('selectingInstructorModal');
                this.updateInstructorForm.instructor_id = null;
                this.updateInstructorForm.contract_id = contract_id;
            },
            attachInstructor() {
                axios.post(route('back.company-contracts.attach-instructor'), {
                    company_contract_id: this.updateInstructorForm.contract_id,
                    instructor_id: this.updateInstructorForm.instructor_id,
                }).then(response => {
                    this.$modal.toggle('selectingInstructorModal');
                    this.getContracts();
                })
            },
            assignInstructorsTrainees() {
                this.selectingTraineesForm.post(route('back.trainees.assign-instructor'));
                this.toggleChoosingTrainees();
                this.getContracts();
            },
            toggleChoosingInstructor() {
                this.$modal.toggle('selectingInstructorModal');
            },
            openChoosingTrainees(instructor) {
                this.selectingTraineesForm.instructor_id = instructor.id;
                let traineeIds = [];
                _.forEach(instructor.trainees, function(trainee) {
                    traineeIds.push(trainee.id);
                })
                this.selectingTraineesForm.trainees = traineeIds;
                this.toggleChoosingTrainees();
            },
            toggleChoosingTrainees() {
                this.$modal.toggle('selectingTraineesModal');
            },
            unlinkInstructor(instructorId, contractId) {
                axios.post(route('back.company-contracts.detach-instructor'), {
                    company_contract_id: contractId,
                    instructor_id: instructorId,
                }).then(response => {
                    this.getContracts();
                }).catch(error => {
                    throw error;
                }).finally(() => {
                })
            },
            deleteContract(contractId) {
                if (confirm(this.$t('words.are-you-sure'))) {
                    axios.delete(route('back.companies.contracts.destroy', {company_id: this.companyId, contract: contractId}))
                        .then(response => {
                            this.getContracts();
                        })
                }
            }
        }
    }
</script>

<style scoped>

</style>
