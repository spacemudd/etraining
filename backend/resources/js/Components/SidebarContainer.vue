<template>
    <ul class="mt-6">
        <sidebar-link link-value="/dashboard" :active="$page.currentRouteName == 'dashboard'">
            <template #icon><DesktopIcon w="20px" h="20px" class="w-5 h-5" /></template>
            <template #title>
                <span class="ltr:ml-4 rtl:mr-4">{{ $t('words.dashboard') }}</span>
            </template>
        </sidebar-link>

        <sidebar-link :link-value="route('back.companies.index')" :active="$page.currentRouteName == 'back.companies.index'">
            <template #icon>
                <ion-icon name="business-outline" class="w-5 h-5"></ion-icon>
            </template>
            <template #title>
                <span class="ltr:ml-4 rtl:mr-4">{{ $t('words.companies') }}</span>
            </template>
        </sidebar-link>

        <sidebar-link :link-value="route('back.instructors.index')">
            <template #icon>
                <img src="/img/teacher.svg" alt="" class="w-5 h-5">
            </template>
            <template #title>
                <span class="ltr:ml-4 rtl:mr-4">{{ $t('words.instructors') }}</span>
            </template>
        </sidebar-link>

        <sidebar-link :link-value="route('back.trainees.index')" :active="$page.currentRouteName == 'back.trainees.index'">
            <template #icon>
                <img src="/img/student.svg" alt="" class="w-5 h-5">
            </template>
            <template #title>
                <span class="ltr:ml-4 rtl:mr-4">{{ $t('words.trainees') }}</span>
            </template>
        </sidebar-link>

        <sidebar-link :link-value="route('back.courses.index')">
            <template #icon>
                <img src="/img/book.svg" alt="" class="w-5 h-5">
            </template>
            <template #title>
                <span class="ltr:ml-4 rtl:mr-4">{{ $t('words.courses') }}</span>
            </template>
        </sidebar-link>

        <sidebar-link :link-value="route('back.finance')" :active="$page.currentRouteName == 'back.finance'">
            <template #icon>
                <img src="/img/bank.svg" alt="" class="w-5 h-5">
            </template>
            <template #title>
                <span class="ltr:ml-4 rtl:mr-4">{{ $t('words.finance') }}</span>
            </template>
        </sidebar-link>

        <sidebar-link :link-value="route('back.settings')">
            <template #icon>
                <ion-icon name="settings-outline" class="w-5 h-5"></ion-icon>
            </template>
            <template #title>
                <span class="ltr:ml-4 rtl:mr-4">{{ $t('words.settings') }}</span>
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
