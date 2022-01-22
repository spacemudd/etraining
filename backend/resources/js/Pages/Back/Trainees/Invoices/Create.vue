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
                                for="month"
                                :value="$t('words.month')"
                            />

                            <div class="relative">
                                <select
                                    class="block appearance-none w-full border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                    v-model="form.month"
                                    id="month"
                                >
                                    <option value="1">{{ $t('words.january') }}</option>
                                    <option value="2">{{ $t('words.february') }}</option>
                                    <option value="3">{{ $t('words.march') }}</option>
                                    <option value="4">{{ $t('words.april') }}</option>
                                    <option value="5">{{ $t('words.may') }}</option>
                                    <option value="6">{{ $t('words.june') }}</option>
                                    <option value="7">{{ $t('words.july') }}</option>
                                    <option value="8">{{ $t('words.august') }}</option>
                                    <option value="9">{{ $t('words.september') }}</option>
                                    <option value="10">{{ $t('words.october') }}</option>
                                    <option value="11">{{ $t('words.november') }}</option>
                                    <option value="12">{{ $t('words.december') }}</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                    <svg
                                        class="fill-current h-4 w-4"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20"
                                    >
                                        <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                                    </svg>
                                </div>
                            </div>

                            <jet-input-error
                                :message="form.error('month')"
                                class="mt-2"
                            />
                        </div>
                        <div class="col-span-2 sm:col-span-2">
                            <jet-label
                                for="year"
                                :value="$t('words.year')"
                            />

                            <jet-input
                                type="number"
                                min="2021"
                                :max="current_year + 1"
                                class="mt-1 block w-full"
                                v-model="form.year"
                                autocomplete="off"
                                required="true"
                                step="1"
                            />

                            <jet-input-error
                                :message="form.error('year')"
                                class="mt-2"
                            />
                        </div>

                        <div class="col-span-2 sm:col-span-2">
                            <jet-label
                                for="amount"
                                :value="$t('words.expected-invoice-value-per-trainee')"
                            />

                            <jet-input
                                class="mt-1 block w-full"
                                id="amount"
                                :value="trainee.company.monthly_subscription_per_trainee"
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
            current_year: moment().utc().year(),
            form: this.$inertia.form({
                month: moment().utc().month() + 1,
                year: moment().utc().year(),
            }, {
                bag: 'createTraineeInvoice',
            })
        }
    },
    methods: {
        createTraineeInvoice() {
            this.form.post(`/back/trainees/${this.trainee.id}/invoices/`, {
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
