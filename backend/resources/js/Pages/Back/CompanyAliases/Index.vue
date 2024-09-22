<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'companies', link: route('back.companies.index')},
                    {title: 'name-aliases'}
                ]"
            ></breadcrumb-container>
            <div class="flex justify-between">
                <h1 class="mb-8 font-bold text-3xl">{{ company.name_ar }}</h1>
                <div class="mb-6 flex justify-between items-center gap-2">
                    <form class="flex" @submit.prevent="save">
                        <jet-input id="alias"
                                   class="block w-full mx-2"
                                   v-model="alias"
                                   placeholder="اسم الشركة الإضافي ..."
                                   autocomplete="off" />
                        <button class="btn-gray">
                            <span>{{ $t('words.new') }}</span>
                        </button>
                    </form>
                </div>
            </div>
            <div class="bg-white rounded shadow overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <tr class="text-left font-bold">
                        <th class="px-6 pt-6 pb-4">{{ $t('words.name') }}</th>
                        <th class="px-6 pt-6 pb-4"></th>
                    </tr>
                    <tr v-if="!company.aliases.length"
                        class="hover:bg-gray-100 focus-within:bg-gray-100">
                        <td class="border-t" colspan="2">
                            <span class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('back.companies.show', company.id)">
                                {{ $t('words.no-records-have-been-found') }}
                            </span>
                        </td>
                    </tr>

                    <tr v-for="alias in company.aliases"
                        :key="alias.id"
                        class="hover:bg-gray-100 focus-within:bg-gray-100">
                        <td class="border-t">
                            <span class="px-6 py-4 flex items-center focus:text-indigo-500">
                                {{ alias.alias }}
                            </span>
                        </td>
                        <td class="border-t">
                            <div class="flex justify-end">
                                <button class="mt-2 px-2 py-1 bg-white text-black rounded-sm"
                                        @click="deleteAlias(alias.id)">
                                    <ion-icon name="trash-bin-outline" class="block w-6 h-6 fill-red-400 mx-2"></ion-icon>
                                </button>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout'
    import IconNavigate from 'vue-ionicons/dist/ios-arrow-dropright'
    import BreadcrumbContainer from "@/Components/BreadcrumbContainer";
    import JetInput from '@/Jetstream/Input';
    // import IonTrash from 'vue-ionicons/dist/ios-trash-outline'

    export default {
        metaInfo: { title: 'اسماء اضافية' },
        components: {
            // IonTrash,
            BreadcrumbContainer,
            IconNavigate,
            AppLayout,
            JetInput,
        },
        props: ['company'],
        data() {
            return {
                alias: '',
            }
        },
        methods: {
            save()  {
                this.$inertia.post(route('back.companies.aliases.store', this.company.id), {alias: this.alias})
                    .then(() => {
                        this.alias = '';
                    });
            },
            deleteAlias(id) {
                this.$inertia.delete(route('back.companies.aliases.delete', {company_id: this.company.id, id: id}));
            }
        },
    }
</script>
