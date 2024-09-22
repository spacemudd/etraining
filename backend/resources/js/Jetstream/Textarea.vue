<template>
    <textarea class="resize border rounded focus:outline-none focus:shadow-outline p-2 rounded-md shadow-sm" :value="value" @input="$emit('input', $event.target.value)" ref="input"></textarea>
</template>

<script>
    export default {
        props: ['value'],

        methods: {
            focus() {
                this.$refs.input.focus()
            }
        }
    }
</script>

