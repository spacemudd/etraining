<template>
    <div class="bg-white rounded shadow overflow-x-auto">
        <div class="flex justify-end mx-5">
            <inertia-link
                :href="`/back/trainees/${trainee_id}/invoices/create`"
                class="mt-5 bg-blue-600 p-2 text-white rounded"
            >
                {{ $t('words.issue-invoice') }}
            </inertia-link>
        </div>
        <table class="w-full whitespace-no-wrap">
        <colgroup>
            <col>
            <col>
            <col width="50px">
        </colgroup>
        <thead class="text-left font-bold">
            <th class="px-6 pt-6 pb-4">{{ $t('words.no') }}.</th>
            <th class="px-6 pt-6 pb-4">{{ $t('words.status') }}</th>
            <th class="px-6 pt-6 pb-4">{{ $t('words.status') }}</th>
        </thead>
        <tbody>
            <tr v-for="warning in warnings" class="hover:bg-gray-100 focus-within:bg-gray-100">
                <td class="border-t px-6 py-4">
                    <p>
                        {{ warning.attendance_report_record.course_batch_session.course.name_ar }}<br/>
                        <span dir="ltr">{{ warning.attendance_report_record.course_batch_session.starts_at_timezone | toDate }}</span><br/>
                        <span dir="ltr">{{ warning.attendance_report_record.course_batch_session.starts_at_timezone | toHours }}</span>
                    </p>
                </td>
                <td class="border-t px-6 py-4">{{ warning.attendance_report_record.absence_reason }}</td>
                <td class="border-t px-6 py-4">
                    <button @click="confirmDeletingWarning(warning.id)" class="bg-red-500 text-white font-semibold p-2 text-center rounded my-1">{{ $t('words.delete') }}</button>
                </td>
            </tr>
            <tr v-if="!warnings.length">
                <td colspan="3" class="border-t text-center py-5 text-gray-500">{{ $t('words.no-records-have-been-found') }}</td>
            </tr>
        </tbody>
    </table>
    </div>
</template>

<script>
import EmptySlate from "./EmptySlate";

export default {
    components: [
        EmptySlate,
    ],
    props: ['trainee_id'],
    data() {
        return {
            warnings: [],
        }
    },
    filters: {
        toDate(timestamp) {
            return moment(timestamp, 'YYYY-MM-DD LT').format('DD-MM-YYYY');
        },
        toHours(timestamp) {
            return moment(timestamp, 'YYYY-MM-DD LT').format('hh:mm A');
        }
    },
    mounted() {
        this.getWarnings();
    },
    methods: {
        getWarnings() {
            axios.get(route('back.trainees.warnings', {trainee_id: this.trainee_id}))
            .then(response => {
                this.warnings = response.data;
            })
        },
        confirmDeletingWarning(warningId) {
            if (confirm(this.$t('words.are-you-sure'))) {
                axios.delete(route('back.trainees.warnings.delete', {trainee_id: this.trainee_id, id: warningId}))
                    .then(response => {
                        this.getWarnings();
                    })
            }
        },
        removeAll() {
            if (confirm(this.$t('words.are-you-sure'))) {
                this.$inertia.delete(route('back.trainees.warnings.delete.all', {trainee_id: this.trainee_id}))
            }
        }
    }
}
</script>

<style lang="sass">
//
</style>
