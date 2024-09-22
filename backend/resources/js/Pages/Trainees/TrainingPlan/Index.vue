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
                    {title: 'training-plan', link: route('trainees.training-plan.index')},
                ]"
            ></breadcrumb-container>

            <div class="col text-center h-20">
                <a target="_blank"
                   rel="noopener noreferrer"
                   href="/trainees-schedule-updated.pdf"
                   class="bg-red-500 px-2 py-1 rounded mx-auto text-white hover:bg-red-700">
                    الخطة التدريبية
                </a>
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
