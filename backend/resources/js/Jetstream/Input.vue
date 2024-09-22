<template>
    <input
        class="form-input rounded-md shadow-sm disabled:bg-gray-100 disabled:cursor-not-allowed"
        :value="value"
        @blur="$emit('blur', $event.target.value)"
        @input="$emit('input', $event.target.value)"
        ref="input"
    >
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

