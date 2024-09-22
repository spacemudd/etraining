<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'companies', link: route('back.companies.index')},
                    {title_raw: company.name_ar, link: route('back.companies.show', company.id)},
                    {title: 'open-permissions'},
                ]"
            ></breadcrumb-container>
            <div class="flex justify-between">
                <h1 class="mb-8 font-bold text-3xl">{{ $t('words.companies') }}</h1>
            </div>

            <div class="flex justify-center">
                <div>
                    <form @submit.prevent="addUser">
                        <label for="email">{{ $t('words.email') }}</label>
                        <input type="email" dir="ltr" v-model="form.email" class="input mx-2">
                        <button>{{ $t('words.add') }}</button>
                    </form>
                </div>
            </div>
            <div class="flex justify-center">
                <table class="whitespace-no-wrap bg-white rounded-lg my-5 p-5 shadow text-sm w-1/2">
                <thead>
                    <tr class="text-left font-bold">
                        <th class="p-4 pr-4">{{ $t('words.user') }}</th>
                        <th class="p-4 pr-4"></th>
                    </tr>
                </thead>
                    <tbody>
                            <tr v-for="user in company.allowed_users" class="border-t hover:bg-gray-100 focus-within:bg-gray-100">
                                <td class="text-left px-4 py-4">{{ user.name }}<br/>{{ user.name_ar }}</td>
                                <td class="px-4 py-4 flex justify-end">
                                    <button @click="removeUser(user.id)" class="btn btn-primary">X</button>
                                </td>
                            </tr>
                            <tr class="border-t hover:bg-gray-100 focus-within:bg-gray-100">
                                <td colspan="2" class="text-left px-4 py-4">** {{ $t('words.in-addition-to-users-with-global-permissions') }}</td>
                            </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </app-layout>
</template>

<script>
    import mapValues from 'lodash/mapValues'
    import AppLayout from '@/Layouts/AppLayout'
    import IconNavigate from 'vue-ionicons/dist/ios-arrow-dropright'
    import BreadcrumbContainer from "@/Components/BreadcrumbContainer";
    import EmptySlate from "@/Components/EmptySlate";

    export default {
        metaInfo: { title: 'Companies' },
        // layout: Layout,
        components: {
            EmptySlate,
            BreadcrumbContainer,
            IconNavigate,
            AppLayout,
        },
        props: {
            company: Object,
        },
        data() {
            return {
                form: {
                    // search: this.filters.search,
                    // trashed: this.filters.trashed,
                },
            }
        },
        methods: {
            addUser() {
                this.$inertia.post(route('back.companies.allowed-users.store', this.company.id), this.form);
            },
            removeUser(user_id) {
                this.$inertia.delete(route('back.companies.allowed-users.delete', {company_id: this.company.id, id: user_id}));
            },
            reset() {
                this.form = mapValues(this.form, () => null)
            },
        },
    }
</script>
