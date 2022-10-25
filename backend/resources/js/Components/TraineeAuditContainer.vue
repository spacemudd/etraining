<template>
    <div>
        <table class="w-full whitespace-no-wrap">
            <colgroup>
                <col>
            </colgroup>
        <thead>
        <tr class="text-left font-bold">
        	<th class="border px-6 pt-6 pb-4">{{ $t('words.audit') }}</th>
            <th class="border px-6 pt-6 pb-4">{{ $t('words.old-values') }}</th>
            <th class="border px-6 pt-6 pb-4">{{ $t('words.new-values') }}</th>
        </tr>
        </thead>
        	<tbody v-if="loaded">
        			<tr v-for="(audit, key) in audits" :key="audit.id"
                        class="border"
                        :class="{'bg-gray-200':(key%2)}">
                        <td class="border text-right p-2" dir="ltr">
                            {{ audit.user ? audit.user.name : '-' }}<br/>
                            {{ audit.event }}<br/>
                            {{ audit.created_at_human }}
                        </td>
                        <td class="border">
                            <table class="table table-bordered table-hover" style="width:100%">
                                <colgroup>
                                    <col style="width:50%">
                                </colgroup>
                                <tr v-for="(old_value, attribute) in audit.old_values">
                                    <template v-if="attribute === 'company_id'">
                                        <td class="border">{{ attribute }}</td>
                                        <td class="border">
                                            <inertia-link v-if="old_value" class="text-blue-500 hover:text-blue-800" :href="route('back.companies.show', {company: old_value})">{{ old_value.substring(0, 8) }}</inertia-link>
                                        </td>
                                    </template>
                                    <template v-else>
                                        <td class="border">{{ attribute }}</td>
                                        <td class="border">{{ old_value }}</td>
                                    </template>
                                </tr>
                            </table>
                        </td>
                        <td class="border">
                            <table class="table table-bordered table-hover" style="width:100%">
                                <colgroup>
                                    <col style="width:50%">
                                </colgroup>
                                <tr v-for="(new_value, attribute) in audit.new_values">
                                    <template v-if="attribute === 'company_id'">
                                        <td class="border">{{ attribute }}</td>
                                        <td class="border">
                                            <inertia-link v-if="new_value" class="text-blue-500 hover:text-blue-800" :href="route('back.companies.show', {company: new_value})">
                                                {{ new_value.substring(0, 8) }}
                                            </inertia-link>
                                        </td>
                                    </template>
                                    <template v-else>
                                        <td class="border">{{ attribute }}</td>
                                        <td class="border">{{ new_value }}</td>
                                    </template>
                                </tr>
                            </table>
                        </td>
        			</tr>
        	</tbody>
        </table>
    </div>
</template>

<script>
export default {
    name: 'TraineeAuditContainer',
    props: [
        'trainee_id',
    ],
    data() {
        return {
            audits: [],
            loaded: false,
        }
    },
    mounted() {
        let vm = this;
        setTimeout(function() {
            vm.getAuditRecords();
        }, 200)
    },
    methods: {
        getAuditRecords() {
            axios.get(route('back.trainees.audit', this.trainee_id))
            .then(response => {
                this.audits = response.data;
                this.loaded = true;
            }).catch(error => {
                this.loaded = true;
            })
        },
    }
}
</script>

<style lang="sass">
//
</style>

