<template>
    <div v-if="validationErrors" class="bg-red-100 border border-red-400 text-red-700 px-10 py-3 rounded relative mb-10">
        <ul class="list-disc">
            <li v-for="(value, key, index) in validationErrors">{{ value }}</li>
        </ul>
    </div>
</template>

<script>
export default {
    props: ['errors'],
    data() {
        return {
            //
        }
    },
    mounted() {
        //
    },
    methods: {
        //
    },
    computed: {
        validationErrors(){
            let errors = Object.values(this.errors);
            errors = errors.flat();
            return errors;
        }
    }
}
</script>

<style lang="sass">
//
</style>
