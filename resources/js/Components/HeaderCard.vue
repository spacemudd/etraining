<template>
    <div class="flex items-center p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800">
        <div class="p-3 ltr:mr-4 rtl:ml-4 text-orange-500 bg-orange-100 rounded-full dark:text-orange-100 dark:bg-orange-500">
            <div class="w-5 h5">
                <img v-if="iconPath" :src="iconPath" alt="Buildings">
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
    </div>
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
