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
    <div class="h-full w-full h-40">
        <div class="flex flex-col py-10 justify-center items-center">
            <ion-icon name="telescope-outline" size="large"></ion-icon>
            <span class="text-sm text-gray-700 mt-4"><slot name="subtitle">{{ $t('words.nothing-is-here') }}</slot></span>
            <span class="mt-4"><slot name="actions"></slot></span>
        </div>
    </div>
</template>

<script>
    export default {
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
