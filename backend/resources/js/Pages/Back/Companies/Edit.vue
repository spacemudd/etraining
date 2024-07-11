<template>
  <app-layout>
    <div class="container px-6 mx-auto grid pt-6">
      <breadcrumb-container
        :crumbs="[
          { title: 'dashboard', link: route('dashboard') },
          { title: 'companies', link: route('back.companies.index') },
          {
            title_raw: company.name_ar,
            link: route('back.companies.index', { company_id: company.id }),
          },
          { title: 'edit' },
        ]"
      ></breadcrumb-container>

      <div class="mt-4">
        <jet-form-section @submitted="updateCompany">
          <template #title>
            {{ $t("words.edit") }}
          </template>

          <template #form>
            <div class="col-span-2 sm:col-span-2">
              <jet-label for="center_id" :value="$t('words.center')" />
              <div class="relative">
                <select
                  class="block appearance-none w-full border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                  v-model="form.center_id"
                  id="educational_level_id"
                >
                  <option
                    v-for="center in centers"
                    :key="center.id"
                    :value="center.id"
                  >
                    {{ center.name_ar }}
                  </option>
                </select>
                <div
                  class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700"
                >
                  <svg
                    class="fill-current h-4 w-4"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20"
                  >
                    <path
                      d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"
                    />
                  </svg>
                </div>
              </div>
              <jet-input-error
                :message="form.error('center_id')"
                class="mt-2"
              />
            </div>
            <div class="col-span-2 sm:col-span-2">
              <jet-label for="recruitment_company_id" :value="$t('words.recruitment-company')" />
              <div class="relative">
                <select
                  class="block appearance-none w-full border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                  v-model="form.recruitment_company_id"
                  id="educational_level_id"
                >
                  <option value=""></option>
                  <option
                    v-for="recruitmentCompany in recruitmentCompanies"
                    :key="recruitmentCompany.id"
                    :value="recruitmentCompany.id"
                  >
                    {{ recruitmentCompany.name }}
                  </option>
                </select>
                <div
                  class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700"
                >
                  <svg
                    class="fill-current h-4 w-4"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20"
                  >
                    <path
                      d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"
                    />
                  </svg>
                </div>
              </div>
              <jet-input-error
                :message="form.error('recruitment_company_id')"
                class="mt-2"
              />
            </div>

            <template
              v-for="fieldName in [
                'name_ar',
                'name_en',
                'cr_number',
                'contact_number',
                'company_rep',
                'company_rep_mobile',
                'address',
                'email',
                'monthly_subscription_per_trainee',
                'shelf_number',
                'salesperson_email',
              ]"
            >
              <div class="col-span-2 sm:col-span-2">
                <jet-label for="name" :value="$t('words.' + fieldName)" />
                <jet-input
                  id="name"
                  type="text"
                  class="mt-1 block w-full"
                  v-model="form[fieldName]"
                  autocomplete="name"
                  :autofocus="fieldName === 'name_ar'"
                />
                <jet-input-error
                  :message="form.error(fieldName)"
                  class="mt-2"
                />
              </div>
            </template>
            <div class="col-span-4 sm:col-span-1">
              <jet-label for="region_id" :value="$t('words.region')" />
              <div class="relative mt-2">
                <select
                  class="mt-1 block w-full bg-gray-100 appearance-none border border-gray-300 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:border-gray-500"
                  v-model="form.region_id"
                  id="region_id"
                >
                  <option
                    v-for="region in regions"
                    :key="region.id"
                    :value="region.id"
                  >
                    {{ region.name }}
                  </option>
                </select>
              </div>
              <jet-input-error
                :message="form.error('region_id')"
                class="mt-2"
              />
            </div>
          </template>

          <template #actions>
            <jet-action-message :on="form.recentlySuccessful" class="mr-3">
              Saved.
            </jet-action-message>

            <inertia-link
              :href="`/back/companies/${company.id}`"
              class="flex items-center justify-start rtl:ml-4 ltr:mr-4 rounded-md px-4 py-2 bg-white hover:bg-gray-300 text-right"
            >
              {{ $t("words.cancel") }}
            </inertia-link>

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
import Breadcrumb from "@/Components/Breadcrumb";
import JetDialogModal from "@/Jetstream/DialogModal";
import JetInput from "@/Jetstream/Input";
import JetInputError from "@/Jetstream/InputError";
import JetActionMessage from "@/Jetstream/ActionMessage";
import JetButton from "@/Jetstream/Button";
import JetFormSection from "@/Jetstream/FormSection";
import JetLabel from "@/Jetstream/Label";
import BreadcrumbContainer from "@/Components/BreadcrumbContainer";

export default {
  props: ["sessions", "company", "regions", "centers", "recruitmentCompanies"],

  components: {
    AppLayout,
    JetSectionBorder,
    Breadcrumb,
    JetDialogModal,
    JetInput,
    JetInputError,
    JetActionMessage,
    JetButton,
    JetFormSection,
    JetLabel,
    BreadcrumbContainer,
  },
  mounted() {
    this.form.name_ar = this.company.name_ar;
    this.form.name_en = this.company.name_en;
    this.form.cr_number = this.company.cr_number;
    this.form.contact_number = this.company.contact_number;
    this.form.company_rep = this.company.company_rep;
    this.form.company_rep_mobile = this.company.company_rep_mobile;
    this.form.email = this.company.email;
    this.form.address = this.company.address;
    this.form.monthly_subscription_per_trainee =
      this.company.monthly_subscription_per_trainee;
    this.form.shelf_number = this.company.shelf_number;
    this.form.region_id = this.company.region_id;
    this.form.center_id = this.company.center_id;
    this.form.recruitment_company_id = this.company.recruitment_company_id;
    this.form.salesperson_email = this.company.salesperson_email;
   
  },
  data() {
    return {
      form: this.$inertia.form(
        {
          name_ar: "",
          name_en: "",
          cr_number: "",
          contact_number: "",
          company_rep: "",
          company_rep_mobile: "",
          address: "",
          email: "",
          monthly_subscription_per_trainee: "",
          shelf_number: "",
          salesperson_email: "",
          region_id: "",
          center_id: "",
          recruitment_company_id: "",
        },
        {
          bag: "updateCompany",
        }
      ),
    };
  },
  methods: {
    updateCompany() {
      this.form.put("/back/companies/" + this.company.id, {
        preserveScroll: true,
      });
    },
  },
};
</script>
