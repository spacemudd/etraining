<template>
    <ul class="mt-6">
        <sidebar-link link-value="/dashboard" :active="$page.currentRouteName == 'dashboard'">
            <template #icon><DesktopIcon w="20px" h="20px" class="w-5 h-5" /></template>
            <template #title>
                <span class="ltr:ml-4 rtl:mr-4">{{ $t('words.dashboard') }}</span>
            </template>
        </sidebar-link>

        <sidebar-link :link-value="route('teaching.courses.index')" :active="$page.currentRouteName == 'teaching.courses.index'">
            <template #icon>
                <ion-icon name="business-outline" class="w-5 h-5"></ion-icon>
            </template>
            <template #title>
                <span class="ltr:ml-4 rtl:mr-4">{{ $t('words.courses') }}</span>
            </template>
        </sidebar-link>

<!--        <sidebar-link :link-value="route('back.trainees.index')" :active="$page.currentRouteName == 'back.trainees.index'">-->
<!--            <template #icon>-->
<!--                <img src="/img/student.svg" alt="" class="w-5 h-5">-->
<!--            </template>-->
<!--            <template #title>-->
<!--                <span class="ltr:ml-4 rtl:mr-4">{{ $t('words.trainees') }}</span>-->
<!--            </template>-->
<!--        </sidebar-link>-->

        <sidebar-link :link-value="route('teaching.trainee-groups.index')" :active="$page.currentRouteName == 'teaching.trainee-groups.index'">
            <template #icon>
                <img src="/img/student.svg" alt="" class="w-5 h-5">
            </template>
            <template #title>
                <span class="ltr:ml-4 rtl:mr-4">{{ $t('words.trainee-groups') }}</span>
            </template>
        </sidebar-link>

        <li class="relative px-6 py-3">
            <inertia-link
                class="inline-flex items-center w-full text-sm text-gray-800 transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 dark:text-gray-100"
                href="/">
                <ion-icon name="logo-whatsapp" class="w-5 h-5 text-green-600"></ion-icon>
                <span class="ltr:ml-4 rtl:mr-4">{{ $t('words.whatsapp') }}</span>
            </inertia-link>
        </li>

        <li class="pr-8 mt-2 text-xs"><inertia-link class="hover:text-blue-600" :href="route('survey.index')">الاستبيانات</inertia-link>
        <li class="pr-8 mt-2 text-xs"><inertia-link class="hover:text-blue-600" :href="route('training-schedule.index')">الخطة التدريبية والجدول الزمني</inertia-link>
        <li class="pr-8 mt-2 text-xs"><inertia-link class="hover:text-blue-600" :href="route('training-plan.index')">خطة تدريب المدربين</inertia-link>
        <li class="pr-8 mt-2 text-xs"><inertia-link class="hover:text-blue-600" :href="route('user-guides.index')">الادلة الارشادية</inertia-link>
        <li class="pr-8 mt-2 text-xs"><inertia-link class="hover:text-blue-600" :href="route('obligations.index')">الالتزامات</inertia-link>
        <li class="pr-8 mt-2 text-xs"><inertia-link class="hover:text-blue-600" :href="route('management-roles.index')">دليل الكادر الاشرافي</inertia-link>
        <li class="pr-8 mt-2 text-xs"><inertia-link class="hover:text-blue-600" :href="route('company-roles.index')">وثيقة الادوار والمسؤوليات</inertia-link>
        <li class="pr-8 mt-2 text-xs"><inertia-link class="hover:text-blue-600" :href="route('suggestions.index')">للاقتراحات والاستفسارات</inertia-link>
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
