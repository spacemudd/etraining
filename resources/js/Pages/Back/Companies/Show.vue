<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <!--<breadcrumb/>-->
            <div class="grid grid-cols-4 gap-6">
                <div class="col-span-4 flex items-center justify-end bg-gray-50 text-right">
                    <inertia-link :href="`/back/companies/${this.company.id}/edit`" class="flex items-center justify-start rounded-md px-4 py-2 bg-gray-200 hover:bg-gray-300 text-right">
                        {{ $t('words.edit') }}
                    </inertia-link>
                    <!--<inertia-link href="/back/companies/${this.company}" class="flex items-center justify-start rtl:ml-4 ltr:mr-4 rounded-md px-4 py-2 bg-red-200 hover:bg-gray-300 text-right">-->
                    <!--    {{ $t('words.archive') }}-->
                    <!--</inertia-link>-->
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
                        <jet-input id="name" type="text" class="mt-1 block w-full bg-gray-200" :value="company[fieldName]" disabled />
                    </div>
                </template>
            </div>


            <jet-section-border></jet-section-border>
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from './../../../Layouts/AppLayout'
    import JetSectionBorder from './../../../Jetstream/SectionBorder'
    import Breadcrumb from "../../../Components/Breadcrumb";
    import JetDialogModal from './../../../Jetstream/DialogModal'
    import JetInput from './../../../Jetstream/Input'
    import JetInputError from './../../../Jetstream/InputError'
    import JetActionMessage from './../../../Jetstream/ActionMessage';
    import JetButton from './../../../Jetstream/Button';
    import JetFormSection from './../../../Jetstream/FormSection';
    import JetLabel from './../../../Jetstream/Label';

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
        }
    }
</script>
