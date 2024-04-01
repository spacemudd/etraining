<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'companies', link: route('back.companies.index')},
                    {title: 'new'},
                ]"
            ></breadcrumb-container>

            <div class="mt-4">
                <jet-form-section @submitted="createCompany">
                    <template #title>
                        {{ $t('words.open-new-company-file') }}
                    </template>

                    <template #description>
                        {{ $t('words.open-new-company-file-description') }}
                    </template>

                    <template #form>
                        <div class="col-span-2 sm:col-span-2">
                            <jet-label for="center_id" :value="$t('words.center')" />
                            <div class="relative">
                                <select class="block appearance-none w-full border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                        v-model="form.center_id"
                                        id="educational_level_id">
                                    <option v-for="center in centers" :key="center.id" :value="center.id">{{ center.name_ar }}</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                                </div>
                            </div>
                            <jet-input-error :message="form.error('center_id')" class="mt-2" />
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
                            'monthly_subscription_per_trainee',
                            'salesperson_email',
                            ]">
                            <div class="col-span-2 sm:col-span-2">
                                <jet-label for="name" :value="$t('words.'+fieldName)" />
                                <jet-input :id="fieldName" :name="fieldName" type="text" class="mt-1 block w-full" v-model="form[fieldName]" autocomplete="off" :autofocus="fieldName==='name_ar'" />
                                <jet-input-error :message="form.error(fieldName)" class="mt-2" />
                            </div>
                        </template>
                        <div class="col-span-4 sm:col-span-4">
                            <jet-label for="region_id" :value="$t('words.region')" />
                            <div class="relative mt-2">
                                <select class="block appearance-none w-full border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                        v-model="form.region_id"
                                        id="region_id">
                                    <option v-for="region in regions"
                                            :key="region.id"
                                            :value="region.id">
                                        {{ region.name }}
                                    </option>
                                </select>
                            </div>
                            <jet-input-error :message="form.error('region_id')" class="mt-2" />
                        </div>
                    </template>

                    <template #actions>
                        <jet-action-message :on="form.recentlySuccessful" class="mr-3">
                            {{ $t('words.saved-successfully') }}
                        </jet-action-message>

                        <inertia-link href="/back/companies" class="flex items-center justify-start rtl:ml-4 ltr:mr-4 rounded-md px-4 py-2 bg-white hover:bg-gray-300 text-right">
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
    import BreadcrumbContainer from "@/Components/BreadcrumbContainer";

    export default {
        props: ['sessions', 'regions', 'centers'],

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
                form: this.$inertia.form({
                    name_ar: '',
                    name_en: '',
                    cr_number: '',
                    contact_number: '',
                    company_rep: '',
                    company_rep_mobile: '',
                    address: '',
                    email: '',
                    monthly_subscription_per_trainee: '',
                    salesperson_email: '',
                    region_id: '',
                    center_id: '',
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
