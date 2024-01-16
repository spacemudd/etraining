<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
            :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                ]"
        ></breadcrumb-container>


            <div class="col-span-6 sm:col-span-2">
                <h6 class="my-2">{{ $t('words.identity_number')}}</h6>
                <jet-input id="id" v-model="id_number" />
            </div>
            <div class="my-5">
                <gosi-container :nin-or-iqama="id_number"></gosi-container>
            </div>
        </div>

    </app-layout>
</template>
<script>
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
        }
    }
}
</script>
