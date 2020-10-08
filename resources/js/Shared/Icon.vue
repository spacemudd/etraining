<template>
    <div>
        <div class="flex justify-content-center" v-if="name==='ar'">
            العربية
        </div>

        <div class="flex justify-content-center" v-if="name==='en'">
            English
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            name: {
                type: String,
            }
        },
    }
</script>
