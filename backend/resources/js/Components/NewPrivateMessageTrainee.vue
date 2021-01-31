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
        <!--<button  class="items-center px-4 py-2 bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150">-->
        <!--    {{ $t('words.new') }}-->
        <!--</button>-->
        <button @click="open" class="flex items-center justify-start rounded-md px-4 py-2 bg-yellow-200 hover:bg-yellow-300 text-right">
            {{ $t('words.send-private-message') }}
        </button>

        <portal-target :name="`send-private-message-to-${trainee.id}`"></portal-target>
        <portal :to="`send-private-message-to-${trainee.id}`">
            <modal :name="'sendPrivateMessageTo'+trainee.id" :key="'sendPrivateMessageTo'+trainee.id">
                <div class="bg-white rounded-lg">
                    <div class="m-5">
                        <h1 class="text-lg font-bold">{{ $t('words.message') }}</h1>

                        <jet-textarea id="message" class="mt-1 block w-full" v-model="form.message" autocomplete="off" autofocus />

                        <div class="mt-5">
                            <jet-secondary-button @click.native="close">
                                {{ $t('words.cancel') }}
                            </jet-secondary-button>

                            <jet-button class="rtl:mr-5 ltr:ml-5"
                                        @click.native="sendMessage"
                                        :class="{ 'opacity-25': form.processing }"
                                        :disabled="form.processing">
                                {{ $t('words.send') }}
                            </jet-button>
                        </div>
                    </div>
                </div>
            </modal>
        </portal>
    </div>
</template>

<script>
    import JetLabel from '@/Jetstream/Label';
    import JetSecondaryButton from '@/Jetstream/SecondaryButton';
    import JetInputError from '@/Jetstream/InputError';
    import JetInput from '@/Jetstream/Input';
    import JetButton from '@/Jetstream/Button';
    import JetTextarea from '@/Jetstream/Textarea';

    export default {
        components: {
            JetLabel,
            JetSecondaryButton,
            JetInputError,
            JetInput,
            JetButton,
            JetTextarea,
        },
        props: ['trainee', 'traineeGroup'],
        computed: {
            rtl() {
                let lang = document.documentElement.lang.substr(0, 2);
                return lang === 'ar';
            },
        },
        data() {
            return {
                form: this.$inertia.form({
                    trainee_id: '',
                    trainee_group_id: '',
                    message: '',
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
            sendMessage() {
                this.form.trainee_id = this.trainee.id;
                this.form.trainee_group_id = this.traineeGroup.id;
                this.form.post(route('teaching.trainee-groups.trainees.send-message', {
                    trainee_group_id: this.traineeGroup.id,
                    id: this.trainee.id,
                })).then(response => {
                    this.close();
                    this.$emit('message:saved');
                }).catch(error => {
                   throw error;
                });
            },
            close() {
                this.$modal.hide('sendPrivateMessageTo'+this.trainee.id);
            },
            open() {
                this.$modal.show('sendPrivateMessageTo'+this.trainee.id);
            },
        }
    }
</script>
