<template>
  <app-layout>
    <div class="container px-6 mx-auto grid pt-6">
      <breadcrumb-container
        :crumbs="[
          { title: 'dashboard', link: route('dashboard') },
          { title: 'settings', link: route('back.settings') },
          {
            title: 'recruitment-companies',
            link: route('back.settings.recruitment-companies.index'),
          },
          { title: 'new' },
        ]"
      ></breadcrumb-container>
      <div class="mt-4">
        <!-- my comment -->
        <jet-form-section @submitted="createRecruitmentCompany">
          <template #title>
            {{ $t("words.open-new-recruitment-company") }}
          </template>

          <template #description>
            {{ $t("words.open-new-recruitment-company-description") }}
          </template>

          <template #form>
            <div class="col-span-6 sm:col-span-2">
              <jet-label for="name_ar" :value="$t('words.recruitment_name_ar')" />
              <jet-input
                id="name_ar"
                type="text"
                class="mt-1 block w-full"
                v-model="form.recruitment_name_ar"
                autocomplete="off"
                autofocus
              />
              <jet-input-error
                :message="form.error('recruitment_name_ar')"
                class="mt-2"
              />
               

            </div>
            <div class="col-span-6 sm:col-span-2">
              <jet-label for="name_en" :value="$t('words.recruitment_name_en')" />
              <jet-input
                id="name_en"
                type="text"
                class="mt-1 block w-full"
                v-model="form.recruitment_name_en"
                autocomplete="off"
                autofocus
              />
            </div>
          </template>

          <template #actions>
            <jet-action-message :on="form.recentlySuccessful" class="mr-3">
              {{ $t("words.saved-successfully") }}
            </jet-action-message>

            <!-- <inertia-link
              href="/back/companies"
              class="flex items-center justify-start rtl:ml-4 ltr:mr-4 rounded-md px-4 py-2 bg-white hover:bg-gray-300 text-right"
            >
              {{ $t("words.cancel") }}
            </inertia-link> -->

            <jet-button
              :class="{ 'opacity-25': form.processing }"
              :disabled="form.processing"
            >
              {{ $t("words.save") }}
            </jet-button>
          </template>
        </jet-form-section>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import JetSectionBorder from "@/Jetstream/SectionBorder";
import JetDialogModal from "@/Jetstream/DialogModal";
import JetInput from "@/Jetstream/Input";
import JetInputError from "@/Jetstream/InputError";
import JetActionMessage from "@/Jetstream/ActionMessage";
import JetButton from "@/Jetstream/Button";
import JetFormSection from "@/Jetstream/FormSection";
import JetLabel from "@/Jetstream/Label";
import BreadcrumbContainer from "@/Components/BreadcrumbContainer";

export default {
  props: ["sessions", "regions", "centers"],

  components: {
    AppLayout,
    JetSectionBorder,
    JetDialogModal,
    JetInput,
    JetInputError,
    JetActionMessage,
    JetButton,
    JetFormSection,
    JetLabel,
    BreadcrumbContainer,
  },
  data() {
    return {
      form: this.$inertia.form(
        {
          recruitment_name_ar: "",
          recruitment_name_en: "",
        },
        {
          bag: "createRecruitmentCompany",
        }
      ),
    };
  },
methods: {
    createRecruitmentCompany() {
        this.form.post("/back/settings/recruitment-companies/store", {
            preserveScroll: true,
        });
    },
},


};
</script>
