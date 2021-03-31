<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'trainees', link: route('back.trainees.index')},
                    {title: 'send'},
                ]"
            ></breadcrumb-container>

            <div class="mt-4">
                <jet-form-section @submitted="sendNotification">
                    <template #title>
                        {{ $t('words.send-messages-to-groups-of-trainees') }}
                    </template>

                    <template #description>

                    </template>

                    <template #form>

                        <div class="col-span-4 sm:col-span-4">
                            <jet-label for="group" :value="$t('words.group')" />
                            <!--<jet-input id="to_trainee_status" type="text" class="mt-1 block w-full" value="المرشحين" disabled />-->
                            <select class="block appearance-none w-full border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                    v-model="form.to_trainees_status"
                                    id="instructor_id">
                                <option value="1">المرشحين</option>
                                <option value="0">ملف غير مكتمل</option>
                            </select>
                            <jet-input-error :message="form.error('to_trainee_status')" class="mt-2" />
                        </div>

                        <div class="col-span-4 sm:col-span-4">
                            <jet-label for="registered_today_online" :value="$t('words.registered-today-online')" />
                            <input type="checkbox" v-model="form.registered_today_online">
                            <jet-input-error :message="form.error('registered_today_online')" class="mt-2" />
                        </div>

                        <div class="col-span-4 sm:col-span-4">
                            <jet-label for="email_title" :value="$t('words.email-title')" />
                            <jet-input id="email_title" type="text" class="mt-1 block w-full" v-model="form.email_title" autocomplete="off" autofocus />
                            <jet-input-error :message="form.error('email_title')" class="mt-2" />
                        </div>

                        <div class="col-span-4 sm:col-span-4">
                            <jet-label for="email_body" :value="$t('words.email-body')" />
                            <jet-textarea id="email_body" type="text" class="mt-1 block w-full" v-model="form.email_body" autocomplete="off" />
                            <jet-input-error :message="form.error('email_body')" class="mt-2" />
                        </div>

                        <div class="col-span-4 sm:col-span-4">
                            <jet-label for="sms_body" :value="$t('words.sms-body')" />
                            <jet-input id="sms_body" type="text" class="mt-1 block w-full" v-model="form.sms_body" autocomplete="off" />
                            <jet-input-error :message="form.error('sms_body')" class="mt-2" />
                        </div>
                    </template>

                    <template #actions>
                        <jet-action-message :on="form.recentlySuccessful" class="mr-3">
                            {{ $t('words.saved-successfully') }}
                        </jet-action-message>

                        <inertia-link href="/back/trainees" class="flex items-center justify-start rtl:ml-4 ltr:mr-4 rounded-md px-4 py-2 bg-white hover:bg-gray-300 text-right">
                            {{ $t('words.cancel') }}
                        </inertia-link>

                        <jet-button :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            {{ $t('words.send') }}
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
    import JetTextarea from '@/Jetstream/Textarea';

    export default {
        props: [
            //
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
            JetTextarea,
        },
        data() {
            return {
                addressSearch: '',
                form: this.$inertia.form({
                    to_trainees_status: 1,
                    registered_today_online: false,
                    email_title: '',
                    email_body: '',
                    email_message: '',
                    sms_body: '',
                }, {
                    bag: 'sendNotification',
                })
            }
        },
        methods: {
            sendNotification() {
                this.form.post('/back/trainees/send-notification/send', {
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
