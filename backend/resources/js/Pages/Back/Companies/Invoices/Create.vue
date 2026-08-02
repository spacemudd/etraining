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
                                :value="$t('words.value-per-invoice')+' ('+$t('words.with-vat')+')'"
                            />

                            <jet-input
                                id="value-per-invoice"
                                type="number"
                                step="0.01"
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
                                    :value="$t('words.trainees-to-invoice')+' ('+form.trainees.length+')'"
                                />

                                <button
                                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150"
                                    type="button"
                                    @click.prevent="selectAllTrainees"
                                >
                                    {{ $t('words.select-all') }}
                                </button>
                            </div>

                            <div class="mt-2">
                                    <jet-input
                                        type="text"
                                        class="mt-1 block w-full"
                                        :placeholder="$t('words.search')"
                                        autocomplete="off"
                                        v-model="searchString"
                                        @input="triggerSearching()"
                                    />
                                <div v-if="searchBoxVisible"
                                     ref="searchResultsRef"
                                     id="searchResultsRef"
                                     class="search-results-box bg-white shadow-lg mt-1 p-3 overflow-y-auto">
                                    <div class="flex justify-between">
                                        <div class="text-gray-500 cursor-pointer hover:text-black" @click="searchBoxVisible=false;searchString=''">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <p class="text-gray-500">{{ $t('words.results') }}</p>
                                    </div>

                                    <div v-if="searchResults.length === 0"
                                         style="min-height:200px;"
                                         class="flex justify-center align-middle align-items-stretch h-full flex-col">
                                        <div class="text-center">
                                            <svg class="mx-auto" style="fill:#a9a8a8;" width="64px" viewBox="0 0 512.00037 512" xmlns="http://www.w3.org/2000/svg">
                                                <g fill-rule="evenodd">
                                                    <path d="m248.953125 61.273438c-5.367187-1.238282-10.722656 2.101562-11.964844 7.46875-1.242187 5.367187 2.105469 10.726562 7.472657 11.96875 24.8125 5.734374 47.492187 18.332031 65.582031 36.421874 53.183593 53.183594 53.183593 139.726563 0 192.910157-53.183594 53.1875-139.726563 53.1875-192.910157 0-53.1875-53.183594-53.1875-139.726563 0-192.910157 15.269532-15.269531 33.339844-26.40625 53.710938-33.109374 5.230469-1.71875 8.078125-7.355469 6.359375-12.589844-1.722656-5.234375-7.363281-8.082032-12.59375-6.359375-23.367187 7.683593-44.085937 20.453125-61.582031 37.953125-60.964844 60.964844-60.964844 160.160156 0 221.125 30.480468 30.480468 70.519531 45.722656 110.5625 45.722656 40.039062-.003906 80.078125-15.242188 110.5625-45.722656 60.960937-60.964844 60.960937-160.160156 0-221.125-20.738282-20.734375-46.738282-35.175782-75.199219-41.753906zm0 0"/>
                                                    <path d="m498.414062 432.707031-104.53125-104.53125c53.601563-84.054687 41.863282-194.484375-29.265624-265.617187-40.339844-40.339844-93.976563-62.558594-151.027344-62.558594-57.054688 0-110.691406 22.21875-151.03125 62.558594-40.34375 40.339844-62.558594 93.976562-62.558594 151.03125 0 57.050781 22.214844 110.6875 62.558594 151.027344 40.339844 40.339843 93.972656 62.554687 151.023437 62.554687 40.945313 0 80.386719-11.484375 114.59375-33.289063l104.53125 104.53125c8.746094 8.75 20.414063 13.566407 32.855469 13.566407 12.4375 0 24.105469-4.816407 32.855469-13.566407 18.109375-18.117187 18.109375-47.589843-.003907-65.707031zm-14.105468 51.601563c-4.980469 4.976562-11.636719 7.71875-18.746094 7.71875-7.113281 0-13.769531-2.742188-18.75-7.71875l-110.304688-110.304688c-1.929687-1.933594-4.484374-2.921875-7.054687-2.921875-1.976563 0-3.960937.582031-5.683594 1.777344-32.410156 22.480469-70.515625 34.363281-110.1875 34.363281-51.722656 0-100.347656-20.140625-136.917969-56.710937-75.5-75.5-75.5-198.347657 0-273.847657 36.574219-36.574218 85.199219-56.714843 136.925782-56.714843 51.722656 0 100.347656 20.140625 136.921875 56.714843 66.28125 66.285157 75.683593 170.207032 22.347656 247.105469-2.75 3.964844-2.269531 9.324219 1.144531 12.738281l110.304688 110.304688c10.335937 10.335938 10.335937 27.15625 0 37.496094zm0 0"/>
                                                    <path d="m273.804688 153.371094c-3.894532-3.894532-10.207032-3.894532-14.105469 0l-46.109375 46.109375-46.113282-46.109375c-3.894531-3.894532-10.210937-3.894532-14.105468 0-3.894532 3.894531-3.894532 10.210937 0 14.105468l46.109375 46.113282-46.109375 46.109375c-3.894532 3.894531-3.894532 10.210937 0 14.105469 1.945312 1.949218 4.5 2.921874 7.050781 2.921874 2.554687 0 5.105469-.972656 7.054687-2.921874l46.109376-46.109376 46.109374 46.109376c1.949219 1.949218 4.503907 2.921874 7.054688 2.921874 2.554688 0 5.105469-.972656 7.054688-2.921874 3.894531-3.894532 3.894531-10.210938 0-14.105469l-46.113282-46.109375 46.113282-46.113282c3.894531-3.894531 3.894531-10.210937 0-14.105468zm0 0"/>
                                                    <path d="m206.976562 77.328125c5.492188 0 9.972657-4.480469 9.972657-9.976563 0-5.492187-4.480469-9.972656-9.972657-9.972656-5.496093 0-9.976562 4.480469-9.976562 9.972656 0 5.496094 4.480469 9.976563 9.976562 9.976563zm0 0"/>
                                                </g>
                                            </svg>
                                            <p class="inline-block text-xl p-1 text-white bg-black p-1 px-5 mt-5">{{ $t('words.no-records-have-been-found') }}</p>
                                        </div>
                                    </div>
                                    <div v-else class="search-results-container mt-5">
                                        <div v-for="(record, index) in searchResults"
                                          :key="index"
                                          tabindex="-1"
                                          class="search-result-row p-1 px-5 rounded-lg block"
                                          :class="{'hover:bg-gray-100': searchResults.length}"
                                        >
                                            <div class="text-black font-bold flex justify-between text-sm">
                                                <div style="min-width:150px;">
                                                    <span v-if="record.deleted_at" class="text-sm inline-block mt-2 p-1 px-2 bg-red-600 text-white rounded-lg">{{ $t('words.blocked') }}</span>
                                                    <Skeleton>{{ record.resource_label }}</Skeleton>
                                                    <br/>
                                                    <Skeleton><span v-if="record.company" class="text-gray-500 font-light">{{ record.company.name_ar }}</span></Skeleton>
                                                </div>
                                                <div class="flex gap-5 my-2" v-if="record.show_url">
                                                    <a class="border flex gap-2 p-1 rounded text-sm hover:bg-blue-500" :href="record.show_url ? record.show_url : '/'" target="_blank">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                        </svg>
                                                        {{ $t('words.view') }}
                                                    </a>
                                                    <div class="border flex gap-2 p-1 rounded text-sm hover:bg-blue-500 cursor-pointer" @click="addToTrainees(record.id, record.name)">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                                        </svg>
                                                        {{ $t('words.add') }}
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-2 grid grid-cols-1 gap-4">
                                <div
                                    v-for="(trainee_name, trainee_id) in trainees"
                                    :key="trainee_id"
                                >
                                    <div class="flex items-center">
                                        <label class="flex items-center w-full">
                                            <input
                                                type="checkbox"
                                                :checked="form.trainees.includes(trainee_id)"
                                                :disabled="isTraineeOnLeave(trainee_id)"
                                                class="form-checkbox"
                                                @click="toggleSelectedTrainee(trainee_id)"
                                            >
                                            <div class="ml-3 flex-1 flex items-center justify-between">
                                                <div class="flex items-center gap-2">
                                                    <span v-if="deleted_trainees[trainee_id]"
                                                          class="inline-block text-sm px-2 bg-red-600 text-white rounded">
                                                        {{ $t('words.blocked') }}
                                                    </span>
                                                    <span class="text-sm text-gray-600" :class="{ 'text-gray-400': isTraineeOnLeave(trainee_id) }">{{ trainee_name }}</span>
                                                    <span class="border-2 border-red-600 px-2 text-xs rounded" v-if="company.trainees.find((obj) => obj.id === trainee_id).override_training_costs != null">
                                                        {{ $t('words.fixed-training-costs') }} - {{ company.trainees.find((obj) => obj.id === trainee_id).override_training_costs }} ر.س.
                                                    </span>
                                                </div>
                                                
                                                <!-- معلومات الإجازة بجانب الاسم -->
                                                <div v-if="isTraineeOnLeave(trainee_id)" class="flex items-center gap-2">
                                                    <div class="flex items-center gap-1 px-2 py-1 bg-yellow-50 border border-yellow-200 rounded-lg">
                                                        <svg class="w-3 h-3 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                                        </svg>
                                                        <span class="text-xs font-medium text-yellow-800">{{ getTraineeLeaveInfo(trainee_id).leave_type }}</span>
                                                        <span class="inline-flex items-center px-1 py-0.5 rounded-full text-xs font-medium" 
                                                              :class="getTraineeLeaveInfo(trainee_id).status === 'approved' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-orange-100 text-orange-800 border border-orange-200'">
                                                            {{ getTraineeLeaveInfo(trainee_id).status === 'approved' ? 'معتمدة' : 'معلقة' }}
                                                        </span>
                                                    </div>
                                                    <div class="text-xs text-gray-500">
                                                        {{ getTraineeLeaveInfo(trainee_id).from_date_formatted }} - {{ getTraineeLeaveInfo(trainee_id).to_date_formatted }}
                                                    </div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
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
import moment from "moment";
import _ from "lodash";
import { Skeleton } from 'vue-loading-skeleton';

export default {
    props: [
        'company',
        'trainees',
        'monthly_subscription_per_trainee',
        'deleted_trainees',
        'trainee_leaves',
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
                value_per_invoice: this.$props.monthly_subscription_per_trainee,
                trainees: [],
            }),
            searchString: '',
            searchResults: [],
            searchBoxVisible: false,
        }
    },
    mounted() {
        this.traineesCollection = this.trainees;
        console.log('Trainee Leaves Data:', this.trainee_leaves);
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
            // لا نسمح باختيار المتدربين في إجازة
            if (this.isTraineeOnLeave(trainee_id)) {
                return;
            }
            
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
        createCompanyInvoice() {
            if(this.form.value_per_invoice <= 5000 ){
                this.form.post(`/back/companies/${this.company.id}/invoices/`, {
                    preserveScroll: true
                }).catch(error => {
                    this.form.processing = false;
                }).finally(() => {
                    this.form.processing = false;
                });
            } else if(confirm(this.$t('words.the-maximum'))){
                return 0;
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
                    // لا نضيف المتدربين في إجازة
                    if (!this.isTraineeOnLeave(trainee)) {
                        this.form.trainees.push(trainee);
                    }
                })
            }
        },
        
        isTraineeOnLeave(trainee_id) {
            return this.trainee_leaves && this.trainee_leaves[trainee_id] && this.trainee_leaves[trainee_id].length > 0;
        },
        
        getTraineeLeaveInfo(trainee_id) {
            if (this.isTraineeOnLeave(trainee_id)) {
                // نأخذ أول إجازة (الأحدث بسبب الترتيب)
                const leave = this.trainee_leaves[trainee_id][0];
                return leave;
            }
            return {};
        },
    }
}
</script>
