<template>
    <app-layout>
        <div class="container mx-auto px-4 py-6">
            <div class="bg-white shadow rounded-lg p-6 space-y-6">
                <h1 class="text-2xl font-bold text-gray-800">تقرير تواريخ الحضور المستحقة</h1>

                <!-- اختيار الشركة -->
                <div>
                    <label class="block mb-2 font-medium text-gray-700">اختر الشركة</label>
                    <select v-model="selectedCompany" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200">
                        <option value="" disabled selected>-- اختر شركة --</option>
                        <option v-for="company in companies" :key="company.id" :value="company.id">
                            {{ company.name_ar }}
                        </option>
                    </select>
                </div>

                <!-- اختيار الكورس -->
                <div>
                    <label class="block mb-2 font-medium text-gray-700">اختر الكورس</label>
                    <select v-model="selectedCourse" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200">
                        <option value="" disabled selected>-- اختر كورس --</option>
                        <option v-for="course in coursesNames" :key="course.name_ar" :value="course.name_ar">
                            {{ course.name_ar }}
                        </option>
                    </select>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script setup>
defineProps({
    companies: Array,
    coursesNames: Array
});

import { ref } from 'vue';

const selectedCompany = ref('');
const selectedCourse = ref('');
</script>
