<template>
    <div>
        <div
            v-if="open"
            class="fixed inset-0 z-40 bg-black bg-opacity-40 md:hidden"
            @click="$emit('close')"
        ></div>

        <aside
            v-if="open"
            class="fixed z-50 bg-white shadow-xl flex flex-col overflow-hidden"
            :class="isMobile
                ? 'top-0 right-0 bottom-0 left-0'
                : 'top-0 right-0 bottom-0 w-[380px] border-l border-gray-200'"
        >
            <div class="px-3 py-2 border-b flex items-center justify-between gap-2 flex-shrink-0">
                <div class="min-w-0">
                    <h3 class="text-xs font-semibold text-gray-800 truncate">
                        {{ $t('words.caller-dialer') }}
                    </h3>
                    <p class="text-[11px] text-gray-500 truncate">
                        {{ statusLabel }}
                    </p>
                </div>
                <div class="flex items-center gap-1 flex-shrink-0">
                    <button
                        type="button"
                        class="text-xs px-2 py-1 rounded-md border border-gray-200 text-gray-700 hover:bg-gray-50 disabled:opacity-50"
                        :disabled="connecting || !configured"
                        @click="connectDialer"
                    >
                        {{ connecting ? $t('words.caller-connecting') : $t('words.caller-reconnect') }}
                    </button>
                    <button
                        type="button"
                        class="text-xs px-2 py-1 rounded-md border border-gray-200 text-gray-700 hover:bg-gray-50 disabled:opacity-50"
                        :disabled="!dialerUrl"
                        @click="openDialerWindow"
                    >
                        {{ $t('words.caller-open-in-window') }}
                    </button>
                    <button
                        type="button"
                        class="text-gray-500 hover:text-gray-800 p-1 rounded border border-gray-200 bg-white"
                        @click="$emit('close')"
                        :aria-label="$t('words.cancel')"
                    >
                        <ion-icon name="close-outline" class="w-5 h-5"></ion-icon>
                    </button>
                </div>
            </div>

            <p
                v-if="!configured"
                class="px-3 py-2 text-xs text-yellow-800 bg-yellow-50 border-b border-yellow-100"
            >
                {{ $t('words.caller-maqsam-not-configured') }}
                <inertia-link
                    :href="route('back.settings.maqsam-system.index')"
                    class="font-medium underline"
                >
                    {{ $t('words.maqsam-system') }}
                </inertia-link>
            </p>
            <p
                v-else-if="errorMessage"
                class="px-3 py-2 text-xs text-red-700 bg-red-50 border-b border-red-100"
            >
                {{ errorMessage }}
            </p>
            <p
                v-else-if="successMessage"
                class="px-3 py-2 text-xs text-green-700 bg-green-50 border-b border-green-100"
            >
                {{ successMessage }}
            </p>
            <p
                v-else
                class="px-3 py-1.5 text-[11px] text-gray-500 bg-gray-50 border-b"
            >
                {{ $t('words.caller-must-be-online') }}
            </p>

            <div class="flex-1 min-h-0 bg-gray-50 relative">
                <div
                    v-if="connecting && !dialerUrl"
                    class="absolute inset-0 flex items-center justify-center text-xs text-gray-500"
                >
                    {{ $t('words.caller-connecting') }}
                </div>
                <iframe
                    v-if="dialerUrl"
                    :key="iframeKey"
                    :src="dialerUrl"
                    class="h-full w-full border-0"
                    allow="microphone"
                    title="Maqsam Dialer"
                ></iframe>
            </div>
        </aside>
    </div>
</template>

<script>
export default {
    name: 'MaqsamDialerPanel',
    props: {
        open: {
            type: Boolean,
            default: false,
        },
        configured: {
            type: Boolean,
            default: false,
        },
        agentEmail: {
            type: String,
            default: '',
        },
    },
    data() {
        return {
            dialerUrl: '',
            iframeKey: 0,
            connecting: false,
            dialing: false,
            connected: false,
            errorMessage: '',
            successMessage: '',
            viewportWidth: typeof window !== 'undefined' ? window.innerWidth : 1024,
        };
    },
    computed: {
        isMobile() {
            return this.viewportWidth < 768;
        },
        statusLabel() {
            if (!this.configured) {
                return this.$t('words.caller-maqsam-not-configured');
            }
            if (this.connecting) {
                return this.$t('words.caller-connecting');
            }
            if (this.connected) {
                return this.$t('words.caller-dialer-connected');
            }
            return this.$t('words.caller-dialer-offline');
        },
    },
    watch: {
        open(isOpen) {
            if (isOpen && this.configured && !this.dialerUrl && !this.connecting) {
                this.connectDialer();
            }
        },
    },
    mounted() {
        window.addEventListener('resize', this.updateViewportWidth);
        if (this.open && this.configured) {
            this.connectDialer();
        }
    },
    beforeDestroy() {
        window.removeEventListener('resize', this.updateViewportWidth);
    },
    methods: {
        updateViewportWidth() {
            this.viewportWidth = window.innerWidth;
        },
        async connectDialer() {
            if (!this.configured) {
                return false;
            }

            if (this.connecting) {
                while (this.connecting) {
                    await new Promise((resolve) => setTimeout(resolve, 100));
                }

                return this.connected;
            }

            if (this.connected && this.dialerUrl) {
                return true;
            }

            this.connecting = true;
            this.errorMessage = '';
            this.successMessage = '';
            this.connected = false;

            try {
                const response = await axios.post(route('caller.connect'), {
                    email: this.agentEmail,
                });

                this.dialerUrl = response.data.url;
                this.iframeKey += 1;
                this.connected = true;
                return true;
            } catch (error) {
                this.errorMessage = error.response?.data?.message || this.$t('words.caller-maqsam-login-failed');
                this.dialerUrl = '';
                this.connected = false;
                return false;
            } finally {
                this.connecting = false;
            }
        },
        openDialerWindow() {
            if (!this.dialerUrl) {
                return;
            }

            window.open(this.dialerUrl, 'maqsam-dialer', 'toolbar=no,menubar=no,width=420,height=720');
        },
        async dial(phone) {
            if (!phone || this.dialing) {
                return false;
            }

            this.dialing = true;
            this.errorMessage = '';
            this.successMessage = '';

            try {
                const connected = await this.connectDialer();
                if (!connected) {
                    this.errorMessage = this.errorMessage || this.$t('words.caller-maqsam-login-failed');
                    return false;
                }

                const response = await axios.post(route('caller.dial'), {
                    phone,
                    email: this.agentEmail,
                });

                this.successMessage = response.data.message;
                return true;
            } catch (error) {
                this.errorMessage = error.response?.data?.message || this.$t('words.caller-dial-failed');
                return false;
            } finally {
                this.dialing = false;
            }
        },
    },
};
</script>
