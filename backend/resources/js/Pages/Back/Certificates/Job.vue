<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'certificates', link: route('back.certificates.import')},
                    {title_raw: job.id}
                ]"
            ></breadcrumb-container>
            <div class="flex justify-between">
                <h1 class="mb-8 font-bold text-3xl">{{ $t('words.certificates') }}</h1>
            </div>
            <div class="w-1/3 mx-auto">
                <div class="w-full border-2 p-5 bg-gray-100 text-center">
                    <div v-if="job.completed_at">
                        <table class="w-full whitespace-no-wrap bg-white rounded-lg my-5 p-5 bg-gray-50 rounded shadow-lg text-sm">
                            <colgroup>
                                <col style="width: 100px">
                                <col style="width: 100px">
                            </colgroup>
                        	<tbody>
                                <tr class="border-t hover:bg-gray-100 focus-within:bg-gray-100">
                                    <td class="text-left px-4 py-4">{{ $t('words.status') }}</td>
                                    <td class="px-4 py-4">{{ job.status_text }}</td>
                                </tr>
                                <tr class="border-t hover:bg-gray-100 focus-within:bg-gray-100">
                                    <td class="text-left px-4 py-4">{{ $t('words.successful') }}</td>
                                    <td class="px-4 py-4">{{ job.rows_count }}</td>
                                </tr>
                                <tr class="border-t hover:bg-gray-100 focus-within:bg-gray-100">
                                    <td class="text-left px-4 py-4">{{ $t('words.failed') }}</td>
                                    <td class="px-4 py-4">
                                        <ul>
                                            <li v-for="row in job.failed_rows">{{ row }}</li>
                                        </ul>
                                    </td>
                                </tr>
                        	</tbody>
                        </table>

                        <button v-if="job.can_issue"
                                class="btn btn-secondary mt-5"
                                @click="sendToAll">{{ $t('words.issue-certificates') }}</button>
                    </div>
                    <div v-else>
                        <spinner></spinner>
                        <p>{{ $t('words.processing') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
    import Pagination from '@/Shared/Pagination'
    import AppLayout from '@/Layouts/AppLayout'
    import IconNavigate from 'vue-ionicons/dist/ios-arrow-dropright'
    import BreadcrumbContainer from "@/Components/BreadcrumbContainer";
    import EmptySlate from "@/Components/EmptySlate";
    import Spinner from '@/Components/Spinner';
    export default {
        metaInfo: { title: 'Certificates' },
        props: [
            'job',
        ],
        components: {
            EmptySlate,
            BreadcrumbContainer,
            IconNavigate,
            AppLayout,
            Pagination,
            Spinner,
        },
        data() {
            return {

            }
        },
        mounted() {
            let vm = this;
            if (!this.job.completed_at) {
                setTimeout(function() {
                    vm.pollJob();
                }, 2000);
            }
        },
        methods: {
            sendToAll() {
                if (confirm(this.$t('words.are-you-sure'))) {
                    this.$inertia.post(route('back.certificates.import.issue', this.job.id));
                }
            },
            pollJob() {
                let vm = this;
                this.$inertia.get(route('back.certificates.import.job', this.job.id))
                    .then(data => {
                        if (!this.job.completed_at) {
                            setTimeout(function() {
                                vm.pollJob();
                            }, 2000);
                        }
                    })
            }
        },
    }
</script>
