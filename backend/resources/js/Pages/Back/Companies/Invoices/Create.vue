<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'companies', link: route('back.companies.index')},
                    {title_raw: company.name_ar, link: route('back.companies.show', company.id)},
                    {title: 'issue-invoice'},
                ]"
            ></breadcrumb-container>

            <div class="mt-4">
                <jet-form-section @submitted="createCompanyInvoice">
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
                                for="value-per-invoice"
                                :value="$t('words.value-per-invoice')+' ('+$t('words.without-vat')+')'"
                            />

                            <jet-input
                                id="value-per-invoice"
                                type="number"
                                class="mt-1 block w-full"
                                v-model="form.value_per_invoice"
                                autocomplete="off"
                            />

                            <jet-input-error
                                :message="form.error('value_per_invoice')"
                                class="mt-2"
                            />
                        </div>


                        <div class="col-span-4" v-if="expectedToPay">
                            <p class="col-span-4 bg-black text-white p-2">
                                {{ $t('words.expected-cost-per-invoice') }}: {{ expectedToPay }}
                            </p>
                        </div>

                        <div class="col-span-4 sm:col-span-4">
                            <div class="flex justify-between">
                                <jet-label
                                    for="amount"
                                    :value="$t('words.trainees-to-invoice')"
                                />

                                <button
                                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150"
                                    type="button"
                                    @click.prevent="selectAllTrainees"
                                >
                                    Select All
                                </button>
                            </div>

                            <div class="mt-2 grid grid-cols-1 gap-4">
                                <div
                                    v-for="(trainee_name, trainee_id) in trainees"
                                    :key="trainee_id"
                                >
                                    <label class="flex items-center">
                                        <input
                                            type="checkbox"
                                            class="form-checkbox"
                                            :value="trainee_id"
                                            v-model="form.trainees"
                                        >
                                        <span class="mx-2 text-sm text-gray-600">{{ trainee_name }}</span>
                                    </label>
                                </div>
                            </div>

                            <jet-input-error
                                :message="form.error('trainees')"
                                class="mt-2"
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
                            :href="`/back/companies/${company.id}`"
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
import moment from "moment";

export default {
    props: [
        'company',
        'trainees',
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
    },
    data() {
        return {
            expectedToPay: null,
            current_year: moment().utc().year(),
            form: this.$inertia.form({
                from_date: null,
                to_date: null,
                value_per_invoice: this.$props.monthly_subscription_per_trainee,
                trainees: [],
            }),
        }
    },
    methods: {
        updateExpectedAmountPerInvoice() {
            if (this.form.from_date && this.form.to_date) {
                axios.post('/back/finance/expected-amount-per-invoice', {
                    from_date: this.form.from_date,
                    to_date: this.form.to_date,
                    company_id: this.company.id,
                }).then(response => {
                    this.expectedToPay = response.data.cost;
                });
            }
        },
        createCompanyInvoice() {
            this.form.post(`/back/companies/${this.company.id}/invoices/`, {
                preserveScroll: true
            }).catch(error => {
                this.form.processing = false;
            }).finally(() => {
                this.form.processing = false;
            });
        },
        selectAllTrainees() {
            this.form.trainees = Object.keys(this.trainees);
        },
    }
}
</script>
