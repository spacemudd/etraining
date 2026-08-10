<template>
    <app-layout>
        <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center p-4">
            <div class="max-w-md w-full bg-white rounded-lg shadow-xl p-8 text-center">
                <!-- Course Info -->
                <div class="mb-8">
                    <h1 class="text-xl font-bold text-gray-900 mb-2">
                        {{ course_batch_session.course.name }}
                    </h1>
                    <p class="text-gray-600">
                        {{ course_batch_session.course_batch.name }}
                    </p>
                </div>

                <!-- Waiting Message -->
                <div class="mb-8">
                    <div class="flex justify-center mb-4">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                    </div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-2">
                        {{ $t('words.instructor-preparing-session') }}
                    </h2>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        {{ $t('words.instructor-delay-message') }}
                    </p>
                </div>

                <div class="mb-6">
                    <div class="bg-blue-50 rounded-lg p-4">
                        <p class="text-sm text-gray-700">
                            {{ $t('words.waiting-click-refresh-when-ready') }}
                        </p>
                    </div>
                </div>

                <!-- Manual Refresh Button -->
                <button 
                    @click="refreshPage"
                    :disabled="isRefreshing"
                    class="w-full bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-medium py-2 px-4 rounded-md transition duration-200"
                >
                    <span v-if="isRefreshing" class="flex items-center justify-center">
                        <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                        {{ $t('words.refreshing') }}
                    </span>
                    <span v-else>
                        {{ $t('words.refresh-now') }}
                    </span>
                </button>

                <!-- Help Text -->
                <div class="mt-6 text-xs text-gray-500">
                    <p>{{ $t('words.having-trouble-contact-support') }}</p>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'

export default {
    components: {
        AppLayout,
    },
    
    props: ['course_batch_session'],
    
    data() {
        return {
            isRefreshing: false,
        }
    },
    
    methods: {
        refreshPage() {
            if (this.isRefreshing) {
                return;
            }

            this.isRefreshing = true;

            // Manual check only — no auto reload (avoids request storms before class start)
            if (this.$inertia && this.$inertia.reload) {
                this.$inertia.reload({
                    preserveScroll: true,
                    onFinish: () => {
                        this.isRefreshing = false;
                    },
                    onError: () => {
                        this.isRefreshing = false;
                    },
                });
                return;
            }

            window.location.reload();
        }
    }
}
</script>

<style scoped>
/* Custom styles for the waiting page */
.animate-spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}
</style>
