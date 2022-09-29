<template>
    <app-layout>
        <div class="container px-6 mx-auto grid">
            <div class="grid grid-cols-2 gap-6 mt-5">
                <div class="col">
                    <form @submit.prevent="submitForm">
                        <jet-label for="course_name" :value="$t('words.course')" />
                        <jet-input id="course_name" type="text" class="mt-1 block w-full" v-model="createComplaintForm.course_name" autocomplete="off" autofocus="on" />
                        <div class="mt-5">
                            <jet-label for="course_instructor" :value="$t('words.instructor')" />
                            <jet-input id="course_instructor" type="text" class="mt-1 block w-full" v-model="createComplaintForm.course_instructor" autocomplete="off" />
                        </div>

                        <div class="mt-5">
                            <jet-label for="message" :value="$t('words.message')" />
                            <jet-textarea id="message" type="textarea" class="mt-1 block w-full" v-model="createComplaintForm.message" />
                        </div>

                        <jet-button  class="mt-5" :class="{ 'opacity-25': createComplaintForm.processing }" :disabled="createComplaintForm.processing">
                            {{ $t('words.send') }}
                        </jet-button>
                    </form>
                </div>
                <div class="col">
                    <div class="px-20">
                        <img src="/img/ptc_complaints_v2.jpg" class="shadow-lg rounded-lg" alt="PTCComplaints">
                    </div>
                </div>
            </div>

        </div>
        <div>
            <div class="overflow-x-auto">
                <Table
                    class="mt-5 w-full whitespace-no-wrap"
                    :filters="queryBuilderProps.filters"
                    :search="queryBuilderProps.search"
                    :columns="queryBuilderProps.columns"
                    :on-update="setQueryBuilder"
                    :meta="complaints"
                >
                    <template #head>
                        <tr>
                            <th class="rtl:text-right font-weight-bold" @click.prevent="sortBy('created_at')">{{ $t('words.course') }}</th>
                            <th class="rtl:text-right font-weight-bold">{{ $t('words.instructor') }}</th>
                            <th class="rtl:text-right font-weight-bold">{{ $t('words.message') }}</th>
                            <th class="rtl:text-right font-weight-bold">{{ $t('words.order-date') }}</th>
                        </tr>
                    </template>

                    <template #body>
                        <tr v-for="trainees_complaint in trainees_complaints.data" :key="trainees_complaint.id"
                            v-if="trainees_complaint.complaints_status === 0">
                            <td class="rtl:text-right text-black">
                                <inertia-link :href="route('complaints.Show', trainees_complaint.id)">
                                    {{ complaints.course_name }}
                                </inertia-link>
                            </td>
                            <td class="rtl:text-right text-black">
                                <inertia-link :href="route('complaints.Show', trainees_complaint.id)">
                                    {{ complaints.course_instructor }}
                                </inertia-link>
                            </td>
                            <td class="rtl:text-right text-black">
                                <inertia-link :href="route('complaints.Show', trainees_complaint.id)">
                                    {{ complaints.message }}
                                </inertia-link>
                            </td>
                            <td class="rtl:text-right text-black">
                                <inertia-link :href="route('complaints.Show', trainees_complaint.id)">
                                    {{ complaints.created_at }}
                                </inertia-link>
                            </td>
                        </tr>
                    </template>
                </Table>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayoutComplaints'
import Welcome from '@/Jetstream/Welcome'
import LanguageSelector from "@/Shared/LanguageSelector";
import HeaderCard from "@/Components/HeaderCard";
import JetLabel from '@/Jetstream/Label';
import JetInput from '@/Jetstream/Input';
import JetInputError from '@/Jetstream/InputError';
import JetTextarea from '@/Jetstream/Textarea';
import JetButton from '@/Jetstream/Button';
import {Components, InteractsWithQueryBuilder} from "@protonemedia/inertiajs-tables-laravel-query-builder";
import throttle from "lodash/throttle";
import pickBy from "lodash/pickBy";

export default {
    mixins: [InteractsWithQueryBuilder],
    metaInfo: { title: 'Complaints complaints' },

    components: {
        AppLayout,
        Welcome,
        LanguageSelector,
        HeaderCard,
        JetLabel,
        JetInput,
        JetInputError,
        JetTextarea,
        JetButton,
    },
    props: {
        complaints: Object,
        filters: Object,
    },
    watch: {
        form: {
            handler: throttle(function() {
                let query = pickBy(this.form)
                this.$inertia.replace(this.route('trainees-complaints.index', Object.keys(query).length ? query : { remember: 'forget' }))
            }, 150),
            deep: true,
        },
    },
    data() {
        return {
            createComplaintForm: this.$inertia.form({
                course_name: '',
                course_instructor: '',
                message: '',
            }, {
                bag: 'createComplaintForm',
            }),
        }
    },
    mounted() {
        let vm = this;
        Components.Pagination.setTranslations({
            no_results_found: vm.$t('words.no-records-have-been-found'),
            previous: vm.$t('pagination.previous'),
            next: vm.$t('pagination.next'),
            to: vm.$t('pagination.to'),
            of: vm.$t('pagination.of'),
            results: vm.$t('pagination.results'),
        });
    },
    methods: {
        submitForm() {
            this.createComplaintForm.post(route('trainees-complaints.store'));
        }
    },
    beforeDestroy() {

    }
}
</script>
