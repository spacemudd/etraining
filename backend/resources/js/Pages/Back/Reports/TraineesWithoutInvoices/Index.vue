<!--
  - Copyright (c) 2020 - Clarastars, LLC  - All Rights Reserved.
  -
  - Unauthorized copying of this file via any medium is strictly prohibited.
  - This file is a proprietary of Clarastars LLC and is confidential / educational purpose only.
  -
  - https://clarastars.com - info@clarastars.com
  - @author Shafiq al-Shaar <shafiqalshaar@gmail.com>
  -->

<template>
  <app-layout>
    <div class="container px-6 mx-auto grid pt-6">
      <breadcrumb-container
        :crumbs="[
          { title: 'dashboard', link: route('dashboard') },
          { title: 'reports', link: route('back.reports.index') },
          {
            title: 'trainees-without-invoice',
            link: route('back.reports.contracts.index'),
          },
        ]"
      ></breadcrumb-container>

     

      <template v-if="report_status === 'new'">
        <form @submit.prevent="generateReport">
          
          <div class="grid grid-cols-12 gap-6">

            <div class="col-span-12 sm:col-span-2 mt-5">
              <jet-label
                class="mb-2"
                for="date_from"
                :value="$t('words.date-from')"
              />
              <input
                name="date_from"
                type="date"
                v-model="form.date_from"
                class="form-input rounded-md shadow-sm w-full"
                required
              />
            </div>

            <div class="col-span-12 sm:col-span-2 mt-5">
              <jet-label
                class="mb-2"
                for="date_to"
                :value="$t('words.date-to')"
              />
              <input
                name="date_to"
                type="date"
                v-model="form.date_to"
                class="form-input rounded-md shadow-sm w-full"
                required
              />
            </div>
          </div>

          <button
            class="btn btn-gray mt-5"
            type="submit"
            :disabled="form.processing"
          >
            {{ $t("words.export") }}
          </button>
        </form>
      </template>
    </div>
  </app-layout>
</template>

<script>
import JetLabel from "@/Jetstream/Label";
import AppLayout from "@/Layouts/AppLayout";
import IconNavigate from "vue-ionicons/dist/ios-arrow-dropright";
import BreadcrumbContainer from "@/Components/BreadcrumbContainer";
import BtnLoadingIndicator from "../../../../Components/BtnLoadingIndicator";
import { months } from "moment";

export default {
  
  mounted() {
  },

  metaInfo() {
    return {
      title: this.$t("words.trainees-without-invoice"),
    };
  },
  components: {
    IconNavigate,
    AppLayout,
    JetLabel,
    BreadcrumbContainer,
    BtnLoadingIndicator,
  },
  computed: {
    token() {
      return document.head.querySelector('meta[name="csrf-token"]').content;
    },
  },
  data() {
    return {
      report_status: "new",
      job_tracker: null,
      form: {
        processing: false,
        date_from: new Date().toISOString().substring(0, 10),
        date_to: new Date().toISOString().substring(0, 10),
      },
    };
  },
  methods: {
    generateReport() {
       window.location.href = route("back.reports.trainees-witout-invoices.export", this.form);
    },
   
  },
};
</script>
