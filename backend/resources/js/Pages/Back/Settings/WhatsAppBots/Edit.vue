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
        <div class="container px-6 mx-auto pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'settings', link: route('back.settings')},
                    {title: 'whatsapp-bots', link: route('back.settings.whatsapp-bots.index')},
                    {title: 'edit', link: route('back.settings.whatsapp-bots.edit', workflow.id)},
                ]"
            ></breadcrumb-container>

            <div class="bg-white rounded-lg shadow-lg p-4 mb-4 flex flex-wrap items-center gap-3 justify-between">
                <div class="flex items-center gap-3 flex-wrap">
                    <input v-model="form.name" type="text" class="border rounded px-3 py-2 text-sm" />
                    <label class="text-sm flex items-center gap-2">
                        <input v-model="form.is_active" type="checkbox" />
                        {{ $t('words.active') }}
                    </label>
                    <button type="button" class="btn btn-secondary text-sm" @click="saveMeta">
                        {{ $t('words.save') }}
                    </button>
                </div>
                <button type="button" class="btn btn-primary" @click="saveGraph" :disabled="saving">
                    {{ saving ? $t('words.saving') : $t('words.save-workflow') }}
                </button>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
                <div class="bg-white rounded-lg shadow-lg p-4">
                    <h4 class="font-semibold text-gray-700 mb-3">{{ $t('words.node-palette') }}</h4>
                    <button
                        v-for="nodeType in node_types"
                        :key="nodeType.type"
                        type="button"
                        class="w-full mb-2 text-left border rounded px-3 py-2 text-sm hover:bg-gray-50"
                        @click="addNode(nodeType.type)"
                    >
                        {{ nodeType.label }}
                    </button>
                    <p class="text-xs text-gray-500 mt-4">{{ $t('words.whatsapp-bot-editor-hint') }}</p>
                </div>

                <div class="lg:col-span-2 bg-white rounded-lg shadow-lg overflow-hidden" style="min-height: 560px;">
                    <div ref="drawflow" id="whatsapp-bot-drawflow" class="w-full h-full" style="height: 560px;"></div>
                </div>

                <div class="bg-white rounded-lg shadow-lg p-4">
                    <h4 class="font-semibold text-gray-700 mb-3">{{ $t('words.node-settings') }}</h4>
                    <div v-if="!selectedNode" class="text-sm text-gray-400">
                        {{ $t('words.select-node-to-edit') }}
                    </div>
                    <div v-else class="space-y-3 text-sm">
                        <div>
                            <label class="block text-gray-600 mb-1">{{ $t('words.type') }}</label>
                            <div class="font-medium">{{ selectedNode.type }}</div>
                        </div>

                        <div v-if="['send_message', 'assign_agent', 'end', 'wait_input', 'buttons'].includes(selectedNode.type)">
                            <label class="block text-gray-600 mb-1">{{ $t('words.message') }}</label>
                            <textarea
                                v-model="selectedNode.data.body"
                                rows="4"
                                class="w-full border rounded px-2 py-1"
                                @input="syncSelectedNode"
                            ></textarea>
                            <p class="text-xs text-gray-400 mt-1">{{ $t('words.whatsapp-bot-tags-hint') }}</p>
                        </div>

                        <div v-if="selectedNode.type === 'wait_input'">
                            <label class="block text-gray-600 mb-1">{{ $t('words.match-mode') }}</label>
                            <select v-model="selectedNode.data.match" class="w-full border rounded px-2 py-1" @change="syncSelectedNode">
                                <option value="any">{{ $t('words.any-reply') }}</option>
                                <option value="keywords">{{ $t('words.keywords') }}</option>
                            </select>
                            <div v-if="selectedNode.data.match === 'keywords'" class="mt-2 space-y-2">
                                <div
                                    v-for="(keyword, index) in selectedNode.data.keywords"
                                    :key="'kw-' + index"
                                    class="flex gap-2"
                                >
                                    <input
                                        v-model="keyword.keyword"
                                        type="text"
                                        class="flex-1 border rounded px-2 py-1"
                                        :placeholder="$t('words.keyword')"
                                        @input="syncSelectedNode"
                                    />
                                    <button type="button" class="text-red-500" @click="removeKeyword(index)">×</button>
                                </div>
                                <button type="button" class="text-blue-600" @click="addKeyword">
                                    + {{ $t('words.add-keyword') }}
                                </button>
                            </div>
                        </div>

                        <div v-if="selectedNode.type === 'buttons'">
                            <label class="block text-gray-600 mb-1">{{ $t('words.buttons') }}</label>
                            <div
                                v-for="(button, index) in selectedNode.data.buttons"
                                :key="'btn-' + index"
                                class="flex gap-2 mb-2"
                            >
                                <input
                                    v-model="button.label"
                                    type="text"
                                    class="flex-1 border rounded px-2 py-1"
                                    :placeholder="$t('words.button-label')"
                                    @input="syncSelectedNode"
                                />
                                <button type="button" class="text-red-500" @click="removeButton(index)">×</button>
                            </div>
                            <button type="button" class="text-blue-600" @click="addButton">
                                + {{ $t('words.add-button') }}
                            </button>
                        </div>

                        <div v-if="selectedNode.type === 'condition'">
                            <label class="block text-gray-600 mb-1">{{ $t('words.keyword') }}</label>
                            <input
                                v-model="selectedNode.data.keyword"
                                type="text"
                                class="w-full border rounded px-2 py-1"
                                @input="syncSelectedNode"
                            />
                            <label class="block text-gray-600 mb-1 mt-2">{{ $t('words.match-mode') }}</label>
                            <select v-model="selectedNode.data.mode" class="w-full border rounded px-2 py-1" @change="syncSelectedNode">
                                <option value="contains">{{ $t('words.contains') }}</option>
                                <option value="equals">{{ $t('words.equals') }}</option>
                            </select>
                        </div>

                        <button
                            v-if="selectedNode.type !== 'start'"
                            type="button"
                            class="text-red-600 mt-4"
                            @click="removeSelectedNode"
                        >
                            {{ $t('words.delete-node') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import BreadcrumbContainer from '@/Components/BreadcrumbContainer'
import Drawflow from 'drawflow'
import 'drawflow/dist/drawflow.min.css'

const NODE_OUTPUTS = {
    start: 1,
    send_message: 1,
    wait_input: 3,
    buttons: 3,
    condition: 2,
    assign_agent: 0,
    end: 0,
}

const NODE_LABELS = {
    start: 'Start',
    send_message: 'Send message',
    wait_input: 'Wait for input',
    buttons: 'Buttons',
    condition: 'Condition',
    assign_agent: 'Assign agent',
    end: 'End',
}

function defaultDataForType(type) {
    if (type === 'send_message') {
        return { body: '' }
    }
    if (type === 'wait_input') {
        return { body: '', match: 'any', keywords: [] }
    }
    if (type === 'buttons') {
        return { body: '', buttons: [{ label: 'Option 1' }, { label: 'Option 2' }] }
    }
    if (type === 'condition') {
        return { keyword: '', mode: 'contains' }
    }
    if (type === 'assign_agent') {
        return { body: 'Connecting you to an agent...' }
    }
    if (type === 'end') {
        return { body: '' }
    }
    return {}
}

function nodeHtml(type, data) {
    const title = NODE_LABELS[type] || type
    let preview = ''
    if (data && data.body) {
        preview = `<div style="font-size:11px;margin-top:4px;opacity:.8;max-width:160px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${escapeHtml(data.body)}</div>`
    } else if (type === 'condition' && data && data.keyword) {
        preview = `<div style="font-size:11px;margin-top:4px;opacity:.8;">${escapeHtml(data.keyword)}</div>`
    }
    return `<div style="padding:8px 10px;min-width:140px;"><strong>${title}</strong>${preview}</div>`
}

function escapeHtml(value) {
    return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
}

export default {
    metaInfo: { title: 'Edit WhatsApp Bot' },
    components: {
        AppLayout,
        BreadcrumbContainer,
    },
    props: {
        workflow: {
            type: Object,
            required: true,
        },
        node_types: {
            type: Array,
            default: () => [],
        },
    },
    data() {
        return {
            form: {
                name: this.workflow.name,
                is_active: this.workflow.is_active,
            },
            editor: null,
            selectedNode: null,
            selectedNodeId: null,
            saving: false,
            nodeCounter: 1,
        }
    },
    mounted() {
        this.initEditor()
    },
    beforeDestroy() {
        if (this.editor) {
            this.editor.clear()
        }
    },
    methods: {
        initEditor() {
            const container = this.$refs.drawflow
            this.editor = new Drawflow(container)
            this.editor.reroute = true
            this.editor.start()

            this.editor.on('nodeSelected', (id) => {
                this.selectNode(String(id))
            })
            this.editor.on('nodeUnselected', () => {
                this.selectedNode = null
                this.selectedNodeId = null
            })

            const graph = this.workflow.graph || {}
            if (graph.drawflow && Object.keys(graph.drawflow).length) {
                this.editor.import(graph.drawflow)
                this.nodeCounter = this.maxNodeId() + 1
            } else {
                this.importNormalizedGraph(graph)
            }
        },
        maxNodeId() {
            const data = (((this.editor.export() || {}).drawflow || {}).Home || {}).data || {}
            return Object.keys(data).reduce((max, id) => Math.max(max, parseInt(id, 10) || 0), 0)
        },
        importNormalizedGraph(graph) {
            const nodes = graph.nodes || {}
            const connections = graph.connections || {}
            const ids = Object.keys(nodes)
            if (!ids.length) {
                this.addNode('start', 80, 80)
                return
            }

            const drawflowData = { drawflow: { Home: { data: {} } } }
            ids.forEach((id, index) => {
                const node = nodes[id]
                const type = node.type || 'send_message'
                const data = Object.assign(defaultDataForType(type), node.data || {}, { type })
                const outputsCount = NODE_OUTPUTS[type] || 1
                const inputsCount = type === 'start' ? 0 : 1
                const outputs = {}
                for (let i = 1; i <= outputsCount; i++) {
                    outputs['output_' + i] = { connections: [] }
                }
                const inputs = {}
                for (let i = 1; i <= inputsCount; i++) {
                    inputs['input_' + i] = { connections: [] }
                }
                drawflowData.drawflow.Home.data[id] = {
                    id: parseInt(id, 10) || (index + 1),
                    name: type,
                    data,
                    class: type,
                    html: nodeHtml(type, data),
                    typenode: false,
                    inputs,
                    outputs,
                    pos_x: 80 + (index % 3) * 220,
                    pos_y: 60 + Math.floor(index / 3) * 140,
                }
                this.nodeCounter = Math.max(this.nodeCounter, (parseInt(id, 10) || 0) + 1)
            })

            Object.keys(connections).forEach((fromId) => {
                (connections[fromId] || []).forEach((connection) => {
                    const toId = String(connection.to_node || connection.to || '')
                    const output = connection.from_output || connection.output || 'output_1'
                    if (!toId || !drawflowData.drawflow.Home.data[fromId] || !drawflowData.drawflow.Home.data[toId]) {
                        return
                    }
                    if (!drawflowData.drawflow.Home.data[fromId].outputs[output]) {
                        drawflowData.drawflow.Home.data[fromId].outputs[output] = { connections: [] }
                    }
                    drawflowData.drawflow.Home.data[fromId].outputs[output].connections.push({
                        node: toId,
                        output: 'input_1',
                    })
                    if (!drawflowData.drawflow.Home.data[toId].inputs.input_1) {
                        drawflowData.drawflow.Home.data[toId].inputs.input_1 = { connections: [] }
                    }
                    drawflowData.drawflow.Home.data[toId].inputs.input_1.connections.push({
                        node: fromId,
                        input: output,
                    })
                })
            })

            this.editor.import(drawflowData)
        },
        addNode(type, posX, posY) {
            if (!this.editor) {
                return
            }
            const data = Object.assign(defaultDataForType(type), { type })
            const outputs = NODE_OUTPUTS[type] || 1
            const inputs = type === 'start' ? 0 : 1
            const x = typeof posX === 'number' ? posX : 60 + Math.random() * 300
            const y = typeof posY === 'number' ? posY : 60 + Math.random() * 200
            const id = this.editor.addNode(
                type,
                inputs,
                outputs,
                x,
                y,
                type,
                data,
                nodeHtml(type, data),
                false
            )
            this.nodeCounter = Math.max(this.nodeCounter, parseInt(id, 10) + 1)
            this.selectNode(String(id))
        },
        selectNode(id) {
            const exported = this.editor.export()
            const node = ((((exported || {}).drawflow || {}).Home || {}).data || {})[id]
            if (!node) {
                this.selectedNode = null
                this.selectedNodeId = null
                return
            }
            const data = Object.assign({}, node.data || {})
            if (!data.type) {
                data.type = node.name
            }
            if (data.type === 'wait_input' && !Array.isArray(data.keywords)) {
                data.keywords = []
            }
            if (data.type === 'buttons' && !Array.isArray(data.buttons)) {
                data.buttons = []
            }
            this.selectedNodeId = id
            this.selectedNode = {
                id,
                type: data.type || node.name,
                data,
            }
        },
        syncSelectedNode() {
            if (!this.editor || !this.selectedNodeId || !this.selectedNode) {
                return
            }
            const data = Object.assign({}, this.selectedNode.data, { type: this.selectedNode.type })
            this.editor.updateNodeDataFromId(this.selectedNodeId, data)
            const el = this.editor.container.querySelector('#node-' + this.selectedNodeId + ' .drawflow_content_node')
            if (el) {
                el.innerHTML = nodeHtml(this.selectedNode.type, data)
            }
        },
        addKeyword() {
            if (!this.selectedNode.data.keywords) {
                this.$set(this.selectedNode.data, 'keywords', [])
            }
            this.selectedNode.data.keywords.push({ keyword: '', mode: 'contains' })
            this.syncSelectedNode()
        },
        removeKeyword(index) {
            this.selectedNode.data.keywords.splice(index, 1)
            this.syncSelectedNode()
        },
        addButton() {
            if (!this.selectedNode.data.buttons) {
                this.$set(this.selectedNode.data, 'buttons', [])
            }
            if (this.selectedNode.data.buttons.length >= 3) {
                return
            }
            this.selectedNode.data.buttons.push({ label: '' })
            this.syncSelectedNode()
        },
        removeButton(index) {
            this.selectedNode.data.buttons.splice(index, 1)
            this.syncSelectedNode()
        },
        removeSelectedNode() {
            if (!this.selectedNodeId || !this.editor) {
                return
            }
            this.editor.removeNodeId('node-' + this.selectedNodeId)
            this.selectedNode = null
            this.selectedNodeId = null
        },
        buildNormalizedGraph() {
            const exported = this.editor.export()
            const data = ((((exported || {}).drawflow || {}).Home || {}).data || {})
            const nodes = {}
            const connections = {}

            Object.keys(data).forEach((id) => {
                const node = data[id]
                const type = (node.data && node.data.type) || node.name
                const nodeData = Object.assign({}, node.data || {})
                delete nodeData.type
                nodes[id] = {
                    id: String(id),
                    type,
                    data: nodeData,
                }

                const outs = node.outputs || {}
                connections[id] = []
                Object.keys(outs).forEach((outputName) => {
                    const list = (outs[outputName] && outs[outputName].connections) || []
                    list.forEach((connection) => {
                        connections[id].push({
                            from_output: outputName,
                            to_node: String(connection.node),
                        })
                    })
                })
            })

            return {
                nodes,
                connections,
                drawflow: exported,
            }
        },
        saveMeta() {
            this.$inertia.put(route('back.settings.whatsapp-bots.update', this.workflow.id), {
                name: this.form.name,
                is_active: this.form.is_active,
            }, {
                preserveScroll: true,
            })
        },
        saveGraph() {
            this.saving = true
            this.$inertia.put(route('back.settings.whatsapp-bots.graph', this.workflow.id), {
                graph: this.buildNormalizedGraph(),
            }, {
                preserveScroll: true,
                onFinish: () => {
                    this.saving = false
                },
            })
        },
    },
}
</script>

<style>
#whatsapp-bot-drawflow {
    background:
        linear-gradient(90deg, rgba(0,0,0,0.03) 1px, transparent 1px),
        linear-gradient(rgba(0,0,0,0.03) 1px, transparent 1px);
    background-size: 20px 20px;
}
#whatsapp-bot-drawflow .drawflow .drawflow-node {
    background: #fff;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    color: #111827;
    min-height: auto;
    padding: 0;
}
#whatsapp-bot-drawflow .drawflow .drawflow-node.selected {
    border-color: #2563eb;
    box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.2);
}
#whatsapp-bot-drawflow .drawflow .drawflow-node.start {
    border-color: #059669;
}
#whatsapp-bot-drawflow .drawflow .drawflow-node.end {
    border-color: #dc2626;
}
</style>
