<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'messages', link: route('inbox.index')},
                ]"
            ></breadcrumb-container>

            <div class="bg-white rounded shadow overflow-x-auto my-5 p-5" v-for="message in messages">
                <div class="flex justify-between">
                    <div>
                        <div v-if="message.from" class="text-gray-500 text-xs font-bold">{{ $t('words.from') }}:</div>
                        <div v-if="message.from">{{ message.from.name }}</div>
                    </div>
                    <div>
                        <p class="text-xs" dir="ltr">{{ message.created_at | timestampDate }}</p>
                        <p class="text-xs" dir="ltr">{{ message.created_at | timestampHours }}</p>
                        <!-- TODO: Delete inbox message. -->
                        <!--<button class="hover:text-red-500">-->
                        <!--    <ion-icon name="trash-outline" class="block w-6 h-6 fill-gray-400"></ion-icon>-->
                        <!--</button>-->
                    </div>
                </div>
                <div class="my-2 bg-gray-100 p-8 rounded-lg">
                    <p>{{ message.body }}</p>
                </div>
            </div>

        </div>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayoutTrainee'
    import IconNavigate from 'vue-ionicons/dist/ios-arrow-dropright'
    import BreadcrumbContainer from "@/Components/BreadcrumbContainer";

    export default {
        metaInfo: { title: 'Inbox' },
        components: {
            BreadcrumbContainer,
            IconNavigate,
            AppLayout,
        },
        props: ['messages'],
        filters: {
            timestampDate(dateString) {
                if (!dateString) return '';
                return moment(dateString).local().format('YYYY-MM-DD');
            },
            timestampHours(dateString) {
                if (!dateString) return '';
                return moment(dateString).local().format('hh:mm A');
            },
        },
        data() {
            return {
                //
            }
        },
        methods: {
            //
        },
    }
</script>
