<template>
    <img :src="link" alt="Logo">
</template>

<script>
    export default {
        data() {
            return {
                link: '',
            }
        },
        mounted() {
            // if (window.location.href.includes('ptc-ksa.net')) {
            //     // this.link = '/img/logo.png';
            // } else {
            //     // this.link = '/img/logo-lg.png';
            // }
        },
        methods: {
            //
        }
    }
</script>
