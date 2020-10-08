<template>
    <ul class="mt-6">
        <sidebar-link link-value="/">
            <template #icon><DesktopIcon w="20px" h="20px" class="w-5 h-5" /></template>
            <template #title>
                <span class="ltr:ml-4 rtl:mr-4">{{ $t('words.dashboard') }}</span>
            </template>
        </sidebar-link>
    </ul>
</template>

<script>
    import DesktopIcon from 'vue-ionicons/dist/ios-desktop.vue'
    import SidebarLink from "./SidebarLink";
    export default {
        components: {
            DesktopIcon,
            SidebarLink,
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
