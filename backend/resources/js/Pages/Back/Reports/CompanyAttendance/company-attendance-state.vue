<template>
    <div>
        <div>
            <button @click="markAs('new_registration')"
                    class="btn btn-action"
                    :class="{'btn-action-active': trainee.pivot.status === 'new_registration'}">{{ $t('words.new-registration') }}</button>

            <button @click="markAs('temporary_stop')"
                    class="btn btn-action"
                    :class="{'btn-action-active': trainee.pivot.status === 'temporary_stop'}">
                {{ $t('words.temporary-stop') }}</button>

            <button @click="markAs('suspend_account')"
                    class="btn btn-action"
                    :class="{'btn-action-active': trainee.pivot.status === 'suspend_account'}">{{ $t('words.suspend-account') }}</button>

            <button @click="markAs('block')"
                    class="btn btn-action"
                    :class="{'btn-action-active': trainee.pivot.status === 'block'}">{{ $t('words.block') }}</button>
        </div>
        <input class="w-full border p-2 m-2" type="text" v-model="comment"  @blur="markAs(currentMarkAs)" :placeholder="$t('words.notes')">
    </div>
</template>

<script>
export default {
    name: "company-attendance-state.vue",
    props: ['trainee', 'report'],
    data() {
        return {
            currentMarkAs: 'new_registration',
            comment: '',
        }
    },
    mounted() {
        if (this.trainee) {
            this.currentMarkAs = this.trainee.pivot.status;
            this.comment = this.trainee.pivot.comment;
        }
    },
    methods: {
        markAs(state) {
            this.currentMarkAs = state;
            this.$inertia.post(route('back.reports.company-attendance.trainees.update', {
                report_id: this.report.id,
                trainee_id: this.trainee.id,
            }), {
                status: state,
                comment: this.comment,
            }, {
                preserveScroll: true
            })
        },
    },
}
</script>

<style scoped>
.btn-action {
    @apply inline-flex
    items-center
    px-4
    py-2
    bg-gray-200
    border
    border-transparent
    rounded-md
    font-semibold
    text-xs
    text-black
    uppercase
    tracking-normal
    transition
    ease-in-out
    duration-150
}
.btn-action-active {
    @apply bg-blue-500
    text-white
}
</style>
