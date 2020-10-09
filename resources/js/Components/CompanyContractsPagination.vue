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
                        <a target="_blank" class="bg-gray-500 h-10 text-white text-sm rounded-sm mt-2 flex justify-center items-center" :href="route('back.companies.contracts.attachments', {company_id: contract.company_id, contract_id: contract.id})">
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
                                    :title="$t('words.attach-trainers-help')">
                                {{ $t('words.attach-trainers') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>

<script>
    import JetButton from '@/Jetstream/Button';
    import { Skeleton } from 'vue-loading-skeleton';
    import Logrocket from 'logrocket';
    import EmptySlate from "@/Components/EmptySlate";

    export default {
        components: {
            JetButton,
            Skeleton,
            EmptySlate,
        },
        props: ['companyId'],
        name: "CompanyContractsPagination",
        data() {
            return {
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
        }
    }
</script>

<style scoped>

</style>
