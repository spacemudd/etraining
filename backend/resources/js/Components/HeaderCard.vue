<template>
    <inertia-link :href="href" class="flex items-center p-4 bg-white rounded-lg shadow-lg dark:bg-gray-800 hover:shadow-xl">
        <div class="p-3 ltr:mr-4 rtl:ml-4 text-orange-500 bg-orange-100 rounded-full dark:text-orange-100 dark:bg-orange-500">
            <div class="w-5">
                <img v-if="iconPath" :src="iconPath" alt="">
            </div>
        </div>
        <div>
            <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">
                {{ titleValue }}
            </p>
            <p class="text-lg font-semibold text-gray-700 dark:text-gray-200">
                {{ countValue }}
            </p>
        </div>
    </inertia-link>
</template>

<script>
    export default {
        props: {
            countValue: {
                default: '',
            },
            titleValue: {
                default: '',
            },
            iconPath: {
                default: '',
            },
            href: {
                default: '',
            }
        },
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
        }
    }
</script>

<style lang="sass">
    //
</style>
