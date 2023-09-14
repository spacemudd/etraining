<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'companies', link: route('back.companies.index')},
                    {title: 'manage-chasers'},
                ]"
            ></breadcrumb-container>
            <div class="flex justify-between">
                <h1 class="mb-8 font-bold text-3xl">{{ $t('words.manage-chasers') }}</h1>
            </div>
        </div>

        <!-- TODO: Continue -->
        <div class="container flex grid-cols-12 gap-6">
            <div>
                <table class="table-fixed w-full text-xs text-right">
                <thead>
                    <tr>
                        <th class="border border-red-500">{{ $t('words.name') }}</th>
                        <th class="border border-red-500">{{ $t('words.companies') }}</th>
                    </tr>
                </thead>
                	<tbody>
                			<tr>
                				<td></td>
                			</tr>
                	</tbody>
                </table>
            </div>
        </div>
    </app-layout>
</template>

<script>
// import Icon from '@/Shared/Icon'
// import Layout from '@/Shared/Layout'
import mapValues from 'lodash/mapValues'
import Pagination from '@/Shared/Pagination'
import pickBy from 'lodash/pickBy'
// import SearchFilter from '@/Shared/SearchFilter'
import throttle from 'lodash/throttle'
import AppLayout from '@/Layouts/AppLayout'
import IconNavigate from 'vue-ionicons/dist/ios-arrow-dropright'
import BreadcrumbContainer from "@/Components/BreadcrumbContainer";
import EmptySlate from "@/Components/EmptySlate";
import AdminSearchbar from '@/Components/AdminSearchbar';

export default {
    metaInfo: { title: 'Companies' },
    // layout: Layout,
    components: {
        AdminSearchbar,
        EmptySlate,
        BreadcrumbContainer,
        IconNavigate,
        AppLayout,
        // Icon,
        Pagination,
        // SearchFilter,
    },
    props: [
        'chasers',
    ],
    data() {
        return {
            form: {
                // search: this.filters.search,
                // trashed: this.filters.trashed,
            },
        }
    },
    watch: {
        form: {
            handler: throttle(function() {
                let query = pickBy(this.form)
                this.$inertia.replace(this.route('companies', Object.keys(query).length ? query : { remember: 'forget' }))
            }, 150),
            deep: true,
        },
    },
    methods: {
        reset() {
            this.form = mapValues(this.form, () => null)
        },
    },
}
</script>
