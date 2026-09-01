<template>
    <span class="hidden"></span>
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
            errorMessage: '',
        };
    },
    methods: {
        isWindowOpen() {
            return !!liveDialerPopup();
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
                return false;
            }

            dialerPopup = existing;

            try {
                existing.focus();
            } catch (error) {
                // ignore
            }

            if (this.isPopupAlreadyOnMaqsam(existing)) {
                this.errorMessage = '';
                return true;
            }

            this.connecting = true;
            this.errorMessage = '';

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
                    return false;
                }

                return true;
            } catch (error) {
                this.errorMessage = error.response?.data?.message || this.$t('words.caller-maqsam-login-failed');
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

            try {
                if (!this.isWindowOpen()) {
                    const connected = await this.connectDialer();
                    if (!connected) {
                        return false;
                    }
                }

                const response = await axios.post(route('caller.dial'), {
                    phone,
                    email: this.agentEmail,
                });

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
