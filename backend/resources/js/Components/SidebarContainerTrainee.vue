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

        <sidebar-link :link-value="route('trainees.attendance-sheet.index')"
                      :active="$page.currentRouteName == 'trainees.attendance-sheet.index'">
            <template #icon>
                <ion-icon name="calendar-outline" class="w-5 h-5"></ion-icon>
            </template>
            <template #title>
                <span class="ltr:ml-4 rtl:mr-4">{{ $t('words.attendance') }}</span>
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

        <li class="relative px-6 py-3">
            <a target="_blank"
                class="inline-flex items-center w-full text-sm text-gray-800 transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 dark:text-gray-100"
                href="https://api.whatsapp.com/send?phone=+966555094487">
                <ion-icon name="logo-whatsapp" class="w-5 h-5 text-green-600"></ion-icon>
                <span class="ltr:ml-4 rtl:mr-4">{{ $t('words.whatsapp') }}</span>
            </a>
        </li>

        <li class="pr-8 mt-2 text-xs"><a class="hover:text-blue-600" href="/terms#attendance-policy">{{ $t('words.attendance-policy') }}</a></li>
        <li class="pr-8 text-xs"><a class="hover:text-blue-600" href="/terms#privacy-policy">{{ $t('words.privacy-policy') }}</a></li>
        <li class="pr-8 text-xs"><a class="hover:text-blue-600" href="/terms">{{ $t('words.support-policy') }}</a></li>
        <li class="pr-8 text-xs"><a class="hover:text-blue-600" href="/terms#academic-integrity-policy">{{ $t('words.academic-integrity-policy') }}</a></li>
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

        },
        methods: {

        }
    }
</script>

<style lang="sass">
    //
</style>
