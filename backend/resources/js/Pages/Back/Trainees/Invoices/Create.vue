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
                                for="from"
                                :value="$t('words.from-date')"
                            />

                            <jet-input
                                id="from"
                                type="date"
                                @input="updateExpectedAmountPerInvoice"
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
                                for="year"
                                :value="$t('words.to-date')"
                            />

                            <jet-input
                                id="to"
                                type="date"
                                @input="updateExpectedAmountPerInvoice"
                                class="mt-1 block w-full"
                                v-model="form.to_date"
                                autocomplete="off"
                            />

                            <jet-input-error
                                :message="form.error('to_date')"
                                class="mt-2"
                            />
                        </div>

                        <div class="col-span-4">
                            <jet-label
                                for="invoice-value"
                                :value="$t('words.value-per-invoice')+' ('+$t('words.without-vat')+')'"
                            />

                            <jet-input
                                id="invoice-value"
                                type="number"
                                class="mt-1 block w-full"
                                v-model="form.invoice_value"
                                autocomplete="off"
                            />

                            <jet-input-error
                                :message="form.error('invoice_value')"
                                class="mt-2"
                            />
                        </div>

                        <div class="col-span-4" v-if="expectedToPay">
                            <p class="col-span-4 bg-black text-white p-2">
                                {{ $t('words.expected-cost-per-invoice') }}: {{ expectedToPay }}
                            </p>
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
            expectedToPay: null,
            current_year: moment().utc().year(),
            form: this.$inertia.form({
                from_date: null,
                to_date: null,
                invoice_value: this.$props.monthly_subscription_per_trainee,
            })
        }
    },
    methods: {
        updateExpectedAmountPerInvoice() {
            if (this.form.from_date && this.form.to_date) {
                axios.post('/back/finance/expected-amount-per-invoice', {
                    from_date: this.form.from_date,
                    to_date: this.form.to_date,
                    company_id: this.trainee.company_id,
                }).then(response => {
                    this.expectedToPay = response.data.cost;
                });
            }
        },
        createTraineeInvoice() {
            if(this.form.invoice_value <= 5000 ){
                this.form.post(`/back/trainees/${this.trainee.id}/invoices/`, {
                    preserveScroll: true
                }).catch(error => {
                    this.form.processing = false;
                }).finally(() => {
                    this.form.processing = false;
                });
            }else if(confirm(this.$t('words.the-maximum'))){
                return 0;
            }
        },
    },
}
</script>
