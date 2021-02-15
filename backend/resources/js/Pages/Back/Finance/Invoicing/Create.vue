<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'finance', link: route('back.finance')},
                    {title: 'monthly-invoicing', link: route('back.finance.invoicing.index')},
                    {title: 'new'},
                ]"
            ></breadcrumb-container>

            <div class="mt-4">
                <jet-form-section @submitted="createCourse">
                    <template #title>
                        {{ $t('words.invoice-current-students-for-the-month') }}
                    </template>

                    <!--<template #description>-->
                    <!--</template>-->

                    <template #form>
                        <div class="col-span-6 sm:col-span-2">
                            <jet-label for="invoice_date" :value="$t('words.invoices-date')" />
                            <jet-input id="invoice_date" type="text" class="mt-1 block w-full" v-model="invoiceDate" disabled />
                        </div>
                    </template>

                    <template #actions>
                        <jet-action-message :on="form.recentlySuccessful" class="mr-3">
                            {{ $t('words.saved-successfully') }}
                        </jet-action-message>

                        <inertia-link :href="route('back.finance.invoicing.index')" class="flex items-center justify-start rtl:ml-4 ltr:mr-4 rounded-md px-4 py-2 bg-white hover:bg-gray-300 text-right">
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
    import JetDialogModal from '@/Jetstream/DialogModal'
    import JetInput from '@/Jetstream/Input'
    import JetInputError from '@/Jetstream/InputError'
    import JetActionMessage from '@/Jetstream/ActionMessage';
    import JetButton from '@/Jetstream/Button';
    import JetFormSection from '@/Jetstream/FormSection';
    import JetLabel from '@/Jetstream/Label';
    import JetTextarea from '@/Jetstream/Textarea';
    import BreadcrumbContainer from "@/Components/BreadcrumbContainer";

    export default {
        props: [],

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
            JetTextarea,
            BreadcrumbContainer,
        },
        computed: {
            rtl() {
                let lang = document.documentElement.lang.substr(0, 2);
                return lang === 'ar';
            },
            invoiceDate() {
                return moment().startOf('month').format('YYYY-MM-DD');
            },
        },
        data() {
            return {
                form: this.$inertia.form({

                }, {
                    bag: 'createInvoicingBatch',
                })
            }
        },
        methods: {
            fileAdded(file) {
                this.form.training_package = file;
            },
            createCourse() {
                this.form.post(route('back.finance.invoicing.store'), {
                    preserveScroll: true
                });
            },
        }
    }
</script>
