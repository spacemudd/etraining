<template>
  <app-layout>
    <div class="container px-6 mx-auto grid pt-6">
      <breadcrumb-container
        :crumbs="[
          { title: 'dashboard', link: route('dashboard') },
          { title: 'settings', link: route('back.settings') },
          { title: 'recruitment-companies' },
        ]"
      ></breadcrumb-container>
      <div class="flex justify-between">
        <h1 class="mb-8 font-bold text-3xl">{{ $t("words.recruitment-companies") }}</h1>
        <div class="mb-6 flex justify-between items-center gap-2">
          <inertia-link
            class="btn-gray"
            :href="route('back.settings.recruitment-companies.create')"
          >
            <span>{{ $t("words.new") }}</span>
          </inertia-link>
        </div>
      </div>
      <admin-searchbar />
      <div class="bg-white rounded shadow overflow-x-auto">
        <table class="w-full whitespace-no-wrap">
          <tr class="text-left font-bold">
            <th class="px-6 pt-6 pb-4">
              {{ $t("words.recruitment_name_ar") }}
            </th>
            <th class="px-6 pt-6 pb-4">
              {{ $t("words.recruitment_name_en") }}
            </th>
            <th class="px-6 pt-6 pb-4">
              {{ $t("words.person-whose-add-recruitment") }}
            </th>
          </tr>
          <tr
            v-if="recruitmentCompany.name"
            v-for="recruitmentCompany in recruitmentCompanies.data"
            :key="recruitmentCompany.id"
            class="hover:bg-gray-100 focus-within:bg-gray-100"
          >
            <td class="border-t">
              <div class="px-6 py-4 flex items-center focus:text-indigo-500 justify-center">
                {{ recruitmentCompany.name }}<br />
                <!--<icon v-if="company.deleted_at" name="trash" class="flex-shrink-0 w-3 h-3 fill-gray-400 ml-2" />-->
              </div>
            </td>
            <td class="border-t">
              <div class="px-6 py-4 flex items-center justify-center">
                {{ recruitmentCompany.name_en }}
              </div>
            </td>
            <td class="border-t">
              <div class="px-6 py-4 flex items-center  justify-center">
                {{ recruitmentCompany.created_by.name }}
              </div>
            </td>
            <td class="border-t">
              <button class="bg-red-500 p-2 px-3 rounded" @click.prevent="deleteRecruitmentCompany(recruitmentCompany.id)">
                <span>{{ $t("words.delete") }}</span>
              </button>
            </td>
          </tr>
          <tr v-if="recruitmentCompanies.data.length === 0">
            <td class="border-t px-6 py-4" colspan="4">
              <empty-slate />
            </td>
          </tr>
        </table>
      </div>
      <pagination :links="recruitmentCompanies.links" />
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
    recruitmentCompanies: Object,
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
     deleteRecruitmentCompany(id) {
      if (confirm("Are you sure you want to delete this Recruitment?")) {
        this.$inertia.delete(`/back/settings/recruitment-companies/${id}`, {
          onSuccess: () => {
            this.$inertia.reload();
          }
        });
      }
    }
  },
};
</script>
