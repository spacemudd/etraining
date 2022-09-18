<template>
    <ul class="mt-6">
        <sidebar-link link-value="/dashboard" :active="$page.currentRouteName == 'dashboard'">
            <template #icon><DesktopIcon w="20px" h="20px" class="w-5 h-5" /></template>
            <template #title>
                <span class="ltr:ml-4 rtl:mr-4 hover:text-red-600 hover:font-bold">{{ $t('words.dashboard') }}</span>
            </template>
        </sidebar-link>

        <sidebar-link :link-value="route('back.companies.index')" :active="$page.currentRouteName == 'back.companies.index'">
            <template #icon>
                <ion-icon name="business-outline" class="w-5 h-5"></ion-icon>
            </template>
            <template #title>
                <span class="ltr:ml-4 rtl:mr-4 hover:text-red-600 hover:font-bold">{{ $t('words.trainee-affairs') }}</span>
            </template>
        </sidebar-link>

        <li class="pr-8 mt-2 text-xs"><a class="hover:text-blue-600" href="/terms#attendance-policy">السياسة الاكاديمية والتدريبية</a>
        <li class="pr-8 text-xs"><a class="hover:text-blue-600" href="/terms">{{ $t('words.support-policy') }}</a></li>
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
