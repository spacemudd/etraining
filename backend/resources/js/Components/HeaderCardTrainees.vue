<template>
    <inertia-link :href="href" class="flex items-center p-4 bg-white rounded-lg shadow-lg dark:bg-gray-800 hover:shadow-xl">
        <div class="p-3 ltr:mr-4 rtl:ml-4 text-orange-500 bg-orange-100 rounded-full dark:text-orange-100 dark:bg-orange-500">
            <div class="w-5">
                <img v-if="iconPath" :src="iconPath" alt="">
            </div>
        </div>
        <div class="w-full">
            <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">
                {{ titleValue }} <span class="text-xs font-semibold">({{ countValue }})</span>
            </p>
            <div class="text-lg font-semibold text-gray-700 dark:text-gray-200 flex justify-between">
                <div class="ml-3 flex flex-col">
                    <span>{{ candidatesCount }}</span>
                    <span class="text-xs">مرشحين</span>
                </div>
                <div class="ml-3 flex flex-col">
                    <span>{{ approvedCount }}</span>
                    <span class="text-xs">معتمدين</span>
                </div>
                <div class="ml-3 flex flex-col">
                    <span>{{ incompleteCount }}</span>
                    <span class="text-xs">غير مكتمل</span>
                </div>
            </div>
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
            },
            candidatesCount: {
                default: '',
            },
            approvedCount: {
                default: '',
            },
            incompleteCount: {
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
