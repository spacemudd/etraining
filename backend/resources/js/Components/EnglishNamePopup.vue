<template>
    <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay (no close on click) -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

            <!-- Modal panel -->
            <div class="inline-block align-bottom bg-white rounded-lg text-right overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:mr-4 sm:text-right w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                إدخال الاسم الإنجليزي
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 mb-4">
                                    يرجى إدخال اسمك باللغة الإنجليزية للمتابعة
                                </p>
                                <input
                                    v-model="englishName"
                                    type="text"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="أدخل اسمك الإنجليزي"
                                    @keyup.enter="saveEnglishName"
                                />
                                <div v-if="error" class="mt-2 text-sm text-red-600">
                                    {{ error }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button
                        type="button"
                        @click="saveEnglishName"
                        :disabled="loading || !englishName.trim()"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span v-if="loading" class="inline-flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            جاري الحفظ...
                        </span>
                        <span v-else>حفظ</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'EnglishNamePopup',
    props: {
        show: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            englishName: '',
            loading: false,
            error: ''
        }
    },
    methods: {
        async saveEnglishName() {
            if (!this.englishName.trim()) {
                this.error = 'يرجى إدخال الاسم الإنجليزي';
                return;
            }

            this.loading = true;
            this.error = '';

            try {
                const response = await axios.post(route('trainees.update-english-name'), {
                    english_name: this.englishName.trim()
                });

                if (response.status === 200) {
                    this.$emit('saved', this.englishName.trim());
                    this.closePopup();
                }
            } catch (error) {
                this.error = 'حدث خطأ أثناء حفظ الاسم. يرجى المحاولة مرة أخرى.';
                console.error('Error saving english name:', error);
            } finally {
                this.loading = false;
            }
        },
        closePopup() {
            this.$emit('close');
            this.englishName = '';
            this.error = '';
        }
    }
}
</script> 