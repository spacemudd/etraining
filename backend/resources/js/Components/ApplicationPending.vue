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
    <div>
        <div>
            <h1 class="text-gray-500 text-2xl text-center my-5 font-bold">{{ $t('words.pending-your-application-verification') }}</h1>
        </div>
        <hr>
        <div class="mx-auto mt-5 w-40">
            <lottie-animation
                path="img/doc-loader.json"
                :loop="true"
                :autoPlay="true"
                :speed="1"/>
        </div>

        <p class="text-center mt-5"><b>{{ instructor_email }}</b></p>
        <p class="text-center mt-3"><b>{{ $t('words.we-will-inform-you-when-we-are-done-reviewing-your-application') }}</b></p>

        <hr class="my-10">

        <p class="text-center text-gray-500">{{ $t('words.you-can-check-our-social-media') }}</p>

        <div class="flex w-100 justify-center mb-5">
            <a target="_blank" href="https://www.instagram.com/ptcksa/" class="mx-3 mt-5 hover:text-red-500">
                <instagram-icon w="46px" h="46px"/>
            </a>
            <a target="_blank" href="https://www.facebook.com/ptcksa" class="mx-3 mt-5 hover:text-red-500">
                <facebook-icon w="46px" h="46px"/>
            </a>
        </div>
    </div>
</template>

<script>
import LottieAnimation from 'lottie-vuejs/src/LottieAnimation.vue';
import FacebookIcon from 'vue-ionicons/dist/logo-facebook';
import InstagramIcon from 'vue-ionicons/dist/logo-instagram';
export default {
    name: "Applicationpending.vue",
    components: {
        LottieAnimation,
        FacebookIcon,
        InstagramIcon,
    },
    props: ['instructor_email'],
    methods: {
        //
    },
}
</script>
