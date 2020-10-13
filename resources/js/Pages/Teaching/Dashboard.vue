<template>
    <app-layout>
        <div class="container px-6 mx-auto grid">
            <!--<h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">-->
            <!--    {{ $t('words.dashboard') }}-->
            <!--</h2>-->
            <!-- Cards -->

            <!-- Quick actions actions -->
            <h2 class="my-6 font-semibold text-gray-700 dark:text-gray-200 border-b pb-1">
                {{ $t('words.quick-actions') }}
            </h2>
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayoutInstructor'
    import Welcome from '@/Jetstream/Welcome'
    import LanguageSelector from "@/Shared/LanguageSelector";
    import HeaderCard from "@/Components/HeaderCard";

    export default {
        props: ['companies_count', 'instructors_count', 'trainees_count', 'courses_count'],
        components: {
            AppLayout,
            Welcome,
            LanguageSelector,
            HeaderCard
        },
    }
</script>
