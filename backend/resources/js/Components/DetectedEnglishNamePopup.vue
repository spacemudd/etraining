<template>
    <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto" dir="auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

            <div class="inline-block align-bottom bg-white rounded-lg text-center overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:mx-4 sm:text-start w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-2">
                                {{ $t('words.detected-english-name-title') }}
                            </h3>
                            <p class="text-sm text-gray-600 mb-3">
                                {{ $t('words.detected-english-name-message') }}
                            </p>
                            <p class="text-base font-semibold text-gray-900 tracking-wide break-words" dir="ltr">
                                {{ detectedName }}
                            </p>
                            <p class="mt-3 text-sm text-gray-500">
                                {{ $t('words.detected-english-name-confirm') }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                    <button
                        type="button"
                        @click="confirmYes"
                        :disabled="loading"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:w-auto sm:text-sm disabled:opacity-50"
                    >
                        <span v-if="loading">{{ $t('words.loading') }}</span>
                        <span v-else>{{ $t('words.yes') }}</span>
                    </button>
                    <button
                        type="button"
                        @click="confirmNo"
                        :disabled="loading"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm disabled:opacity-50"
                    >
                        {{ $t('words.no') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'DetectedEnglishNamePopup',
    props: {
        show: {
            type: Boolean,
            default: false,
        },
        detectedName: {
            type: String,
            default: '',
        },
        traineeId: {
            type: [String, Number],
            required: true,
        },
    },
    data() {
        return {
            loading: false,
        };
    },
    methods: {
        async confirmYes() {
            if (!this.detectedName || this.loading) {
                return;
            }

            this.loading = true;

            try {
                const response = await axios.post(
                    route('back.trainees.confirm-detected-english-name', {
                        trainee_id: this.traineeId,
                    }),
                    {
                        english_name: this.detectedName,
                    }
                );

                this.$emit('confirmed', response.data.english_name || this.detectedName);
            } catch (error) {
                console.error('Failed to confirm detected english name', error);
                alert(this.$t('words.please-try-again'));
            } finally {
                this.loading = false;
            }
        },
        confirmNo() {
            this.$emit('declined');
        },
    },
};
</script>
