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
    <div class="flex justify-center mt-20">
        <div class="w-1/2 md:w-1/3 bg-white shadow-lg">
            <inertia-link href="/">
                <img src="/img/survey-success.png"
                     style="image-rendering: optimizequality" alt="">
            </inertia-link>
            <div class="p-10">
                <p>شاركونا في تعزيز جودة خدماتنا المقدمة</p><br/>
                <p>من خلال تعاونكم في الاجابة على الاستبيان التالي:</p>
                <div class="mt-10 text-center">
                    <a :href="url" class="bg-red-600 px-5 py-2 text-white rounded w-full block hover:bg-red-700">
                        شارك رأيك
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import IconNavigate from 'vue-ionicons/dist/ios-arrow-dropright'
import BreadcrumbContainer from "@/Components/BreadcrumbContainer";
import JetInput from '@/Jetstream/Input'
import JetButton from '@/Jetstream/Button'

export default {
    components: {
        IconNavigate,
        BreadcrumbContainer,
        JetInput,
        JetButton,
    },
    props: [
        'type',
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
