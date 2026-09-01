<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'settings', link: route('back.settings')},
                    {title: 'maqsam-system'},
                ]"
            ></breadcrumb-container>

            <div class="mt-4">
                <div class="md:grid md:grid-cols-3 md:gap-6">
                    <div class="md:col-span-1">
                        <div class="px-4 sm:px-0">
                            <h3 class="text-lg font-medium text-gray-900">
                                {{ $t('words.maqsam-system') }}
                            </h3>
                            <p class="mt-1 text-sm text-gray-600">
                                {{ $t('words.maqsam-system-settings-help') }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-5 md:mt-0 md:col-span-2">
                        <form @submit.prevent="updateSettings">
                            <div class="shadow overflow-hidden sm:rounded-md">
                                <div class="px-4 py-5 bg-white sm:p-6">
                                    <div class="grid grid-cols-6 gap-6">
                                        <div class="col-span-6 sm:col-span-6">
                                            <jet-label for="base_url" :value="$t('words.maqsam-system-base-url')" />
                                            <jet-input
                                                id="base_url"
                                                type="text"
                                                dir="ltr"
                                                class="mt-1 block w-full"
                                                v-model="form.base_url"
                                                autocomplete="off"
                                            />
                                            <p class="mt-1 text-sm text-gray-500">
                                                {{ $t('words.maqsam-system-base-url-help') }}
                                            </p>
                                            <jet-input-error :message="form.error('base_url')" class="mt-2" />
                                        </div>

                                        <div class="col-span-6 sm:col-span-6">
                                            <jet-label for="access_key" :value="$t('words.maqsam-system-access-key')" />
                                            <jet-input
                                                id="access_key"
                                                type="password"
                                                dir="ltr"
                                                class="mt-1 block w-full"
                                                v-model="form.access_key"
                                                autocomplete="off"
                                            />
                                            <p class="mt-1 text-sm text-gray-500">
                                                {{ $t('words.maqsam-system-access-key-help') }}
                                            </p>
                                            <jet-input-error :message="form.error('access_key')" class="mt-2" />
                                        </div>

                                        <div class="col-span-6 sm:col-span-6">
                                            <jet-label for="access_token" :value="$t('words.maqsam-system-access-token')" />
                                            <jet-input
                                                id="access_token"
                                                type="password"
                                                dir="ltr"
                                                class="mt-1 block w-full"
                                                v-model="form.access_token"
                                                autocomplete="off"
                                            />
                                            <p class="mt-1 text-sm text-gray-500">
                                                {{ $t('words.maqsam-system-access-token-help') }}
                                            </p>
                                            <jet-input-error :message="form.error('access_token')" class="mt-2" />
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
    props: ['base_url', 'access_key', 'access_token'],

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
                base_url: this.base_url || '',
                access_key: this.access_key || '',
                access_token: this.access_token || '',
            }, {
                resetOnSuccess: false,
            })
        }
    },

    watch: {
        base_url(newVal) {
            this.form.base_url = newVal || '';
        },
        access_key(newVal) {
            this.form.access_key = newVal || '';
        },
        access_token(newVal) {
            this.form.access_token = newVal || '';
        },
    },

    mounted() {
        this.form.base_url = this.base_url || '';
        this.form.access_key = this.access_key || '';
        this.form.access_token = this.access_token || '';
    },

    methods: {
        updateSettings() {
            this.form.put(route('back.settings.maqsam-system.update'));
        },
    }
}
</script>
