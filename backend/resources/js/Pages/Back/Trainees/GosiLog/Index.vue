<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    { title: 'masdr', link: route('back.trainees.gosi') },
                    { title: 'logs', link: route('back.trainees.gosi.log') }
                ]"
            ></breadcrumb-container>

            <div class="bg-white rounded shadow p-4 mt-4">
                <h2 class="text-xl font-semibold mb-4">Request Log</h2>

                <div class="mb-6">
                    <h3 class="text-lg font-medium mb-2">إحصائيات الأسباب لهذا الأسبوع</h3>
                    <canvas id="weekly-reasons-chart" height="100"></canvas>
                </div>

                <table class="min-w-full bg-white">
                    <thead>
                        <tr class="text-left border-b">
                            <th class="py-2 px-4">NIN / Iqama</th>
                            <th class="py-2 px-4">Updated At</th>
                            <th class="py-2 px-4">Reason(s)</th>
                            <th class="py-2 px-4 rtl:text-align">Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="log in logs.data" :key="log.id" class="bo    rder-b hover:bg-gray-50">
                            <td class="py-2 px-4">{{ log.nin_or_iqama }}</td>
                            <td class="py-2 px-4 text-right" dir="ltr">{{ new Date(log.updated_at).toLocaleString('en-US', { hour12: true }) }}</td>
                            <td class="py-2 px-4 text-xs text-gray-700">
                                <ul class="list-disc list-inside space-y-1">
                                    <li v-if="log.reason_employment_office">مكتب التوظيف</li>
                                    <li v-if="log.reason_collection">التحصيل</li>
                                    <li v-if="log.reason_trainee_affairs">شؤون المتدربات</li>
                                    <li v-if="log.reason_sales">المبيعات</li>
                                    <li v-if="log.reason_other">أخرى</li>
                                </ul>
                            </td>
                            <td class="py-2 px-4 text-xs max-w-sm overflow-auto">
                                <pre style="white-space: pre-wrap; word-break: break-word; direction: ltr;">{{ formatJson(log.data) }}</pre>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <pagination class="mt-4" :links="logs.links" />
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue'
import BreadcrumbContainer from '@/Components/BreadcrumbContainer.vue'
import Pagination from '@/Shared/Pagination'
import Chart from 'chart.js/auto';


export default {
    props: {
        logs: Object,
        weekly_reason_stats: Object,
    },
    components: {
        AppLayout,
        BreadcrumbContainer,
        Pagination,
    },
    methods: {
        formatJson(data) {
            let parsed = typeof data === 'string' ? JSON.parse(data) : data;
            return JSON.stringify(parsed, null, 2);
        }
    },
    mounted() {
        const ctx = document.getElementById('weekly-reasons-chart').getContext('2d');
        const stats = this.weekly_reason_stats;

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: Object.keys(stats),
                datasets: [{
                    label: 'عدد الطلبات',
                    data: Object.values(stats),
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: context => `عدد: ${context.raw}`
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
}
</script>
