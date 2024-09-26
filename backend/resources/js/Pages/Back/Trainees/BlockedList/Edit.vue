<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'trainees', link: route('back.trainees.index')},
                    {title: 'blocked-list'},
                    
                ]"
            ></breadcrumb-container>

            <div class="mt-4">
                <jet-form-section @submitted="updateTrainee">
                    <template #title>
                        {{ $t('words.edit') }}
                    </template>

                    <template #form>

                        <div class="col-span-4 sm:col-span-4">
                            <jet-label for="email" :value="$t('words.email')" />
                            <jet-input dir="ltr" id="email" type="email" class="mt-1 block w-full text-right" v-model="form.email" autocomplete="off" autofocus />
                            <jet-input-error :message="form.error('email')" class="mt-2" />
                        </div>

                        <div class="col-span-2 sm:col-span-2">
                            <jet-label for="name" :value="$t('words.name')" />
                            <jet-input id="name" type="text" class="mt-1 block w-full" v-model="form.name" autocomplete="off" />
                            <jet-input-error :message="form.error('name')" class="mt-2" />
                        </div>

                        <div class="col-span-2 sm:col-span-2">
                            <jet-label for="english_name" :value="$t('words.name_en')" />
                            <jet-input id="english_name" type="text" class="mt-1 block w-full" v-model="form.english_name" autocomplete="off" />
                            <jet-input-error :message="form.error('english_name')" class="mt-2" />
                        </div>

                        <div class="col-span-2 sm:col-span-2">
                            <jet-label for="identity_number" :value="$t('words.identity_number')" />
                            <jet-input id="identity_number" type="text" class="mt-1 block w-full" v-model="form.identity_number" autocomplete="off" />
                            <jet-input-error :message="form.error('identity_number')" class="mt-2" />
                        </div>

                        <div class="col-span-2 sm:col-span-2">
                            <jet-label for="phone" :value="$t('words.phone')" />
                            <jet-input id="phone" type="text" class="mt-1 block w-full" v-model="form.phone" placeholder="9665XXXXXXXX" autocomplete="off" />
                            <jet-input-error :message="form.error('phone')" class="mt-2" />
                        </div>

                        <div class="col-span-2 sm:col-span-2">
                            <jet-label for="phone_additional" :value="$t('words.phone_additional')" />
                            <jet-input id="phone_additional" type="text" class="mt-1 block w-full" v-model="form.phone_additional" placeholder="9665XXXXXXXX" autocomplete="off" />
                            <jet-input-error :message="form.error('phone_additional')" class="mt-2" />
                        </div>

                        <div class="col-span-4 sm:col-span-4">
                            <jet-label for="reason" :value="$t('words.reason')" />
                            <jet-input id="reason" type="text" class="mt-1 block w-full" v-model="form.reason" autocomplete="off" required />
                            <jet-input-error :message="form.error('reason')" class="mt-2" />
                        </div>
                    </template>

                    <template #actions>
                        <jet-action-message :on="form.recentlySuccessful" class="mr-3">
                            {{ $t('words.saved-successfully') }}
                        </jet-action-message>

                        <inertia-link :href="route('back.trainees.block-list.index')" class="flex items-center justify-start rtl:ml-4 ltr:mr-4 rounded-md px-4 py-2 bg-white hover:bg-gray-300 text-right">
                            {{ $t('words.cancel') }}
                        </inertia-link>

                        <jet-button :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            {{ $t('words.save') }}
                        </jet-button>
                    </template>
                </jet-form-section>
            </div>
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout'
    import JetSectionBorder from '@/Jetstream/SectionBorder'
    import JetDialogModal from '@/Jetstream/DialogModal'
    import JetInput from '@/Jetstream/Input'
    import JetInputError from '@/Jetstream/InputError'
    import JetActionMessage from '@/Jetstream/ActionMessage';
    import JetButton from '@/Jetstream/Button';
    import JetFormSection from '@/Jetstream/FormSection';
    import JetLabel from '@/Jetstream/Label';
    import BreadcrumbContainer from "@/Components/BreadcrumbContainer";
    import SelectTraineeGroup from "@/Components/SelectTraineeGroup";

    export default {
        props: [
            'sessions',
            'traineeBlockList',
        ],
        components: {
            AppLayout,
            JetSectionBorder,
            JetDialogModal,
            JetInput,
            JetInputError,
            JetActionMessage,
            JetButton,
            JetFormSection,
            JetLabel,
            BreadcrumbContainer,
            SelectTraineeGroup,
        },
        data() {
            return {
                addressSearch: '',
                form: this.$inertia.form({
                    email: '',
                    name: '',
                    english_name: '',
                    identity_number: '',
                    phone: '',
                    phone_additional: '',
                    reason: '',
                }, {
                    bag: 'updateTraineeBlockList',
                })
            }
        },
        mounted() {
            this.form.email = this.traineeBlockList.email;
            this.form.name = this.traineeBlockList.name;
            this.form.english_name = this.traineeBlockList.english_name;
            this.form.identity_number = this.traineeBlockList.identity_number;
            this.form.phone = this.traineeBlockList.phone;
            this.form.phone_additional = this.traineeBlockList.phone_additional;
            this.form.reason = this.traineeBlockList.reason;
        },
        methods: {
            updateTrainee() {
                this.form.put(route('back.trainees.suspend.update', {trainee_block_list_id: this.traineeBlockList.id}), {
                    preserveScroll: true
                }).catch(error => {
                    this.form.processing = false;
                }).finally(() => {
                    this.form.processing = false;
                });
            },
        }
    }
</script>
