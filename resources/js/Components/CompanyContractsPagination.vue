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
        <div class="bg-white rounded-lg my-5 p-5 shadow" v-for="contract in contracts">
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
                                <span v-if="contract.contact_ends_at">{{ toDate(contract.contract_ends_at) }}</span>
                                <span v-else>-</span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-span-2">
                    <!-- Attaching trainees -->
                    <div class="w-full h-full border-2 border-gray-100 rounded p-2">
                        <button class="text-sm bg-green-600 text-white px-3 py-1 rounded mx-auto block"
                                :title="$t('words.attach-trainees-help')">
                            {{ $t('words.attach-trainees') }}
                        </button>
                    </div>
                </div>
                <div class="col-span-2">
                    <div class="w-full h-full border-2 border-gray-100 rounded p-2">
                        <button class="text-sm bg-green-600 text-white px-3 py-1 rounded mx-auto block"
                                :title="$t('words.attach-trainers-help')">
                            {{ $t('words.attach-trainers') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import JetButton from '@/Jetstream/Button';

    export default {
        components: {
            JetButton,
        },
        props: ['companyId'],
        name: "CompanyContractsPagination",
        data() {
            return {
                contracts: [],
            }
        },
        mounted() {
            console.log('Mounted');
            axios.get('/back/companies/'+this.companyId+'/contracts')
            .then(response => {
                this.contracts = response.data;
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
