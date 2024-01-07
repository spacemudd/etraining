<template>
    <app-layout>

        <div class="container">
            <breadcrumb-container>

            </breadcrumb-container>

            <div class="px-8">
                <h1>{{ $t('words.change-date-period') }} (<span class="text-red-500">{{invoices.length}}</span>)</h1>
                <jet-form-section @submitted="ChangeDatePeriod">
                    <template #form>
                        <div class="col-span-2 sm:col-span-2">
                        <jet-label
                            for="from"
                            :value="$t('words.from-date')"
                        />

                        <jet-input
                            id="from"
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
                    </template>
                    <template #actions>
                        <jet-action-message
                            :on="form.recentlySuccessful"
                            class="mr-3"
                        >
                            {{ $t('words.saved-successfully') }}
                        </jet-action-message>

                        <inertia-link
                            :href="`/`"
                            class="flex items-center justify-start rtl:ml-4 ltr:mr-4 rounded-md px-4 py-2 bg-white hover:bg-gray-300 text-right"
                        >
                            {{ $t('words.cancel') }}
                        </inertia-link>

                        <jet-button
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            {{ $t('words.save') }}
                        </jet-button>
                    </template>
                </jet-form-section>
                <table class="w-full whitespace-no-wrap bg-white rounded-lg my-5 p-5 shadow text-sm">
                    <tr class="text-left font-bold">
                        <th class="p-4">{{ $t('words.date-created') }}</th>
                        <th class="p-4">{{ $t('words.date-period') }}</th>
                        <th class="p-4">{{ $t('words.trainees') }}</th>
                        <th class="p-4">{{ $t('words.grand-total') }}</th>
                        <th class="p-4">{{ $t('words.initiated-by') }}</th>
                        <th class="p-4">{{ $t('words.actions') }}</th>
                    </tr>
                    <tr
                        v-for="invoice in invoices"
                        :key="invoice.id"
                        class="border-t hover:bg-gray-100 focus-within:bg-gray-100"
                    >
                        <td class="px-4 py-4">
                            {{ invoice.created_at_date }}
                        </td>

                        <td class="px-4 py-4">
                            {{ invoice.from_date | formatDate }}
                            <br />
                            {{ invoice.to_date | formatDate }}
                        </td>

                        <td class="px-4 py-4">
                            {{ invoice.trainee_count }}
                        </td>

                        <td class="px-4 py-4">
                            {{ invoice.grand_total }}
                        </td>

                        <td class="px-4 py-4">
                            {{ invoice.created_by ? invoice.created_by.name : 'Unknown' }}
                        </td>
                    </tr>

                </table>
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
import _ from "lodash";
import { Skeleton } from 'vue-loading-skeleton';

export default {
    props: [
        'company',
        'invoices',
        'old_from_date',
        'old_to_date',
        'created_at',
        'created_by_id',
    ],

    components: {
        Skeleton,
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
            traineesCollection: [],
            form: this.$inertia.form({
                from_date: null,
                to_date: null,
                old_from_date: null,
                old_to_date: null,
                created_at: null,
                created_by_id: null,
            }),
            searchString: '',
            searchResults: [],
            searchBoxVisible: false,
        }
    },
    mounted() {
        this.form.old_from_date = this.old_from_date;
        this.form.old_to_date = this.old_to_date;
        this.form.created_at = this.created_at;
        this.form.created_by_id = this.created_by_id;
    },
    methods: {
        triggerSearching() {
            if (this.searchString) {
                this.searchBoxVisible = true;
                this.loadSearchResultsBox();
            } else {
                // this.userFinishedWithResults();
                this.searchBoxVisible = false;
            }
        },
        loadSearchResultsBox: _.debounce(function() {
            if (this.searchString) {
                this.searchResults = 3;
                axios.get('/back/search', {
                    params: {
                        search: this.searchString,
                        trainees: true,
                    }
                })
                    .then(response => {
                        this.searchResults = response.data;
                    })
            }
        }, 320),
        toggleSelectedTrainee(trainee_id) {
            if (this.form.trainees.includes(trainee_id)) {
                let index = this.form.trainees.indexOf(trainee_id);
                if (index !== -1) {
                    this.form.trainees.splice(index, 1);
                }
            } else {
                this.form.trainees.push(trainee_id);
            }
        },
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
        ChangeDatePeriod() {
            if(this.form.from_date && this.form.to_date){
                this.form.post(route('back.companies.invoices.change-date-period', {company_id: this.company.id}), {
                    preserveScroll: true
                }).catch(error => {
                    this.form.processing = false;
                }).finally(() => {
                    this.form.processing = false;
                });
            }

        },
        addToTrainees(trainee_id, name) {
            this.traineesCollection[trainee_id] = name;
            this.form.trainees.push(trainee_id);
        },
        selectAllTrainees() {
            if (this.form.trainees.length === _.size(this.traineesCollection)) {
                this.form.trainees = [];
            } else {
                this.form.trainees = [];
                Object.keys(this.traineesCollection).forEach((trainee, key) => {
                    this.form.trainees.push(trainee);
                })
            }
        },
    }
}
</script>
