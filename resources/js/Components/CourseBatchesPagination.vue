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
            <inertia-link :href="`/back/companies/${companyId}/contracts/create`" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150">
                {{ $t('words.add-new-batch') }}
            </inertia-link>
        </div>

        <!-- All contracts -->
        <Skeleton class="mt-3 block" height="150px" v-if="$wait.is('GETTING_CONTRACTS')" />
        <template v-else>
            <div v-if="contracts.length===0">
                <empty-slate class="border-2 rounded-lg mt-3">
                    <template #actions>
                        <inertia-link :href="`/back/companies/${companyId}/contracts/create`" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150">
                            {{ $t('words.add-new-batch') }}
                        </inertia-link>
                    </template>
                </empty-slate>
            </div>
            <div class="bg-white rounded-lg my-5 p-5 shadow" v-for="contract in contracts" :key="contract.id">
                <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-6 gap-12">
                    <div class="col-span-2">
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
                        <a v-if="contract.has_attachments" target="_blank" class="bg-gray-500 h-10 text-white text-sm rounded-sm mt-2 flex justify-center items-center" :href="route('back.companies.contracts.attachments', {company_id: contract.company_id, contract_id: contract.id})">
                            <span class="inline-block">
                                {{ $t('words.download-scan') }}
                            </span>
                        </a>
                    </div>
                    <div class="col-span-2">
                        <!-- Attaching trainees -->
                        <div class="w-full h-full border-2 border-gray-100 rounded p-2 flex justify-center items-center">
                            <button class="text-sm bg-green-600 text-white px-3 py-1 rounded mx-auto block"
                                    :title="$t('words.attach-trainees-help')">
                                {{ $t('words.attach-trainees') }}
                            </button>
                        </div>
                    </div>
                    <div class="col-span-2">
                        <div class="w-full h-full border-2 border-gray-100 rounded p-2 flex justify-center items-center">
                            <button class="text-sm bg-green-600 text-white px-3 py-1 rounded mx-auto block"
                                    @click="openChoosingInstructorForContract(contract.id)"
                                    :title="$t('words.attach-instructors-help')">
                                {{ $t('words.attach-instructors') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <jet-dialog-modal :show="toggleChoosingInstructor" @close="toggleChoosingInstructor">
            <template #title>
                {{ $t('words.instructor') }}
            </template>

            <template #content>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div v-for="instructor in instructors">
                        <label class="flex items-center">
                            <input type="checkbox" class="form-checkbox" :value="instructor.id" v-model="updateInstructorForm.instructor_id">
                            <span class="ml-2 text-sm text-gray-600">{{ instructor.name_ar }}</span>
                        </label>
                    </div>
                </div>
            </template>

            <template #footer>
                <jet-secondary-button @click.native="toggleChoosingInstructor">
                    {{ $t('words.cancel') }}
                </jet-secondary-button>

                <jet-button class="ml-2" @click.native="updateInstructor" :class="{ 'opacity-25': updateInstructorForm.processing }" :disabled="updateInstructorForm.processing">
                    {{ $t('words.save') }}
                </jet-button>
            </template>
        </jet-dialog-modal>

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
