<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'trainees', link: route('back.trainees.index')},
                    {title_raw: trainee.name, link: route('back.trainees.show', trainee.id)},
                    {title: 'issue-invoice'},
                ]"
            ></breadcrumb-container>

            <div class="mt-4">
                <jet-form-section @submitted="createTraineeInvoice">
                    <template #title>
                        {{ $t('words.issue-invoice-trainee') }}
                    </template>

                    <template #description>
                        {{ $t('words.issue-invoice-trainee-description') }}
                    </template>

                    <template #form>
                        <div class="col-span-2 sm:col-span-2">
                            <jet-label
                                for="from_date"
                                :value="$t('words.date-from')"
                            />
                            <jet-input
                                id="from_date"
                                type="date"
                                class="mt-1 block w-full"
                                v-model="form.from_date"
                                autocomplete="off"
                            />
                            <jet-input-error
                                :message="form.error('from_date')"
                                class="mt-2"
                            />
                        </div>

                        <div class="col-span-2 sm:col-span-2">
                            <jet-label
                                for="to_date"
                                :value="$t('words.date-to')"
                            />
                            <jet-input
                                id="to_date"
                                type="date"
                                class="mt-1 block w-full"
                                v-model="form.to_date"
                                autocomplete="off"
                            />
                            <jet-input-error
                                :message="form.error('to_date')"
                                class="mt-2"
                            />
                        </div>

                        <div class="col-span-2 sm:col-span-2">
                            <jet-label
                                for="to_date"
                                :value="$t('words.expected-invoice-value-per-trainee')"
                            />
                            <jet-input
                                id="expected_amount"
                                class="mt-1 block w-full bg-gray-100 cursor-not-allowed"
                                :value="amountBreakdown"
                                disabled
                            />
                        </div>
                    </template>

                    <template #actions>
                        <jet-action-message
                            :on="form.recentlySuccessful"
                            class="mr-3"
                        >
                            {{ $t('words.saved-successfully') }}
                        </jet-action-message>

                        <inertia-link
                            :href="`/back/trainees/${trainee.id}`"
                            class="flex items-center justify-start rtl:ml-4 ltr:mr-4 rounded-md px-4 py-2 bg-white hover:bg-gray-300 text-right"
                        >
                            {{ $t('words.cancel') }}
                        </inertia-link>

                        <jet-button
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            {{ $t('words.submit') }}
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
import moment from "moment";

export default {
    props: [
        'trainee',
        'monthly_subscription_per_trainee',
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
                from_date: '',
                to_date: '',
            }, {
                bag: 'createTraineeInvoice',
            })
        }
    },
    computed: {
        amountBreakdown() {
            if (!!!this.form.from_date && !!!this.form.to_date) {
                return 0;
            }

            const months = moment(this.form.to_date).diff(moment(this.form.from_date), 'months');
            const days = moment(this.form.to_date).diff(moment(this.form.from_date), 'days');

            let amount = 0;

            if(months > 0) {

            }

            console.log(days);

            return `${days} days * 50 SR = 800 SR`;
        },
    },
    methods: {
        createTraineeInvoice() {
            this.form.post(`/back/trainees/${this.trainee.id}/invoices/create`, {
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
