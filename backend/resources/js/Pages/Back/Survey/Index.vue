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
    <trainee-layout>
        <div class="flex justify-center my-20">
            <div class="w-1/2 md:w-1/3 bg-white shadow-lg">
                <!--<div class="w-full"-->
                <!--     style="background-image:url('/img/survey-success.png');background-size:cover;height:310px;background-position: center;">-->
                <!--    -->
                <!--</div>-->
                <img src="/img/survey-success.png"
                     style="image-rendering: optimizequality" alt="">
                <div class="p-10">
                    <p>شاركونا في تعزيز جودة خدماتنا المقدمة</p><br/>
                    <p>من خلال تعاونكم في الاجابة على الاستبيان التالي:</p>
                    <div class="mt-10 text-center">
                        <a :href="url" class="bg-red-600 px-5 py-2 text-white rounded w-full block hover:bg-red-700">
                            شراك رأيك
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </trainee-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import TraineeLayout from '@/Layouts/AppLayoutTrainee'
import IconNavigate from 'vue-ionicons/dist/ios-arrow-dropright'
import BreadcrumbContainer from "@/Components/BreadcrumbContainer";
import JetInput from '@/Jetstream/Input'
import JetButton from '@/Jetstream/Button'

export default {
    components: {
        IconNavigate,
        AppLayout,
        BreadcrumbContainer,
        JetInput,
        JetButton,
        TraineeLayout
    },
    props: [
        'url',
    ],
    metaInfo: { title: 'Thank you - شكراً لك' },
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
