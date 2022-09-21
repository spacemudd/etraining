<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                        {title: 'dashboard', link: route('dashboard')},
                        {title: 'complaints', link: route('complaints.index')},
                    ]"
            ></breadcrumb-container>
            <div class="container px-6 mx-auto grid">
                <div class="grid gap-6 mb-8 md:grid-cols-2 xl:grid-cols-4 mt-6">
                    <header-card :href="route('complaints.NewComplaints.Show')" :title-value="$t('words.new_complaints')" :count-value="companies_count" icon-path="http://cdn.onlinewebfonts.com/svg/img_390935.png"></header-card>
                    <header-card :href="route('complaints.InProgress.Show')" :title-value="$t('words.in_progress_complaints')" :count-value="in_progress_companies_count" icon-path="http://cdn.onlinewebfonts.com/svg/img_390935.png"></header-card>
                    <header-card :href="route('complaints.DoneComplaints.Show')" :title-value="$t('words.done_complaints')" :count-value="done_companies_count" icon-path="http://cdn.onlinewebfonts.com/svg/img_390935.png"></header-card>
                </div>
                <div class="grid gap-6 mb-8 md:grid-cols-2 xl:grid-cols-4">
                    <inertia-link :href="route('complaints.create')" class="shadow-lg flex items-center p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800 text-center hover:bg-red-500 hover:text-red-600 hover:font-semibold hover:shadow-xl">
                        {{ $t('words.add-new-complaints') }}
                    </inertia-link>
                </div>
            </div>
        </div>
    </app-layout>
</template>
<script>
import AppLayout from "../../../Layouts/AppLayout";
import HeaderCard from "../../../Components/HeaderCard";
import Breadcrumb from "@/Components/Breadcrumb";
import BreadcrumbContainer from "@/Components/BreadcrumbContainer";

export default {
    props: [
        'companies_count',
        'new_companies_count',
        'in_progress_companies_count',
        'done_companies_count',
        ],
    components: {
        AppLayout,
        HeaderCard,
        Breadcrumb,
        BreadcrumbContainer,
    }
}
</script>
