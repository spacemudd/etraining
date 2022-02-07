<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'finance', link: route('back.finance')},
                    {title: 'account-statements'},
                ]"
            ></breadcrumb-container>

            <div class="grid md:grid-cols-1 grid-cols-1 gap-6 flex justify-center">
                <div class="bg-white shadow-lg p-5 mx-auto w-1/2">
                    <div class="mb-10">
                        <p class="text-center">
                            <span class="bg-gray-400 text-white rounded p-2">{{ $t('words.account-statements') }}</span>
                        </p>
                    </div>

                    <form class="mt-5" :action="route('back.finance.account-statements.excel')" method="get">
                        <div>
                            <jet-label for="company_id" :value="$t('words.company')"/>
                            <select
                                ref="company_id"
                                name="company_id"
                                id="company_id">
                                <option value=""></option>
                                <option v-for="company in companies" :key="company.id" :value="company.id">
                                    {{ company.name_ar }}
                                </option>
                            </select>

                            <jet-input-error :message="form.error('company_id')" class="mt-2" />
                        </div>

                        <div>
                            <jet-label for="trainee_id" :value="$t('words.trainee')"/>
                            <select
                                ref="trainee_id"
                                name="trainee_id"
                                id="trainee_id">
                                <option value=""></option>
                                <option v-for="trainee in trainees" :key="trainee.id" :value="trainee.id">
                                    {{ trainee.name }}
                                </option>
                            </select>

                            <jet-input-error :message="form.error('trainee_id')" class="mt-2" />
                        </div>

                        <progress v-if="form.progress" :value="form.progress.percentage" max="100">
                            {{ form.progress.percentage }}%
                        </progress>
                        <button v-else
                                class="w-full text-center mt-5 items-center px-4 py-2 bg-blue-500 hover:bg-blue-700 active:bg-blue-900 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150 disabled:cursor-not-allowed mx-"
                                type="submit">
                            {{ $t('words.export') }}
                        </button>
                    </form>

                </div>
            </div>


        </div>
    </app-layout>
</template>

<script>
    // import Icon from '@/Shared/Icon'
    // import Layout from '@/Shared/Layout'
    import mapValues from 'lodash/mapValues'
    import Pagination from '@/Shared/Pagination'
    import pickBy from 'lodash/pickBy'
    // import SearchFilter from '@/Shared/SearchFilter'
    import throttle from 'lodash/throttle'
    import AppLayout from '@/Layouts/AppLayout'
    import IconNavigate from 'vue-ionicons/dist/ios-arrow-dropright'
    import EmptySlate from "@/Components/EmptySlate";
    import BreadcrumbContainer from "@/Components/BreadcrumbContainer";
    import JetInput from '@/Jetstream/Input'
    import JetInputError from '@/Jetstream/InputError'
    import JetLabel from '@/Jetstream/Label';
    import 'selectize/dist/js/standalone/selectize.min';

    export default {
        metaInfo: { title: 'Financial invoices' },
        components: {
            BreadcrumbContainer,
            IconNavigate,
            AppLayout,
            // Icon,
            Pagination,
            // SearchFilter,
            EmptySlate,
            JetInput,
            JetInputError,
            JetLabel,
        },
        props: [
          'companies',
          'trainees',
        ],
        data() {
            return {
                form: this.$inertia.form({
                    company_id: null,
                    trainee_id: null,
                }, {
                    bag: 'accountStatementReport',
                })
            }
        },
        mounted() {
            let vm = this;
            $(document).ready(function () {
                vm.companySelector = $('#company_id').selectize({
                    sortField: 'text',
                    maxOptions: 9999,
                    onChange: function (value) {
                        vm.form.company_id = value;
                        vm.form.trainee_id = null;
                        $('#trainee_id').selectize()[0].selectize.clear();
                    }
                })
            });

            $(document).ready(function () {
                vm.traineeSelector = $('#trainee_id').selectize({
                    sortField: 'text',
                    maxOptions: 9999,
                    onChange: function (value) {
                        vm.form.company_id = null;
                        vm.form.trainee_id = value;
                        $('#company_id').selectize()[0].selectize.clear();
                    }
                })
            });
        },
        beforeDestroy() {
            $(document).ready(function () {
                $('#company_id').selectize()[0].selectize.destroy();
                $('#trainee_id').selectize()[0].selectize.destroy();
            });
        },
        methods: {
            saveForm() {
                this.form.post()
            },
        },
    }
</script>
