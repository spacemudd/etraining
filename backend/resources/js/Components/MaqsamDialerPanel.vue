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
                <button
                    type="button"
                    class="text-gray-500 hover:text-gray-800 p-1 rounded border border-gray-200 bg-white flex-shrink-0"
                    @click="$emit('close')"
                    :aria-label="$t('words.cancel')"
                >
                    <ion-icon name="close-outline" class="w-5 h-5"></ion-icon>
                </button>
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
                {{ $t('words.caller-popup-help') }}
            </p>

            <div class="flex-1 min-h-0 bg-gray-50 p-4 flex flex-col items-center justify-center text-center space-y-3">
                <ion-icon name="call-outline" class="w-10 h-10 text-green-700"></ion-icon>
                <p class="text-sm text-gray-700">
                    {{ $t('words.caller-popup-help') }}
                </p>
                <button
                    type="button"
                    class="text-sm px-3 py-2 rounded-md bg-green-600 hover:bg-green-700 text-white font-medium disabled:opacity-50"
                    :disabled="connecting || !configured"
                    @click="connectDialer"
                >
                    {{ connecting
                        ? $t('words.caller-connecting')
                        : (isDialerWindowOpen
                            ? $t('words.caller-focus-window')
                            : $t('words.caller-open-in-window')) }}
                </button>
            </div>
        </aside>
    </div>
</template>

<script>
const DIALER_WINDOW_NAME = 'maqsam-dialer';
const DIALER_WINDOW_FEATURES = 'toolbar=no,menubar=no,width=420,height=720';

let dialerPopup = null;

function liveDialerPopup() {
    try {
        if (dialerPopup && !dialerPopup.closed) {
            return dialerPopup;
        }
    } catch (error) {
        // ignore cross-origin access on closed checks
    }

    return null;
}

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
        isDialerWindowOpen() {
            return this.connected;
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
    mounted() {
        window.addEventListener('resize', this.updateViewportWidth);
        this.connected = !!liveDialerPopup();
    },
    beforeDestroy() {
        window.removeEventListener('resize', this.updateViewportWidth);
    },
    methods: {
        updateViewportWidth() {
            this.viewportWidth = window.innerWidth;
        },
        isPopupAlreadyOnMaqsam(popup) {
            if (!popup) {
                return false;
            }

            try {
                if (popup.closed) {
                    return false;
                }

                const href = popup.location.href || '';

                return href !== '' && href !== 'about:blank';
            } catch (error) {
                return true;
            }
        },
        async connectDialer() {
            if (!this.configured) {
                return false;
            }

            if (this.connecting) {
                while (this.connecting) {
                    await new Promise((resolve) => setTimeout(resolve, 100));
                }

                return !!liveDialerPopup();
            }

            const existing = liveDialerPopup() || window.open('', DIALER_WINDOW_NAME, DIALER_WINDOW_FEATURES);

            if (!existing) {
                this.errorMessage = this.$t('words.caller-popup-blocked');
                this.connected = false;
                return false;
            }

            dialerPopup = existing;

            try {
                existing.focus();
            } catch (error) {
                // ignore
            }

            if (this.isPopupAlreadyOnMaqsam(existing)) {
                this.connected = true;
                this.errorMessage = '';
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

                try {
                    if (existing.closed) {
                        this.errorMessage = this.$t('words.caller-popup-blocked');
                        return false;
                    }

                    existing.location.href = response.data.url;
                } catch (error) {
                    this.errorMessage = this.$t('words.caller-popup-blocked');
                    this.connected = false;
                    return false;
                }

                this.connected = true;
                return true;
            } catch (error) {
                this.errorMessage = error.response?.data?.message || this.$t('words.caller-maqsam-login-failed');
                this.connected = false;
                return false;
            } finally {
                this.connecting = false;
            }
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
