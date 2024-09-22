<template>
    <button @click="postTrainees"
            class="inline-flex items-center px-4 py-2 bg-orange-300 border border-transparent rounded-md font-semibold text-xs text-black uppercase ltr:tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150">
        {{ $t('words.post-trainees') }}
    </button>
</template>

<script>
export default {
    name: "PostTraineesButton",
    props: ['companyId'],
    methods: {
        postTrainees() {
            if (confirm(this.$t('words.are-you-sure'))) {
                this.$inertia.post(route('back.companies.post-trainees', {company_id: this.companyId}));
            }
        }
    },
}
</script>

<style scoped>

</style>
