<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'companies', link: route('back.companies.index')},
                    {title_raw: company.name_ar},
                ]"
            ></breadcrumb-container>

            <div class="grid grid-cols-4 gap-6">
                <div class="col-span-4 flex items-center justify-end bg-gray-50 text-right">
                    <inertia-link :href="`/back/companies/${this.company.id}/edit`" class="flex items-center justify-start rounded-md mx-4 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-right">
                        {{ $t('words.edit') }}
                    </inertia-link>
                    <button v-if="!company.deleted_at" class="flex items-center justify-start rounded-md px-4 py-2 bg-red-600 text-white hover:bg-red-700 text-right"
                            tabindex="-1"
                            type="button"
                            @click.prevent="deleteCompany()">
                        {{ $t('words.archive') }}
                    </button>
                </div>

                <template v-for="fieldName in [
                            'name_ar',
                            'name_en',
                            'cr_number',
                            'contact_number',
                            'company_rep',
                            'company_rep_mobile',
                            'address',
                            'email',
                            ]">
                    <div class="col-span-4 sm:col-span-1">
                        <jet-label for="name" :value="$t('words.'+fieldName)" />
                        <jet-input :id="fieldName" type="text" class="mt-1 block w-full bg-gray-200" :value="company[fieldName]" disabled />
                    </div>
                </template>
            </div>

            <jet-section-border></jet-section-border>

            <div class="grid grid-cols-1 md:grid-cols-6 gap-6 mt-2">
                <div class="md:col-span-2 sm:col-span-3">
                    <div class="px-4 sm:px-0">
                        <h3 class="text-lg font-medium text-gray-900">
                            {{ $t('words.contracts') }}
                        </h3>

                        <p class="mt-1 text-sm text-gray-600">
                            {{ $t('words.contracts-help') }}
                        </p>
                    </div>
                </div>

                <div class="md:col-span-4 sm:col-span-1">
                    <company-contracts-pagination :company-id="company.id" :instructors="instructors" />
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
    import CompanyContractsPagination from "@/Components/CompanyContractsPagination";
    import BreadcrumbContainer from "@/Components/BreadcrumbContainer";

    export default {
        props: ['sessions', 'company', 'instructors'],

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
            CompanyContractsPagination,
            BreadcrumbContainer,
        },
        data() {
            return {
                form: this.$inertia.form({
                    name_ar: '',
                    name_en: '',
                    cr_number: '',
                    contact_number: '',
                    company_rep: '',
                    company_rep_mobile: '',
                    address: '',
                    email: '',
                }, {
                    bag: 'createCompany',
                })
            }
        },
        methods: {
            createCompany() {
                this.form.post('/back/companies', {
                    preserveScroll: true
                });
            },
            deleteCompany() {
                if (confirm(this.$t('words.are-you-sure'))) {
                    this.$inertia.delete('/back/companies/'+this.company.id);
                }
            }
        }
    }
</script>
