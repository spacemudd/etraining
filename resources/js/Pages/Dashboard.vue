<template>
    <app-layout>
        <div class="container px-6 mx-auto grid">
            <!--<h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">-->
            <!--    {{ $t('words.dashboard') }}-->
            <!--</h2>-->
            <!-- Cards -->
            <div class="grid gap-6 mb-8 md:grid-cols-2 xl:grid-cols-4 mt-6">
                <!-- Card -->
                <header-card :title-value="$t('words.companies')" :count-value="companies_count" icon-path="/img/building.svg"></header-card>
                <header-card :title-value="$t('words.trainers')" :count-value="trainers_count" icon-path="/img/teacher.svg"></header-card>
                <header-card :title-value="$t('words.trainees')" :count-value="trainees_count" icon-path="/img/student.svg"></header-card>
                <header-card :title-value="$t('words.courses')" :count-value="courses_count" icon-path="/img/book.svg"></header-card>
            </div>

            <!-- Quick actions actions -->
            <h2 class="my-6 font-semibold text-gray-700 dark:text-gray-200 border-b pb-1">
                {{ $t('words.quick-actions') }}
            </h2>

            <div class="grid gap-6 mb-8 md:grid-cols-2 xl:grid-cols-4">
                <inertia-link href="/back/companies/create" class="flex items-center p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800 text-center hover:bg-red-500 hover:text-white hover:font-semibold">
                    {{ $t('words.add-new-company') }}
                </inertia-link>
                <inertia-link href="/" class="flex items-center p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800 text-center hover:bg-red-500 hover:text-white hover:font-semibold">
                    {{ $t('words.add-new-trainer') }}
                </inertia-link>
                <inertia-link :href="route('back.trainees.create')" class="flex items-center p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800 text-center hover:bg-red-500 hover:text-white hover:font-semibold">
                    {{ $t('words.add-new-trainee') }}
                </inertia-link>
                <inertia-link href="/" class="flex items-center p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800 text-center hover:bg-red-500 hover:text-white hover:font-semibold">
                    {{ $t('words.add-new-course') }}
                </inertia-link>
            </div>
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from './../Layouts/AppLayout'
    import Welcome from './../Jetstream/Welcome'
    import LanguageSelector from "../Shared/LanguageSelector";
    import HeaderCard from "../Components/HeaderCard";

    export default {
        props: ['companies_count', 'trainers_count', 'trainees_count', 'courses_count'],
        components: {
            AppLayout,
            Welcome,
            LanguageSelector,
            HeaderCard
        },
    }
</script>
