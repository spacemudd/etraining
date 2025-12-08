<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'settings', link: route('back.settings')},
                    {title: 'resignation-email-settings'},
                ]"
            ></breadcrumb-container>

            <div class="mt-4">
                <div class="md:grid md:grid-cols-3 md:gap-6">
                    <div class="md:col-span-1">
                        <div class="px-4 sm:px-0">
                            <h3 class="text-lg font-medium text-gray-900">
                                {{ $t('words.resignation-email-settings') }}
                            </h3>
                            <p class="mt-1 text-sm text-gray-600">
                                {{ $t('words.manage-default-emails-for-resignations') }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-5 md:mt-0 md:col-span-2">
                        <form @submit.prevent="updateSettings">
                            <div class="shadow overflow-hidden sm:rounded-md">
                                <div class="px-4 py-5 bg-white sm:p-6">
                                    <div class="grid grid-cols-6 gap-6">
                                        <div class="col-span-6 sm:col-span-6">
                                            <jet-label for="default_cc_emails" :value="$t('words.default-cc-emails')" />
                                            <jet-input
                                                id="default_cc_emails"
                                                type="text"
                                                class="mt-1 block w-full"
                                                v-model="form.default_cc_emails"
                                                :placeholder="$t('words.enter-emails-separated-by-commas')"
                                            />
                                            <p class="mt-1 text-sm text-gray-500">
                                                {{ $t('words.default-cc-emails-help') }}
                                            </p>
                                            <jet-input-error :message="form.error('default_cc_emails')" class="mt-2" />
                                        </div>

                                        <div class="col-span-6 sm:col-span-6">
                                            <jet-label for="default_bcc_emails" :value="$t('words.default-bcc-emails')" />
                                            <jet-input
                                                id="default_bcc_emails"
                                                type="text"
                                                class="mt-1 block w-full"
                                                v-model="form.default_bcc_emails"
                                                :placeholder="$t('words.enter-emails-separated-by-commas')"
                                            />
                                            <p class="mt-1 text-sm text-gray-500">
                                                {{ $t('words.default-bcc-emails-help') }}
                                            </p>
                                            <jet-input-error :message="form.error('default_bcc_emails')" class="mt-2" />
                                        </div>
                                    </div>
                                </div>

                                <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                                    <jet-button :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                        {{ $t('words.save') }}
                                    </jet-button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import JetInput from '@/Jetstream/Input'
import JetInputError from '@/Jetstream/InputError'
import JetButton from '@/Jetstream/Button'
import JetLabel from '@/Jetstream/Label'
import BreadcrumbContainer from "@/Components/BreadcrumbContainer"

export default {
    props: ['default_cc_emails', 'default_bcc_emails'],

    components: {
        AppLayout,
        JetInput,
        JetInputError,
        JetButton,
        JetLabel,
        BreadcrumbContainer,
    },

    data() {
        return {
            form: this.$inertia.form({
                default_cc_emails: this.default_cc_emails || '',
                default_bcc_emails: this.default_bcc_emails || '',
            }, {
                resetOnSuccess: false,
            })
        }
    },

    watch: {
        default_cc_emails(newVal) {
            this.form.default_cc_emails = newVal || '';
        },
        default_bcc_emails(newVal) {
            this.form.default_bcc_emails = newVal || '';
        },
    },

    mounted() {
        // Sync form with props on mount
        this.form.default_cc_emails = this.default_cc_emails || '';
        this.form.default_bcc_emails = this.default_bcc_emails || '';
    },

    methods: {
        updateSettings() {
            this.form.put(route('back.settings.resignation.update'), {
                onSuccess: () => {
                    // Success message will be shown by the controller
                },
            })
        },
    }
}
</script> 