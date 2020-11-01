<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'companies', link: route('back.companies.index')},
                    {title_raw: company.name_ar, link: route('back.companies.index', {company_id: company.id})},
                    {title: 'edit'},
                ]"
            ></breadcrumb-container>

            <div class="mt-4">
                <jet-form-section @submitted="updateCompany">
                    <template #title>
                        {{ $t('words.edit') }}
                    </template>

                    <template #form>
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
                            <div class="col-span-2 sm:col-span-2">
                                <jet-label for="name" :value="$t('words.'+fieldName)" />
                                <jet-input id="name" type="text" class="mt-1 block w-full" v-model="form[fieldName]" autocomplete="name" :autofocus="fieldName==='name_ar'" />
                                <jet-input-error :message="form.error(fieldName)" class="mt-2" />
                            </div>
                        </template>
                    </template>

                    <template #actions>
                        <jet-action-message :on="form.recentlySuccessful" class="mr-3">
                            Saved.
                        </jet-action-message>

                        <inertia-link :href="`/back/companies/${company.id}`" class="flex items-center justify-start rtl:ml-4 ltr:mr-4 rounded-md px-4 py-2 bg-white hover:bg-gray-300 text-right">
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
        props: ['sessions', 'company'],

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
            this.form.name_ar = this.company.name_ar;
            this.form.name_en = this.company.name_en;
            this.form.cr_number = this.company.cr_number;
            this.form.contact_number = this.company.contact_number;
            this.form.company_rep = this.company.company_rep;
            this.form.company_rep_mobile = this.company.company_rep_mobile;
            this.form.email = this.company.email;
            this.form.address = this.company.address;
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
                    bag: 'updateCompany',
                })
            }
        },
        methods: {
            updateCompany() {
                this.form.put('/back/companies/'+this.company.id, {
                    preserveScroll: true
                });
            },
        }
    }
</script>
