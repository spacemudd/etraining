<template>
    <app-layout>
        <div class="container px-6 mx-auto grid">
            <div class="mt-20" v-if="contract">
                <div class="md:grid md:grid-cols-3 md:gap-6">
                    <div class="md:col-span-1">
                        <div class="px-4 sm:px-0">
                            <h3 class="text-lg font-medium text-gray-900">
                                {{ contract.company.name_ar }}<br/>
                                {{ contract.company.name_en }}
                            </h3>
                            <p class="mt-1 text-sm text-gray-600">
                                {{ $t('words.edit') }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-5 md:mt-0 md:col-span-2">
                        <form @submit.prevent="updateContract" enctype="multipart/form-data">
                            <div class="shadow overflow-hidden sm:rounded-md">
                                <div class="px-4 py-5 bg-white sm:p-6">
                                    <div class="grid grid-cols-4 gap-6">
                                        <template v-for="fieldName in [
                                                'reference_number',
                                                'contract_starts_at',
                                                'contract_period_in_months',
                                                'trainees_count',
                                                'trainee_salary',
                                                'instructor_cost',
                                                'company_reimbursement',
                                                ]">
                                            <div class="col-span-2 sm:col-span-2">
                                                <jet-label :for="fieldName" :value="$t('words.'+fieldName)" />
                                                <jet-input :id="fieldName"
                                                           :type="fieldName==='contract_starts_at' ? 'date' : 'text'"
                                                           class="mt-1 block w-full"
                                                           v-model="contractForm[fieldName]"
                                                           autocomplete="off"
                                                           :autofocus="fieldName==='reference_number'"
                                                           :required="fieldName === 'contract_starts_at'"
                                                />
                                                <!--<jet-input-error :message="contractForm.error(fieldName)" class="mt-2" />-->
                                            </div>
                                        </template>

                                        <div class="col-span-4 sm:col-span-4">
                                            <jet-label for="notes" :value="$t('words.notes')" />
                                            <jet-textarea id="notes" type="textarea" class="mt-1 block w-full" v-model="contractForm.notes" />
                                            <!--<jet-input-error :message="contractForm.error('notes')" class="mt-2" />-->
                                        </div>
                                        <!--Done form-->

                                        <div class="flex items-center justify-start px-4 py-3 bg-gray-50 text-right">
                                            <inertia-link :href="route('back.companies.contracts.show', {company_id: contract.company_id, contract: contract.id})"
                                                          class="flex items-center justify-start rtl:ml-4 ltr:mr-4 rounded-md px-4 py-2 bg-white hover:bg-gray-300 text-right">
                                                {{ $t('words.cancel') }}
                                            </inertia-link>

                                            <jet-button :class="{ 'opacity-25': $wait.is('UPDATING_CONTRACT') }" :disabled="$wait.is('UPDATING_CONTRACT')">
                                                {{ $t('words.save') }}
                                            </jet-button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
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
    import JetTextarea from '@/Jetstream/Textarea'

    export default {
        props: ['contract'],

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
            JetTextarea,
        },
        data() {
            return {
                contractForm: {
                    reference_number: '',
                    contract_starts_at: '',
                    contract_period_in_months: '',
                    trainees_count: '',
                    trainee_salary: '',
                    instructor_cost: '',
                    company_reimbursement: '',
                    notes: '',
                }
            }
        },
        mounted() {
            this.contractForm.reference_number = this.contract.reference_number;
            this.contractForm.contract_starts_at = this.contract.contract_starts_at;
            this.contractForm.contract_period_in_months = this.contract.contract_period_in_months;
            this.contractForm.trainees_count = this.contract.trainees_count;
            this.contractForm.trainee_salary = this.contract.trainee_salary;
            this.contractForm.instructor_cost = this.contract.instructor_cost;
            this.contractForm.company_reimbursement = this.contract.company_reimbursement;
            this.contractForm.notes = this.contract.notes;
        },
        methods: {
            updateContract() {
                this.$wait.start('UPDATING_CONTRACT');
                axios.put(route('back.companies.contracts.update', {company_id: this.contract.company_id, contract: this.contract.id}), this.contractForm)
                .then(response => {
                    this.$wait.end('UPDATING_CONTRACT');
                    this.$inertia.visit(this.contract.show_url, {method: 'get'});
                })
                //     .catch(error => {
                //     this.$wait.end('UPDATING_CONTRACT');
                //     alert(error.response.data.message+' - '+'قد يمكن ان الرقم المرجعي مستخدم من قبل');
                //     Logrocket.captureException(error);
                //     throw error;
                // })
            },
        }
    }
</script>
