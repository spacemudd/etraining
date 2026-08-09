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
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'settings', link: route('back.settings')},
                    {title: 'whatsapp-bots', link: route('back.settings.whatsapp-bots.index')},
                ]"
            ></breadcrumb-container>

            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <div class="flex flex-wrap justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-700">{{ $t('words.whatsapp-bots') }}</h3>
                    <form @submit.prevent="createWorkflow" class="flex items-center gap-2">
                        <input
                            v-model="newWorkflowName"
                            type="text"
                            class="border rounded px-3 py-2 text-sm"
                            :placeholder="$t('words.workflow-name')"
                            required
                        />
                        <button type="submit" class="btn btn-primary">{{ $t('words.new-workflow') }}</button>
                    </form>
                </div>

                <p class="text-sm text-gray-500 mb-4">{{ $t('words.whatsapp-bots-hint') }}</p>

                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b">
                            <th class="py-3 px-2">{{ $t('words.workflow-name') }}</th>
                            <th class="py-3 px-2">{{ $t('words.status') }}</th>
                            <th class="py-3 px-2">{{ $t('words.connected-number') }}</th>
                            <th class="py-3 px-2"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="workflow in workflows" :key="workflow.id" class="border-b">
                            <td class="py-3 px-2">{{ workflow.name }}</td>
                            <td class="py-3 px-2">
                                <span :class="workflow.is_active ? 'text-green-600' : 'text-gray-400'">
                                    {{ workflow.is_active ? $t('words.active') : $t('words.inactive') }}
                                </span>
                            </td>
                            <td class="py-3 px-2" dir="ltr">
                                {{ workflow.sender ? (workflow.sender.label || workflow.sender.phone) : '—' }}
                            </td>
                            <td class="py-3 px-2 text-right space-x-2">
                                <inertia-link
                                    class="text-sm text-blue-600"
                                    :href="route('back.settings.whatsapp-bots.edit', workflow.id)"
                                >
                                    {{ $t('words.edit') }}
                                </inertia-link>
                                <button type="button" class="text-sm text-red-600" @click="deleteWorkflow(workflow)">
                                    {{ $t('words.delete') }}
                                </button>
                            </td>
                        </tr>
                        <tr v-if="!workflows.length">
                            <td colspan="4" class="py-6 text-center text-gray-400">
                                {{ $t('words.no-workflows-yet') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">{{ $t('words.connected-numbers') }}</h3>
                <p class="text-sm text-gray-500 mb-4">{{ $t('words.whatsapp-bot-sender-hint') }}</p>

                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b">
                            <th class="py-3 px-2">{{ $t('words.number') }}</th>
                            <th class="py-3 px-2">{{ $t('words.label') }}</th>
                            <th class="py-3 px-2">{{ $t('words.assigned-workflow') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="sender in senders" :key="sender.id" class="border-b">
                            <td class="py-3 px-2" dir="ltr">{{ sender.phone }}</td>
                            <td class="py-3 px-2">{{ sender.label || '—' }}</td>
                            <td class="py-3 px-2">
                                <select
                                    class="border rounded px-3 py-2 text-sm w-full max-w-xs"
                                    :value="sender.workflow_id || ''"
                                    @change="assignWorkflow(sender, $event.target.value)"
                                >
                                    <option value="">{{ $t('words.none') }}</option>
                                    <option v-for="workflow in workflows" :key="workflow.id" :value="workflow.id">
                                        {{ workflow.name }}
                                    </option>
                                </select>
                            </td>
                        </tr>
                        <tr v-if="!senders.length">
                            <td colspan="3" class="py-6 text-center text-gray-400">
                                {{ $t('words.no-whatsapp-numbers') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import BreadcrumbContainer from '@/Components/BreadcrumbContainer'

export default {
    metaInfo: { title: 'WhatsApp Bots' },
    components: {
        AppLayout,
        BreadcrumbContainer,
    },
    props: {
        workflows: {
            type: Array,
            default: () => [],
        },
        senders: {
            type: Array,
            default: () => [],
        },
    },
    data() {
        return {
            newWorkflowName: '',
        }
    },
    methods: {
        createWorkflow() {
            this.$inertia.post(route('back.settings.whatsapp-bots.store'), {
                name: this.newWorkflowName,
            }, {
                onSuccess: () => {
                    this.newWorkflowName = ''
                },
            })
        },
        assignWorkflow(sender, workflowId) {
            this.$inertia.put(route('back.settings.whatsapp-bots.senders.assign', sender.id), {
                workflow_id: workflowId || null,
            }, {
                preserveScroll: true,
            })
        },
        deleteWorkflow(workflow) {
            if (!confirm(this.$t('words.whatsapp-bot-delete-confirm'))) {
                return
            }
            this.$inertia.delete(route('back.settings.whatsapp-bots.destroy', workflow.id))
        },
    },
}
</script>
