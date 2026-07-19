<template>
    <div>
        <jet-label v-if="label" :value="required ? `${label} *` : label" />

        <div
            class="mt-1 rounded-md border border-gray-300 bg-white focus-within:border-indigo-300 focus-within:ring focus-within:ring-indigo-200 focus-within:ring-opacity-50"
            @click="focusInput"
        >
            <ul v-if="emails.length" class="flex flex-wrap gap-2 p-2 border-b border-gray-100" dir="ltr">
                <li
                    v-for="(email, index) in emails"
                    :key="email + '-' + index"
                    class="inline-flex items-center max-w-full gap-1 rounded-md bg-gray-100 px-2 py-1 text-sm text-gray-800"
                    :class="{ 'ring-2 ring-indigo-400 bg-indigo-50': editingIndex === index }"
                >
                    <span class="truncate" :title="email">{{ email }}</span>
                    <button
                        type="button"
                        class="shrink-0 rounded p-0.5 text-gray-500 hover:bg-gray-200 hover:text-indigo-600"
                        :title="$t('words.edit')"
                        @click.stop="startEdit(index)"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </button>
                    <button
                        type="button"
                        class="shrink-0 rounded p-0.5 text-gray-500 hover:bg-red-100 hover:text-red-600"
                        :title="$t('words.remove')"
                        @click.stop="removeEmail(index)"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </li>
            </ul>

            <div class="flex items-stretch gap-2 p-2">
                <input
                    ref="emailInput"
                    type="email"
                    dir="ltr"
                    class="min-w-0 flex-1 border-0 bg-transparent p-1 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-0"
                    :placeholder="placeholder || $t('words.type-email-and-press-enter')"
                    v-model="draft"
                    @keydown.enter.prevent="commitDraft"
                    @keydown.esc.prevent="cancelEdit"
                    @paste="onPaste"
                    @blur="onBlur"
                />
                <button
                    type="button"
                    class="shrink-0 rounded-md bg-gray-800 px-3 py-1.5 text-xs font-semibold text-white hover:bg-gray-700 disabled:cursor-not-allowed disabled:opacity-40"
                    :disabled="!draft.trim()"
                    @click="commitDraft"
                >
                    {{ editingIndex === null ? $t('words.add') : $t('words.save') }}
                </button>
            </div>
        </div>

        <p v-if="errorMessage" class="mt-1 text-sm text-red-600">{{ errorMessage }}</p>
        <p v-else-if="!emails.length" class="mt-1 text-xs text-gray-400">{{ $t('words.no-emails-added-yet') }}</p>
    </div>
</template>

<script>
import JetLabel from '@/Jetstream/Label'

export default {
    name: 'EmailListInput',

    components: {
        JetLabel,
    },

    props: {
        value: {
            type: [String, Array],
            default: '',
        },
        label: {
            type: String,
            default: '',
        },
        placeholder: {
            type: String,
            default: '',
        },
        required: {
            type: Boolean,
            default: false,
        },
    },

    data() {
        return {
            draft: '',
            editingIndex: null,
            errorMessage: '',
        }
    },

    computed: {
        emails() {
            return this.normalizeEmails(this.value)
        },
    },

    methods: {
        normalizeEmails(value) {
            if (Array.isArray(value)) {
                return value
                    .map((email) => String(email || '').trim())
                    .filter(Boolean)
            }

            if (!value) {
                return []
            }

            return String(value)
                .split(/[,;\n]+/)
                .map((email) => email.trim())
                .filter(Boolean)
        },

        emitEmails(emails) {
            this.$emit('input', emails.join(', '))
        },

        isValidEmail(email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)
        },

        focusInput() {
            this.$refs.emailInput && this.$refs.emailInput.focus()
        },

        startEdit(index) {
            this.editingIndex = index
            this.draft = this.emails[index]
            this.errorMessage = ''
            this.$nextTick(() => this.focusInput())
        },

        cancelEdit() {
            this.editingIndex = null
            this.draft = ''
            this.errorMessage = ''
        },

        removeEmail(index) {
            const emails = [...this.emails]
            emails.splice(index, 1)

            if (this.editingIndex === index) {
                this.cancelEdit()
            } else if (this.editingIndex !== null && this.editingIndex > index) {
                this.editingIndex -= 1
            }

            this.emitEmails(emails)
            this.errorMessage = ''
        },

        commitDraft() {
            const email = this.draft.trim().toLowerCase()

            if (!email) {
                return
            }

            if (!this.isValidEmail(email)) {
                this.errorMessage = this.$t('words.invalid-email-address')
                return
            }

            const emails = [...this.emails]
            const duplicateIndex = emails.findIndex((existing, index) => {
                return existing.toLowerCase() === email && index !== this.editingIndex
            })

            if (duplicateIndex !== -1) {
                this.errorMessage = this.$t('words.email-already-added')
                return
            }

            if (this.editingIndex === null) {
                emails.push(email)
            } else {
                emails.splice(this.editingIndex, 1, email)
            }

            this.emitEmails(emails)
            this.cancelEdit()
        },

        onBlur() {
            // Commit a valid draft when leaving the field, but keep invalid text
            // so the user can fix it without losing what they typed.
            if (!this.draft.trim()) {
                if (this.editingIndex !== null) {
                    this.cancelEdit()
                }
                return
            }

            if (this.isValidEmail(this.draft.trim())) {
                this.commitDraft()
            }
        },

        onPaste(event) {
            const text = (event.clipboardData || window.clipboardData).getData('text') || ''
            const chunks = text
                .split(/[,;\s\n]+/)
                .map((chunk) => chunk.trim().toLowerCase())
                .filter(Boolean)

            if (chunks.length <= 1) {
                return
            }

            event.preventDefault()

            const emails = [...this.emails]
            let added = 0
            let invalid = 0

            chunks.forEach((email) => {
                if (!this.isValidEmail(email)) {
                    invalid += 1
                    return
                }

                if (emails.some((existing) => existing.toLowerCase() === email)) {
                    return
                }

                emails.push(email)
                added += 1
            })

            if (added > 0) {
                this.emitEmails(emails)
            }

            this.draft = ''
            this.editingIndex = null
            this.errorMessage = invalid
                ? this.$t('words.some-emails-were-invalid-and-skipped')
                : ''
        },
    },
}
</script>
