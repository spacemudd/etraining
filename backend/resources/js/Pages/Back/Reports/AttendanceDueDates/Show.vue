<template>
    <app-layout>
        <div class="container mx-auto px-4 py-6">
            <div class="bg-white shadow rounded-lg p-6 space-y-6">
                <h1 class="text-2xl font-bold text-gray-800">تقرير تواريخ الحضور المستحقة</h1>

                <!-- اختيار الشركة -->
                <div>
                    <label class="block mb-2 font-medium text-gray-700">اختر الشركة</label>
                    <select v-model="selectedCompany" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200">
                        <option value="" disabled>-- اختر شركة --</option>
                        <option v-for="company in companies" :key="company.id" :value="company.id">
                            {{ company.name_ar }}
                        </option>
                    </select>
                </div>

                <!-- اختيار الكورس -->
                <div>
                    <label class="block mb-2 font-medium text-gray-700">اختر الكورس</label>
                    <select v-model="selectedCourse" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200">
                        <option value="" disabled>-- اختر كورس --</option>
                        <option v-for="course in coursesNames" :key="course.name_ar" :value="course.name_ar">
                            {{ course.name_ar }}
                        </option>
                    </select>
                </div>

                <!-- تاريخ البداية -->
                <div>
                    <label class="block mb-2 font-medium text-gray-700">تاريخ البداية</label>
                    <input type="date" v-model="startDate" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200">
                </div>

                <!-- تاريخ النهاية -->
                <div>
                    <label class="block mb-2 font-medium text-gray-700">تاريخ النهاية</label>
                    <input type="date" v-model="endDate" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200">
                </div>

                <!-- زر الإرسال -->
                <div class="pt-4">
                    <button
                        @click="submitForm"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        عرض التقرير
                    </button>


                        <p v-if="report && report.status === 'generating'" class="text-gray-600 mt-2">
                            جاري تجهيز التقرير، يرجى الانتظار...
                        </p>


                      <a
                            v-if="report && report.status === 'ready' && report.filename"
                            :href="`/reports/attendance/download/${report.filename}`"
                            class="text-blue-600 underline mt-4 block"
                        >
                            تحميل التقرير
                        </a>



                </div>
            </div>
        </div>
    </app-layout>
</template>


<script>
import { Inertia } from '@inertiajs/inertia'
import AppLayout from '@/Layouts/AppLayout.vue'
import axios from 'axios'

export default {
    components: {
        AppLayout,
    },
    props: {
        companies: Array,
        coursesNames: Array,
        latestReport: Object,
    },
    data() {
        return {
            selectedCompany: '',
            selectedCourse: '',
            startDate: '',
            endDate: '',
            report: this.latestReport,
            pollingInterval: null,
        }
    },
    methods: {
       submitForm() {
            this.report = { status: 'generating' }; // نحدثها قبل الإرسال
            Inertia.post('/attendance-due-dates-report', {
                company_id: this.selectedCompany,
                course_name: this.selectedCourse,
                start_date: this.startDate,
                end_date: this.endDate
            });
        },
        fetchLatestReport() {
            axios.get('/reports/attendance-due-dates/latest')
                .then(response => {
                    this.report = response.data;
                });
        }
    },
    mounted() {
        this.pollingInterval = setInterval(() => {
            this.fetchLatestReport();
        }, 10000); // كل 10 ثواني
    },
    beforeDestroy() {
        clearInterval(this.pollingInterval);
    }
}
</script>

