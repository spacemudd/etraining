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
                <span class="ltr:ml-4 rtl:mr-4 hover:text-red-600 hover:font-bold">{{ $t('words.companies') }}</span>
            </template>
        </sidebar-link>

        <sidebar-link :link-value="route('back.instructors.index')">
            <template #icon>
                <img src="/img/teacher.svg" alt="" class="w-5 h-5">
            </template>
            <template #title>
                <span class="ltr:ml-4 rtl:mr-4 hover:text-red-600 hover:font-bold">{{ $t('words.instructors') }}</span>
            </template>
        </sidebar-link>

        <sidebar-link :link-value="route('back.trainees.index')" :active="$page.currentRouteName == 'back.trainees.index'">
            <template #icon>
                <img src="/img/student.svg" alt="" class="w-5 h-5">
            </template>
            <template #title>
                <span class="ltr:ml-4 rtl:mr-4 hover:text-red-600 hover:font-bold">{{ $t('words.trainees') }}</span>
            </template>
        </sidebar-link>


        <sidebar-link :link-value="route('back.candidates.index')" :active="$page.currentRouteName == 'back.candidates.index'">
            <template #icon>
                <img src="/img/student.svg" alt="" class="w-5 h-5">
            </template>
            <template #title>
                <span class="ltr:ml-4 rtl:mr-4 hover:text-red-600 hover:font-bold">{{ $t('words.candidates') }}</span>
            </template>
        </sidebar-link>

        <sidebar-link :link-value="route('back.courses.index')">
            <template #icon>
                <img src="/img/book.svg" alt="" class="w-5 h-5">
            </template>
            <template #title>
                <span class="ltr:ml-4 rtl:mr-4 hover:text-red-600 hover:font-bold">{{ $t('words.courses') }}</span>
            </template>
        </sidebar-link>

        <sidebar-link :link-value="route('back.finance')" :active="$page.currentRouteName == 'back.finance'">
            <template #icon>
                <img src="/img/bank.svg" alt="" class="w-5 h-5">
            </template>
            <template #title>
                <span class="ltr:ml-4 rtl:mr-4 hover:text-red-600 hover:font-bold">{{ $t('words.finance') }}</span>
            </template>
        </sidebar-link>

        <sidebar-link v-can="'view-backoffice-reports'" :link-value="route('back.reports.index')" :active="$page.currentRouteName == 'back.reports.index'">
            <template #icon>
                <img src="/img/reports.svg" alt="" class="w-5 h-5">
            </template>
            <template #title>
                <span class="ltr:ml-4 rtl:mr-4 hover:text-red-600 hover:font-bold">{{ $t('words.reports') }}</span>
            </template>
        </sidebar-link>

        <sidebar-link :link-value="route('complaints.index')" :active="$page.currentRouteName == 'complaints.index'">
            <template #icon>
                <img src="/img/reports.svg" alt="" class="w-5 h-5">
            </template>
            <template #title>
                <span class="ltr:ml-4 rtl:mr-4 hover:text-red-600 hover:font-bold">{{ $t('words.complaints') }}</span>
            </template>
        </sidebar-link>

        <sidebar-link :link-value="route('back.settings')">
            <template #icon>
                <ion-icon name="settings-outline" class="w-5 h-5"></ion-icon>
            </template>
            <template #title>
                <span class="ltr:ml-4 rtl:mr-4 hover:text-red-600 hover:font-bold">{{ $t('words.settings') }}</span>
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
