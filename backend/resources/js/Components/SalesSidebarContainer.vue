<template>
    <ul class="mt-6">
        <sidebar-link link-value="/dashboard" :active="$page.currentRouteName == 'dashboard'">
            <template #icon><DesktopIcon w="20px" h="20px" class="w-5 h-5" /></template>
            <template #title>
                <span class="ltr:ml-4 rtl:mr-4 hover:text-red-600 hover:font-bold">{{ $t('words.dashboard') }}</span>
            </template>
        </sidebar-link>

        <sidebar-link :link-value="route('sales.companies.index')" :active="$page.currentRouteName == 'back.companies.index'">
            <template #icon>
                <img src="/img/book.svg" alt="" class="w-5 h-5">
            </template>
            <template #title>
                <span class="ltr:ml-4 rtl:mr-4 hover:text-red-600 hover:font-bold">{{ $t('words.companies') }}</span>
            </template>
        </sidebar-link>

        <sidebar-link :link-value="route('back.instructors.index')">
            <template #icon>
                <img src="/img/book.svg" alt="" class="w-5 h-5">
            </template>
            <template #title>
                <span class="ltr:ml-4 rtl:mr-4 hover:text-red-600 hover:font-bold">التواصل</span>
            </template>
        </sidebar-link>

        <sidebar-link :link-value="route('back.trainees.index')" :active="$page.currentRouteName == 'back.trainees.index'">
            <template #icon>
                <img src="/img/book.svg" alt="" class="w-5 h-5">
            </template>
            <template #title>
                <span class="ltr:ml-4 rtl:mr-4 hover:text-red-600 hover:font-bold">الإتفافات</span>
            </template>
        </sidebar-link>


        <sidebar-link :link-value="route('back.candidates.index')" :active="$page.currentRouteName == 'back.candidates.index'">
            <template #icon>
                <img src="/img/book.svg" alt="" class="w-5 h-5">
            </template>
            <template #title>
                <span class="ltr:ml-4 rtl:mr-4 hover:text-red-600 hover:font-bold">عروض الأسعار</span>
            </template>
        </sidebar-link>

        <sidebar-link :link-value="route('back.courses.index')">
            <template #icon>
                <img src="/img/book.svg" alt="" class="w-5 h-5">
            </template>
            <template #title>
                <span class="ltr:ml-4 rtl:mr-4 hover:text-red-600 hover:font-bold">العقود</span>
            </template>
        </sidebar-link>

        <sidebar-link :link-value="route('back.finance')" :active="$page.currentRouteName == 'back.finance'">
            <template #icon>
                <img src="/img/book.svg" alt="" class="w-5 h-5">
            </template>
            <template #title>
                <span class="ltr:ml-4 rtl:mr-4 hover:text-red-600 hover:font-bold">الإستقطاب</span>
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
