<template>
    <div>
        warnings container
        <ul>
            <li v-for="warning in warnings">{{ warning.created_at }}</li>
        </ul>
    </div>
</template>

<script>
export default {
    props: ['trainee_id'],
    data() {
        return {
            warnings: [],
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
        }
    }
}
</script>

<style lang="sass">
//
</style>
