<template>
    <ul class="mt-6">
        <sidebar-link link-value="/dashboard" :active="$page.currentRouteName == 'dashboard'">
            <template #icon><DesktopIcon w="20px" h="20px" class="w-5 h-5" /></template>
            <template #title>
                <span class="ltr:ml-4 rtl:mr-4">{{ $t('words.dashboard') }}</span>
            </template>
        </sidebar-link>

        <sidebar-link :link-value="route('trainees.courses.index')" :active="$page.currentRouteName == 'trainees.courses.index'">
            <template #icon>
                <ion-icon name="business-outline" class="w-5 h-5"></ion-icon>
            </template>
            <template #title>
                <span class="ltr:ml-4 rtl:mr-4">{{ $t('words.courses') }}</span>
            </template>
        </sidebar-link>

        <sidebar-link :link-value="route('inbox.index')" :active="$page.currentRouteName == 'inbox.index'">
            <template #icon>
                <ion-icon name="mail-outline" class="w-5 h-5"></ion-icon>
            </template>
            <template #title>
                <span class="ltr:ml-4 rtl:mr-4">{{ $t('words.messages') }} <span v-if="$page.props.user.inbox_messages_count" class="bg-red-500 rounded p-1 text-white">{{ $page.props.user.inbox_messages_count }}</span></span>
            </template>
        </sidebar-link>
    </ul>
</template>

<script>
    import DesktopIcon from 'vue-ionicons/dist/ios-desktop.vue'
    import BusinessIcon from 'vue-ionicons/dist/ios-business';
    import SidebarLink from "./SidebarLink";

    export default {
        components: {
            DesktopIcon,
            SidebarLink,
            BusinessIcon,
        },
        data() {
            return {
                meetingConfig: [],
                createdMeetingNumber: null,
            }
        },
        mounted() {
            //
        },
        methods: {

        }
    }
</script>

<style lang="sass">
    //
</style>
