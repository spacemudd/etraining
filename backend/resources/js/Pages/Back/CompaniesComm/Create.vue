<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'companies', link: route('back.companies.index')},
                    {title_raw: company.name_ar},
                    {title: 'new-visit'}
                ]"
            ></breadcrumb-container>


            <form class="w-full max-w-lg">
                <div class="flex flex-wrap -mx-3 mb-6">
                    <div class="w-full px-3">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                            {{ $t('words.date') }}
                        </label>
                        <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                               id="occur_at" type="datetime-local">
                        <!--<p class="text-gray-600 text-xs italic">Helper text</p>-->
                    </div>
                </div>

                <div class="flex flex-wrap -mx-3 mb-6">
                    <div class="w-full px-3">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="grid-password">
                            {{ $t('words.result-of-comm') }}
                        </label>
                        <div class="inline-block relative w-full">
                            <select class="block appearance-none w-full bg-white border border-gray-400 hover:border-gray-500 px-4 py-2 pr-8 rounded shadow leading-tight focus:outline-none focus:shadow-outline">
                                <option value="">{{ $t('words.not-interested') }}</option>
                                <option value="">{{ $t('words.interested-set-meeting') }}</option>
                                <option value="">{{ $t('words.interested-set-offer') }}</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap -mx-3 mb-6">
                    <div class="w-full px-3">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="grid-password">
                            {{ $t('words.comment') }}
                        </label>
                        <textarea id="message"
                                  rows="4"
                                  class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                  placeholder=""></textarea>
                    </div>
                </div>

                <hr class="mb-5">

                <div class="flex flex-wrap mx-3 mb-6">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                        {{ $t('words.assign-people') }}
                    </label>
                </div>

                <div class="flex flex-wrap mx-3 mb-6">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                        {{ $t('words.contact-point') }}
                    </label>
                </div>

                <button class="bg-blue-600 text-white rounded p-2 text-sm">{{ $t('words.save') }}</button>
            </form>
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
        props: ['company'],
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
                }, {
                    bag: 'createCompanyComm',
                })
            }
        },
        methods: {
            save() {
                this.form.post(route('companies.comms.store', this.company.id), {
                    preserveScroll: true
                });
            },
        }
    }
</script>
