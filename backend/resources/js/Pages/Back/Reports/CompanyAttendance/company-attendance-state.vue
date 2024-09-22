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

            <inertia-link class="btn btn-action bg-black-500" :href="route('back.reports.company-attendance.individual', {id: report.id, trainee_id: trainee.id})">
                {{ $t('words.individual-report') }}
            </inertia-link>

        </div>
        <div class="mt-2">
            <button @click.prevent="addManualAttendance=!addManualAttendance"
                    v-if="(currentMarkAs==='new_registration' || currentMarkAs==='temporary_stop')"
                    :class="{'text-blue-500': addManualAttendance}"
                    class="font-weight-bold font-bold">+ {{ $t('words.add-manual-attendance') }}</button>
        </div>
        <date-range-picker
            v-if="addManualAttendance"
            class="w-full p-2"
            ref="picker"
            :locale-data="{ firstDay: 1, format: 'dd-mm-yyyy' }"
            :singleDatePicker="false"
            :timePicker="false"
            :showWeekNumbers="true"
            :showDropdowns="true"
            :autoApply="false"
            style="max-width:415px;"
            v-model="period">
            <template v-slot:input="picker" style="max-width:400px;">
                {{ picker.startDate | date }} - {{ picker.endDate | date }}
            </template>
       </date-range-picker>
       <input class="w-full border p-2 m-2" style="max-width:400px" type="text" v-model="comment"  @blur="markAs(currentMarkAs)" :placeholder="$t('words.notes')">
    </div>
</template>

<script>
import DateRangePicker from 'vue2-daterange-picker'
import 'vue2-daterange-picker/dist/vue2-daterange-picker.css'

export default {
    name: "company-attendance-state.vue",
    components: {
        DateRangePicker,
    },
    props: ['trainee', 'report'],
    data() {
        var date = new Date(), y = date.getFullYear(), m = date.getMonth();
        var startDate = new Date(y, m, 1);
        var endDate = new Date(y, m + 1, 0);
        return {
            currentMarkAs: 'new_registration',
            comment: '',
            period: {startDate, endDate},
            addManualAttendance: false,
        }
    },
    filters: {
        date (val) {
            return val ? val.toLocaleString('en-GB', {year: 'numeric', month: '2-digit', day: '2-digit'}) : ''
        }
    },
    mounted() {
        if (this.trainee) {
            this.currentMarkAs = this.trainee.pivot.status;
            this.comment = this.trainee.pivot.comment;
            if (this.trainee.pivot.start_date) {
                this.addManualAttendance = true;
            }
        }
    },
    watch: {
        period() {
            this.updatePeriod();
        },
        addManualAttendance() {
            if (!this.addManualAttendance) {
                this.clearPeriod();
            }
        }
    },
    methods: {
        markAs(state) {
            if (this.currentMarkAs === state) {
                this.clearStatus();
                return;
            }
            this.currentMarkAs = state;

            if (this.currentMarkAs === 'suspend_account' || this.currentMarkAs === 'block') {
                this.$inertia.post(route('back.reports.company-attendance.trainees.update', {
                    report_id: this.report.id,
                    trainee_id: this.trainee.id,
                }), {
                    status: state,
                    comment: this.comment,
                    start_date: null,
                    end_date: null,
                }, {
                    preserveScroll: true
                })
            } else {
                this.$inertia.post(route('back.reports.company-attendance.trainees.update', {
                    report_id: this.report.id,
                    trainee_id: this.trainee.id,
                }), {
                    status: state,
                    comment: this.comment,
                }, {
                    preserveScroll: true
                })
            }
        },
        clearStatus() {
            this.$inertia.post(route('back.reports.company-attendance.trainees.update', {
                report_id: this.report.id,
                trainee_id: this.trainee.id,
            }), {
                status: null,
                comment: this.comment,
            }, {
                preserveScroll: true
            })
        },
        updatePeriod() {
            this.$inertia.post(route('back.reports.company-attendance.trainees.update', {
                report_id: this.report.id,
                trainee_id: this.trainee.id,
            }), {
                status: this.currentMarkAs,
                comment: this.comment,
                start_date: this.period.startDate,
                end_date: this.period.endDate,
            }, {
                preserveScroll: true
            })
        },
        clearPeriod() {
            if (!this.addManualAttendance) {
                this.period.startDate = null;
                this.period.endDate = null;
            }
            this.updatePeriod();
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

.vue-daterange-picker {
    display: block !important;
}

.reportrange-text {
    max-width: 400px !important;
    margin-right: 8px !important;
}
</style>
