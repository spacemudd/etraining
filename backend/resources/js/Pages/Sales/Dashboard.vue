<template>
    <app-sales-layout>
        <div class="container px-6 mx-auto grid">
            <!-- Cards -->
            <!--
            <div class="grid gap-6 mb-8 md:grid-cols-3 xl:grid-cols-3 mt-6">
                <header-card :href="route('back.companies.index')"
                             title-value="متوسط الإتصالات الأسبوعية"
                             :count-value="281"
                             icon-path="/img/building.svg"></header-card>
                <header-card :href="route('back.companies.index')"
                             title-value="نسبة تحويل من زيارة الى عرض سعر"
                             count-value="38%"
                             icon-path="/img/building.svg"></header-card>
                <header-card :href="route('back.companies.index')"
                             title-value="نسبة تحويل عرض السعر الى عقد"
                             count-value="68%"
                             icon-path="/img/building.svg"></header-card>
            </div>
            -->

            <!-- Quick actions actions -->
            <h2 class="my-6 font-semibold text-gray-700 dark:text-gray-200 border-b pb-1">
                {{ $t('words.quick-actions') }}
            </h2>

            <div class="grid gap-6 mb-8 md:grid-cols-2 xl:grid-cols-4">
                <inertia-link :href="route('back.companies.create')" class="shadow-lg flex items-center p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800 text-center hover:bg-red-500 hover:text-red-600 hover:font-semibold hover:shadow-xl">
                        زيارة جديدة
                </inertia-link>
                <inertia-link :href="route('back.instructors.create')" class="shadow-lg flex items-center p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800 text-center hover:bg-red-500 hover:text-red-600 hover:font-semibold hover:shadow-xl">
                    اتفاق جديد
                </inertia-link>
                <inertia-link :href="route('back.trainees.create')" class="shadow-lg flex items-center p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800 text-center hover:bg-red-500 hover:text-red-600 hover:font-semibold hover:shadow-xl">
                    عرض سعر جديد
                </inertia-link>
                <inertia-link :href="route('back.courses.create')" class="shadow-lg flex items-center p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800 text-center hover:bg-red-500 hover:text-red-600 hover:font-semibold hover:shadow-xl">
                    عقد جديد
                </inertia-link>
            </div>
        </div>
    </app-sales-layout>
</template>

<script>
    import AppSalesLayout from './../../Layouts/AppSalesLayout'
    import Welcome from './../../Jetstream/Welcome'
    import LanguageSelector from "./../../Shared/LanguageSelector";
    import HeaderCard from "./../../Components/HeaderCard";
    import HeaderCardTrainees from "./../../Components/HeaderCardTrainees";

    export default {
        props: [

        ],
        components: {
            AppSalesLayout,
            Welcome,
            LanguageSelector,
            HeaderCard,
            HeaderCardTrainees,
        },
    }
</script>
