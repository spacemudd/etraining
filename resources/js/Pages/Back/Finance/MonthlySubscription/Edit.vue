<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'finance', link: route('back.finance')},
                    {title: 'view-monthly-subscription-settings', link: route('back.finance.monthly-subscription.edit')},
                    {title: 'edit'},
                ]"
            ></breadcrumb-container>

            <div class="mt-4">
                <jet-form-section @submitted="updateMonthlySubscription">
                    <template #title>
                        {{ $t('words.edit') }}
                    </template>

                    <template #form>
                            <div class="col-span-2 sm:col-span-2">
                                <jet-label for="name" :value="$t('words.trainee_monthly_subscription')" />
                                <jet-input id="name" type="text" class="mt-1 block w-full" v-model="form.trainee_monthly_subscription" autocomplete="off" autofocus />
                                <jet-input-error :message="form.error('trainee_monthly_subscription')" class="mt-2" />
                            </div>
                    </template>

                    <template #actions>
                        <jet-action-message :on="form.recentlySuccessful" class="mr-3">
                            Saved.
                        </jet-action-message>

                        <inertia-link :href="route('back.finance')" class="flex items-center justify-start rtl:ml-4 ltr:mr-4 rounded-md px-4 py-2 bg-white hover:bg-gray-300 text-right">
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
    import Breadcrumb from "@/Components/Breadcrumb";
    import JetDialogModal from '@/Jetstream/DialogModal'
    import JetInput from '@/Jetstream/Input'
    import JetInputError from '@/Jetstream/InputError'
    import JetActionMessage from '@/Jetstream/ActionMessage';
    import JetButton from '@/Jetstream/Button';
    import JetFormSection from '@/Jetstream/FormSection';
    import JetLabel from '@/Jetstream/Label';
    import BreadcrumbContainer from "@/Components/BreadcrumbContainer";

    export default {
        props: ['sessions', 'trainee_monthly_subscription'],

        components: {
            AppLayout,
            JetSectionBorder,
            Breadcrumb,
            JetDialogModal,
            JetInput,
            JetInputError,
            JetActionMessage,
            JetButton,
            JetFormSection,
            JetLabel,
            BreadcrumbContainer,
        },
        mounted() {
            this.form.trainee_monthly_subscription = this.trainee_monthly_subscription;
        },
        data() {
            return {
                form: this.$inertia.form({
                    trainee_monthly_subscription: '',
                }, {
                    bag: 'updateMonthlySubscription',
                })
            }
        },
        methods: {
            updateMonthlySubscription() {
                this.form.put(route('back.finance.monthly-subscription.update'), {
                    preserveScroll: true
                });
            },
        }
    }
</script>
