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
    <div>
        <button @click="open" class="items-center px-4 py-2 bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150">
            {{ $t('words.new') }}
        </button>

        <portal-target :name="`app-course-session-modal-container-${courseBatch.id}`"></portal-target>
        <portal :to="`app-course-session-modal-container-${courseBatch.id}`">
            <modal :name="'createCourseBatchSessionModal'+courseBatch.id" :key="'createCourseBatchSessionModal'+courseBatch.id">
                <div class="bg-white rounded-lg">
                    <div class="m-5">
                        <h1 class="text-lg font-bold">{{ $t('words.create-course-session') }}</h1>

                        <div class="mt-5">
                            <jet-label for="starts_at" :value="$t('words.start-date')" />
                            <ejs-datetimepicker class="mt-1 block w-full" v-model="form.starts_at" :placeholder="$t('words.select-date')" :enableRtl="rtl"></ejs-datetimepicker>
                            <jet-input-error :message="form.error('starts_at')" class="mt-2" />
                        </div>

                        <div class="mt-5">
                            <jet-label for="ends_at" :value="$t('words.end-date')" />
                            <ejs-datetimepicker class="mt-1 block w-full" v-model="form.ends_at" :placeholder="$t('words.select-date')" :enableRtl="rtl"></ejs-datetimepicker>
                            <jet-input-error :message="form.error('ends_at')" class="mt-2" />
                        </div>

                        <div class="mt-5">
                            <jet-secondary-button @click.native="close">
                                {{ $t('words.cancel') }}
                            </jet-secondary-button>

                            <jet-button class="rtl:mr-5 ltr:ml-5"
                                        @click.native="createNewCourseBatchSession"
                                        :class="{ 'opacity-25': form.processing }"
                                        :disabled="form.processing">
                                {{ $t('words.save') }}
                            </jet-button>
                        </div>
                    </div>
                </div>
            </modal>
        </portal>
    </div>
</template>

<script>
    import '@syncfusion/ej2-base/styles/material.css';
    import '@syncfusion/ej2-buttons/styles/material.css';
    import '@syncfusion/ej2-inputs/styles/material.css';
    import '@syncfusion/ej2-popups/styles/material.css';
    import '@syncfusion/ej2-lists/styles/material.css';
    import '@syncfusion/ej2-vue-calendars/styles/material.css';
    import 'vue-js-modal/dist/styles.css';

    import JetLabel from '@/Jetstream/Label';
    import JetSecondaryButton from '@/Jetstream/SecondaryButton';
    import JetInputError from '@/Jetstream/InputError';
    import JetInput from '@/Jetstream/Input';
    import JetButton from '@/Jetstream/Button';

    export default {
        components: {
            JetLabel,
            JetSecondaryButton,
            JetInputError,
            JetInput,
            JetButton
        },
        props: ['courseBatch'],
        computed: {
            rtl() {
                let lang = document.documentElement.lang.substr(0, 2);
                return lang === 'ar';
            },
        },
        data() {
            return {
                form: this.$inertia.form({
                    course_batch_id: '',
                    starts_at: null,
                    ends_at: null,
                }, {
                    resetOnSuccess: true,
                    bag: 'form',
                }),
            }
        },
        mounted() {
            //
        },
        methods: {
            createNewCourseBatchSession() {
                this.form.course_batch_id = this.courseBatch.id;
                this.form.post(route('back.course-batch-sessions.store', {
                    course_id: this.courseBatch.course_id,
                    course_batch_id: this.courseBatch.id,
                })).then(response => {
                    this.close();
                    this.$emit('session:saved');
                });
            },
            close() {
                this.$modal.hide('createCourseBatchSessionModal'+this.courseBatch.id);
            },
            open() {
                this.$modal.show('createCourseBatchSessionModal'+this.courseBatch.id);
            },
        }
    }
</script>
