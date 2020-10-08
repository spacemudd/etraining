<template>
    <div>
        <div class="flex justify-content-center text-black" v-if="name==='ar'">
            العربية
        </div>

        <div class="flex justify-content-center text-black" v-if="name==='en'">
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
