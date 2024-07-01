<template>
  <app-layout>
    <div class="container px-6 mx-auto grid pt-6">
      <breadcrumb-container
        :crumbs="[
          { title: 'dashboard', link: route('dashboard') },
          { title: 'companies', link: route('back.companies.index') },
        ]"
      ></breadcrumb-container>

      <admin-searchbar />
      <div class="bg-white rounded shadow overflow-x-auto">
   <table class="w-full whitespace-no-wrap">
  <thead>
    <tr class="text-left font-bold">
      <th class="px-6 pt-6 pb-4 text-center">{{ $t("words.name") }}</th>
      <th class="px-6 pt-6 pb-4 text-center">{{ $t("words.company-logo") }}</th>
    </tr>
  </thead>
  <tbody>
    <tr
      v-for="company in companies.data"
      :key="company.id"
      class="hover:bg-gray-100 focus-within:bg-gray-100"
    >
      <td class="border-t text-center">
        <inertia-link
          class="px-6 py-4 flex items-center justify-center focus:text-indigo-500"
          :href="route('back.companies.show', company.id)"
        >
          <div>
            {{ company.name_ar }}<br />
            {{ company.name_en }}
          </div>
        </inertia-link>
      </td>
      <td class="border-t text-center">
        <inertia-link
          class="px-6 py-4 flex items-center justify-center focus:text-indigo-500"
          :href="route('back.companies.show', company.id)"
        >
          <img
            v-if="company.logo_files.length"
            :src="`https://prod.ptc-ksa.net/back/media/${company.logo_files[0].id}`"
            :alt="`${company.name} Logo`"
            class="company-logo w-32 h-auto"
          />
        </inertia-link>
      </td>
    </tr>
    <tr v-if="companies.data.length === 0">
      <td class="border-t px-6 py-4 text-center" colspan="4">
        <empty-slate />
      </td>
    </tr>
  </tbody>
</table>

      </div>
      <pagination :links="companies.links" />
    </div>
  </app-layout>
</template>

<script>
// import Icon from '@/Shared/Icon'
// import Layout from '@/Shared/Layout'
import mapValues from "lodash/mapValues";
import Pagination from "@/Shared/Pagination";
import pickBy from "lodash/pickBy";
// import SearchFilter from '@/Shared/SearchFilter'
import throttle from "lodash/throttle";
import AppLayout from "@/Layouts/AppLayout";
import IconNavigate from "vue-ionicons/dist/ios-arrow-dropright";
import BreadcrumbContainer from "@/Components/BreadcrumbContainer";
import EmptySlate from "@/Components/EmptySlate";
import AdminSearchbar from "@/Components/AdminSearchbar";

export default {
  metaInfo: { title: "Companies" },
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
  props: {
    companies: Object,
    filters: Object,
  },
  data() {
    return {
      form: {
        // search: this.filters.search,
        // trashed: this.filters.trashed,
      },
    };
  },
  watch: {
    form: {
      handler: throttle(function () {
        let query = pickBy(this.form);
        this.$inertia.replace(
          this.route(
            "companies",
            Object.keys(query).length ? query : { remember: "forget" }
          )
        );
      }, 150),
      deep: true,
    },
  },
  methods: {
    reset() {
      this.form = mapValues(this.form, () => null);
    },
  },
};
</script>

<style scoped>
.company-logo {
  width: 40%; 
  height: 40%; 
}
</style>

