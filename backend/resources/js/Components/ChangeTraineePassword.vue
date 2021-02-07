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
    <div class="inline-block">
        <button @click="$refs.changePasswordForTraineeModal.open()"
                class="inline-flex items-center rounded-md px-4 py-2 bg-yellow-200 hover:bg-yellow-300 text-right">
            {{ $t('words.change-password') }}
        </button>

        <sweet-modal ref="changePasswordForTraineeModal">
            <form ref="changeTraineePasswordForm">
                <div class="col-span-2 sm:col-span-2">
                    <jet-label for="new_trainee_password" :value="$t('words.change-password')" />
                    <jet-input id="new_trainee_password" type="password" class="mt-1 block w-full" v-model="form.name" autocomplete="off" />
                    <jet-input-error :message="form.error('new_trainee_password')" class="mt-2" />
                </div>
            </form>
            <div slot="button">
                <button @click="changePasswordRequest"
                            class="btn-primary"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing">
                    <btn-loading-indicator v-if="form.processing" />
                    {{ $t('words.save') }}
                </button>
            </div>
        </sweet-modal>
    </div>
</template>

<script>
    import { SweetModal, SweetModalTab } from 'sweet-modal-vue';
    import JetInput from '@/Jetstream/Input'
    import JetInputError from '@/Jetstream/InputError'
    import JetLabel from '@/Jetstream/Label';
    import BtnLoadingIndicator from "./BtnLoadingIndicator";
    import LogRocket from 'logrocket';

    export default {
        name: "ChangeTraineePassword.vue",
        props: ['trainee'],
        components: {
            SweetModal,
            SweetModalTab,
            JetInput,
            JetInputError,
            JetLabel,
            BtnLoadingIndicator,
        },
        data() {
            return {
                form: this.$inertia.form({
                    new_trainee_password: null,
                }, {
                    bag: 'changingTraineePassword',
                }),
            }
        },
        methods: {
            changePasswordRequest() {
                this.form.post(route('back.trainees.set-password', {trainee_id: this.trainee.id}))
                    .then(() => {
                        this.$refs.changePasswordForTraineeModal.close();
                    }).catch(error => {
                        LogRocket.captureException(error);
                        alert(error.response.data.message);
                        throw error;
                    }).finally(() => {
                        this.form.processing = false;
                });
            }
        }
    }
</script>
