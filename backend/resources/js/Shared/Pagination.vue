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
    <div class="mt-6 -mb-1 flex flex-wrap">
        <template v-for="(link, key) in links">
            <div v-if="link.url === null" :key="key" class="mr-1 mb-1 px-4 py-3 text-sm border rounded text-gray-400"
                 :class="{ 'ml-auto': link.label === 'Next' }">
                <span v-html="link.label"></span>
            </div>
            <inertia-link v-else :key="key"
                          class="mr-1 mb-1 px-4 py-3 text-sm border rounded hover:bg-white focus:border-indigo-500 focus:text-indigo-500"
                          :class="{ 'border-red-500': link.active, 'ml-auto': link.label === 'Next', }"
                          :href="link.url">
                <span v-html="link.label"></span>
            </inertia-link>
        </template>
    </div>
</template>

<script>
    export default {
        props: {
            links: Array,
        },
    }
</script>
