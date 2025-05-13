<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'view-gosi', link: route('back.trainees.gosi')},
                ]"
            ></breadcrumb-container>

            <div class="col-span-6 sm:col-span-2 my-4">
                <h6 class="text-lg font-semibold mb-2">{{ $t('words.current_month_requests') }}</h6>
                <div class="p-4 bg-white rounded shadow">
                    <p :class="['text-gray-700 rtl:text-right', { 'bg-green-100 p-2 rounded': counterUpdated }]" dir="ltr">
                        {{ requestCounter ? requestCounter.count : 0 }} / {{ gosiMonthlyRequests }}
                    </p>
                    <inertia-link
                        :href="route('back.trainees.gosi.log')"
                        class="mt-2 inline-block px-4 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition"
                    >
                        {{ $t('words.log') }}
                    </inertia-link>
                    <div class="mt-4">
                        <label class="text-sm font-medium text-gray-700">حاسبة التكلفة (بناءً على 7.36 ريال لكل طلب)</label>
                        <input
                            type="number"
                            v-model.number="calcRequests"
                            min="0"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50"
                        />
                        <p class="mt-1 text-sm text-gray-800">التكلفة الإجمالية: {{ (calcRequests * 7.36).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }} ريال</p>
                    </div>
                </div>
            </div>

            <div class="col-span-6 sm:col-span-2">
                <div class="flex gap-5">
                    <div>
                        <h6 class="my-2">{{ $t('words.identity_number')}}</h6>
                        <jet-input id="id" v-model="id_number" />
                    </div>

                    <div>
                        <h6 class="my-2">أسباب التحقق</h6>
                        <div class="space-y-2">
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" v-model="reasons.reason_employment_office" class="form-checkbox mx-2">
                                <span>مكتب التوظيف</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" v-model="reasons.reason_collection" class="form-checkbox mx-2">
                                <span>التحصيل</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" v-model="reasons.reason_trainee_affairs" class="form-checkbox mx-2">
                                <span>شؤون المتدربات</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" v-model="reasons.reason_sales" class="form-checkbox mx-2">
                                <span>المبيعات</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" v-model="reasons.reason_other" class="form-checkbox mx-2">
                                <span>أخرى</span>
                            </label>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-span-6 sm:col-span-2 mt-4">

            </div>
            <div class="my-5" v-can="'view-gosi'">
                <div>
                    <p v-if="!isReasonSelected" class="text-red-600 text-sm mb-2">يرجى اختيار سبب واحد على الأقل للتحقق</p>
                    <gosi-container
                        v-if="isReasonSelected"
                        :nin-or-iqama="id_number"
                        :reasons="reasons"
                        @fetch-success="refreshCounter"
                    ></gosi-container>
                </div>
            </div>
        </div>
    </app-layout>
</template>
<script>
import axios from 'axios';
import AppLayout from "../../../Layouts/AppLayout";
import GosiContainer from "../../../Components/GosiContainer";
import Breadcrumb from "@/Components/Breadcrumb";
import BreadcrumbContainer from "@/Components/BreadcrumbContainer";
import JetInput from '@/Jetstream/Input'

export default {
    props: ['gosiMonthlyRequests'],
    components: {
        AppLayout,
        GosiContainer,
        Breadcrumb,
        BreadcrumbContainer,
        JetInput,
    },
    data() {
        return {
            id_number: '',
            requestCounter: null,
            counterUpdated: false,
            calcRequests: 0,
            reasons: {
                reason_employment_office: false,
                reason_collection: false,
                reason_trainee_affairs: false,
                reason_sales: false,
                reason_other: false,
            },
        }
    },
    created() {
        this.requestCounter = this.$page.props.requestCounter ?? null;
    },
    computed: {
        isReasonSelected() {
            return Object.values(this.reasons).some(v => v);
        },
    },
    methods: {
        async refreshCounter() {
            try {
                const response = await axios.get(route('back.gosi.request-counter'));
                this.requestCounter = response.data.requestCounter;
                this.counterUpdated = true;
                setTimeout(() => this.counterUpdated = false, 800);
            } catch (error) {
                console.error('Failed to refresh counter', error);
            }
        },
    },
}
</script>
