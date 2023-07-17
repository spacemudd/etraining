<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'orders', link: route('orders.index')},
                    {title: 'resignations', link: route('orders.resignations.index')},
                    {title: 'new'},
                ]"
            ></breadcrumb-container>

            <div class="mt-4">
                <jet-form-section @submitted="createTrainee">
                    <template #title>
                        {{ $t('words.create-new-resignation-form') }}
                    </template>

                    <template #description>
                        {{ $t('words.create-new-resignation-form-help') }}
                    </template>

                    <template #form>
                        <div class="col-span-4 sm:col-span-4">
                            <jet-label for="company_id" :value="$t('words.company')" />
                            <select-company class="mt-2"
                                            v-model="company"
                                            :required="true"
                            />
                            <jet-input-error class="mt-2" />
                        </div>

                        <div class="col-span-4 sm:col-span-4">
                            <table class="table-auto w-full text-xs">
                                <colgroup>
                                    <col style="width:10px;">
                                    <col style="width:200px;">
                                    <col style="width:100px;">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th class="text-center border-black border bg-gray-300 px-2">#</th>
                                        <th class="text-right border-black border bg-gray-300 px-2">{{ $t('words.name') }}</th>
                                        <th class="text-right border-black border bg-gray-300 px-2">{{ $t('words.identity_number') }}</th>
                                        <th class="text-right border-black border bg-gray-300 px-2">{{ $t('words.current-company-as-of-today') }}</th>
                                    </tr>
                                </thead>
                            	<tbody>
                            			<tr>
                                            <td class="text-center border-black border px-2">1</td>
                            				<td class="border-black border px-2">Shafiq</td>
                                            <td class="border-black border px-2">19191991100</td>
                                            <td class="border-black border px-2">Clarastars</td>
                            			</tr>
                            	</tbody>
                            </table>
                        </div>

                        <div class="col-span-4 sm:col-span-4">
                            <hr>
                        </div>
                    </template>

                    <template #actions>
                        <jet-action-message :on="form.recentlySuccessful" class="mr-3">
                            {{ $t('words.saved-successfully') }}
                        </jet-action-message>

                        <inertia-link href="/back/trainees" class="flex items-center justify-start rtl:ml-4 ltr:mr-4 rounded-md px-4 py-2 bg-white hover:bg-gray-300 text-right">
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
    import SelectCompany from "@/Components/SelectCompany";
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
    import debounce from 'lodash/debounce'
    import SelectTraineeGroup from "@/Components/SelectTraineeGroup";
    import 'selectize/dist/js/standalone/selectize.min';

    export default {
        props: [
            'sessions',
            'companies',
        ],

        components: {
            SelectCompany,
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
                company: null,
                trainees: [],
                addressSearch: '',
                form: this.$inertia.form({
                    company_id: '',
                    emails: [],
                    trainees: [],
                }, {
                    bag: 'createTrainee',
                })
            }
        },
        mounted() {
        },
        methods: {
            createTrainee() {
                this.form.post('/back/trainees', {
                    preserveScroll: true
                }).catch(error => {
                    this.form.processing = false;
                }).finally(() => {
                    this.form.processing = false;
                });
            },
            findAddress: debounce(function () {
                axios.get('/api/location-lookup?search='+this.addressSearch)
            }, 200),
        }
    }
</script>
