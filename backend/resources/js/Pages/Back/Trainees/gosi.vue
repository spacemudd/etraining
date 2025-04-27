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
                        {{ requestCounter ? requestCounter.count : 0 }} / 600
                    </p>
                </div>
            </div>

            <div class="col-span-6 sm:col-span-2">
                <h6 class="my-2">{{ $t('words.identity_number')}}</h6>
                <jet-input id="id" v-model="id_number" />
            </div>
            <div class="my-5" v-can="'view-gosi'">
                <gosi-container :nin-or-iqama="id_number" @fetch-success="refreshCounter"></gosi-container>
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
        }
    },
    created() {
        this.requestCounter = this.$page.props.requestCounter ?? null;
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
