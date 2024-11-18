<template>
    <div class="border-2 border-green-600 rounded w-full p-3">
        <div class="flex">
            <div class="flex">
                <img src="/img/gosi.png" alt="gosi" class="p-3" style="max-width: 130px">
            </div>
            <div class="w-full">
                <div v-if="!data" class="flex justify-center align-middle">
                    <btn-loading-indicator v-if="$wait.is('LOADING_GOSI')" />
                    <p class="text-xs" v-else>الرجاء الضغط على 'تحديث' لطلب البيانات</p>
                </div>
                <div v-if="data && data.errorText">{{ data.errorText }}</div>
                <template v-if="data && !data.errorText">
                    <table class="w-full text-xs self-center" v-for="record in data.employmentStatusInfo">
                        <colgroup>
                            <col style="width:110px">
                            <col>
                            <col style="width:120px">
                        </colgroup>
                        <tbody>
                        <tr>
                            <td class="border font-bold bg-gray-100">{{ $t('words.updated-date') }}</td>
                            <td class="border">{{ new Date().getDay() + '-' + new Date().getMonth() + '-' + new Date().getFullYear() }}</td>
                        </tr>
                        <tr>
                            <td class="border font-bold bg-gray-100">{{ $t('words.identity_number') }}</td>
                            <td class="border">{{ ninOrIqama }}</td>
                            <td class="border font-bold bg-gray-100">{{ $t('words.full-name') }}</td>
                            <td class="border">{{ record.fullName }}</td>
                        </tr>
                        <tr>
                            <td class="border font-bold bg-gray-100">{{ $t('words.full-wage') }}</td>
                            <td class="border">{{ record.fullWage }}</td>
                            <td class="border font-bold bg-gray-100">{{ $t('words.employer-name') }}</td>
                            <td class="border">{{ record.employerName }} ({{ record.commercialRegistrationNumber }})</td>
                        </tr>
                        <tr>
                            <td class="border font-bold bg-gray-100">{{ $t('words.salary-starting-date') }}</td>
                            <td class="border">{{ record.salaryStartingDate }}</td>
                            <td class="border font-bold bg-gray-100">{{ $t('words.employment-status') }}</td>
                            <td class="border">{{ record.employmentStatus }}</td>
                        </tr>
                        </tbody>
                    </table>
                </template>
            </div>
            <button class="btn-primary self-center mx-2 mt-2" @click="fetch">{{ $t('words.refresh') }}</button>
        </div>

    </div>
</template>

<script>
import BtnLoadingIndicator from './BtnLoadingIndicator.vue'
export default {
    components: {
        BtnLoadingIndicator
    },
    name: "GosiContainer",
    props: ['ninOrIqama'],
    data() {
        return {
            loading: false,
            data: null,
        }
    },
    methods: {
        fetch() {
            this.$wait.start('LOADING_GOSI')
            axios.post(route('back.gosi.show'), {ninOrIqama: this.ninOrIqama})
                .then(response => {
                    this.data = response.data;
                    this.$wait.end('LOADING_GOSI')
                    console.log(this.data);
                })
                .catch(error => {
                    this.$wait.end('LOADING_GOSI')
                    console.log(error);
                })
        }
    },
}
</script>

<style scoped>

</style>
