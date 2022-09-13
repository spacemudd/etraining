<template>
    <app-layout>
        <div class="container px-6 mx-auto grid">
            <!--<h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">-->
            <!--    {{ $t('words.dashboard') }}-->
            <!--</h2>-->
            <!-- Cards -->
            <div class="grid gap-6 mb-8 md:grid-cols-2 xl:grid-cols-4 mt-6">
                <!-- Card -->
                <header-card :href="route('back.companies.index')" :title-value="$t('words.companies')" :count-value="companies_count" icon-path="/img/building.svg"></header-card>
                <header-card :href="route('back.instructors.index')" :title-value="$t('words.instructors')" :count-value="instructors_count" icon-path="/img/teacher.svg"></header-card>
                <header-card-trainees :href="route('back.trainees.index')" :title-value="$t('words.trainees')"
                                      :count-value="trainees_count"
                                      :candidates-count="trainees_candidates_count"
                                      :approved-count="trainees_approved_count"
                                      :incomplete-count="trainees_incomplete_count"
                                      icon-path="/img/student.svg"></header-card-trainees>
                <header-card :href="route('back.courses.index')" :title-value="$t('words.courses')" :count-value="courses_count" icon-path="/img/book.svg"></header-card>
            </div>

            <!-- Quick actions actions -->
            <h2 class="my-6 font-semibold text-gray-700 dark:text-gray-200 border-b pb-1">
                {{ $t('words.quick-actions') }}
            </h2>

            <div class="grid gap-6 mb-8 md:grid-cols-2 xl:grid-cols-4">
                <inertia-link :href="route('back.companies.create')" class="shadow-lg flex items-center p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800 text-center hover:bg-red-500 hover:text-red-600 hover:font-semibold hover:shadow-xl">
                    {{ $t('words.add-new-company') }}
                </inertia-link>
                <inertia-link :href="route('back.instructors.create')" class="shadow-lg flex items-center p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800 text-center hover:bg-red-500 hover:text-red-600 hover:font-semibold hover:shadow-xl">
                    {{ $t('words.add-new-instructor') }}
                </inertia-link>
                <inertia-link :href="route('back.trainees.create')" class="shadow-lg flex items-center p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800 text-center hover:bg-red-500 hover:text-red-600 hover:font-semibold hover:shadow-xl">
                    {{ $t('words.add-new-trainee') }}
                </inertia-link>
                <inertia-link :href="route('back.courses.create')" class="shadow-lg flex items-center p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800 text-center hover:bg-red-500 hover:text-red-600 hover:font-semibold hover:shadow-xl">
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
    import HeaderCardTrainees from "../Components/HeaderCardTrainees";

    export default {
        props: [
            'companies_count',
            'instructors_count',
            'trainees_count',
            'courses_count',
            'trainees_candidates_count',
            'trainees_approved_count',
            'trainees_incomplete_count',
        ],
        components: {
            AppLayout,
            Welcome,
            LanguageSelector,
            HeaderCard,
            HeaderCardTrainees,
        },
    }
</script>
