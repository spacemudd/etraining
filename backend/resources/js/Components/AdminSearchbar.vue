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
    <div class="relative w-full max-w-xl ltr:mr-6 rtl:ml-6 focus-within:text-red-500">
        <div class="absolute inset-y-0 flex items-center ltr:pl-2 rtl:pr-2">
            <svg class="w-4 h-4" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
            </svg>
        </div>
        <input
            class="w-full ltr:pl-8 ltr:pr-2 rtl:pr-8 rtl:pl-8 text-sm text-gray-700 placeholder-gray-600 bg-gray-100 border-0 rounded-md dark:placeholder-gray-500 dark:focus:shadow-outline-gray dark:focus:placeholder-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:placeholder-gray-500 focus:bg-white focus:border-red-300 focus:outline-none focus:shadow-outline-red form-input"
            type="text"
            @click="toggleSearchResultsBox"
            :placeholder="$t('words.search')"
            aria-label="Search"
        />

        <!-- View of results -->
        <div class="search-results-box bg-white shadow-lg mt-1 p-3 overflow-x-scroll">
            <p class="text-gray-500">{{ $t('words.results') }}</p>
            <div class="search-results-container mt-2">
                <div class="search-result-row hover:bg-gray-100 p-1 px-5 rounded-lg">
                    <p class="text-black font-bold">شفيق الشعار</p>
                    <p class="text-gray-500">متدرب</p>
                </div>
                <div class="search-result-row hover:bg-gray-100 p-1 px-5 rounded-lg">
                    <p class="text-black font-bold">سارة الحمداني</p>
                    <p class="text-gray-500">مدرب</p>
                </div>
                <div class="search-result-row hover:bg-gray-100 p-1 px-5 rounded-lg">
                    <p class="text-black font-bold">التعليم الإحترافي</p>
                    <p class="text-gray-500">دورة</p>
                </div>
                <div class="search-result-row hover:bg-gray-100 p-1 px-5 rounded-lg">
                    <p class="text-black font-bold">مجدة الشنطة</p>
                    <p class="text-gray-500">Record 2</p>
                </div>
                <div class="search-result-row hover:bg-gray-100 p-1 px-5 rounded-lg">
                    <p class="text-black font-bold">Instructor</p>
                    <p>Record 2</p>
                </div>
                <div class="search-result-row hover:bg-gray-100 p-1 px-5 rounded-lg">
                    <p class="text-black font-bold">Instructor</p>
                    <p>Record 2</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "AdminSearchbar.vue",
    data() {
        return {
            searchBoxVisible: false,
        }
    },
    methods: {
        toggleSearchResultsBox() {
            this.searchBoxVisible = !this.searchBoxVisible;
        }
    },
}
</script>

<style scoped>
    .search-results-box {
        width:150%;
        height:300px;
        position: absolute;
    }

    .search-result-row {
        margin: 1rem 0;
        cursor: pointer;
    }

    @media (min-width: 475px) {
        .search-results-box {
            width: 100%;
        }
    }
</style>
