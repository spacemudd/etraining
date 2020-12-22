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
    <app-layout>
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
            <div>
                <a href="/">
                    <img src="/img/logo-lg.png" alt="Logo" class="w-56">
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">

            </div>

        </div>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/ZoomLayout'
    export default {
        components: {
            AppLayout,
        },
        data() {
            return {
            }
        },
        props: [
            'instructor_email'
        ],
    }
</script>
