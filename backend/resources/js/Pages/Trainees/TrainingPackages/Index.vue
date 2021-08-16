<!--
  - Copyright (c) 2020 - Clarastars, LLC  - All Rights Reserved.
  -
  - Unauthorized copying of this file via any medium is strictly prohibited.
  - This file is a proprietary of Clarastars LLC and is confidential / educational purpose only.
  -
  - https://clarastars.com - info@clarastars.com
  - @author Shafiq al-Shaar <shafiqalshaar@gmail.com>
  -->

<template>
    <app-layout-trainee>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'training-packages', link: route('trainees.training-packages.index')},
                ]"
            ></breadcrumb-container>

            <div class="grid md:grid-cols-4 grid-cols-1 gap-6">
                <div v-for="course in courses"
                    class="col-span-1 bg-white shadow rounded p-5 transition-all duration-500 ease-in-out hover:bg-gray-200">
                    <h2>{{ course.name_ar }}</h2>
                    <p class="text-xs h-10">{{ course.description }}</p>
                    <div class="mt-2">
                        <a :href="route('trainees.courses.training-package', {course_id: course.id})"
                           v-show="course.training_package_url"
                           class="block text-sm py-1 rounded bg-blue-500 text-white w-1/2 text-center">
                            {{ $t('words.download') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </app-layout-trainee>
</template>

<script>
import AppLayoutTrainee from '@/Layouts/AppLayoutTrainee'
import BreadcrumbContainer from "@/Components/BreadcrumbContainer";
export default {
    components: {
        AppLayoutTrainee,
        BreadcrumbContainer,
    },
    props: [
        'courses',
    ],
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
