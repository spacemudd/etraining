<template>
  <app-layout>
    <div class="container px-6 mx-auto grid pt-6">
      <breadcrumb-container
        :crumbs="[
          { title: 'dashboard', link: route('dashboard') },
          { title: 'companies', link: route('back.companies.index') },
          { title_raw: company.name_ar },
          { title_raw: company.code },
        ]"
      ></breadcrumb-container>

      <div class="grid grid-cols-6 gap-6 mb-10">
        <div
          class="col-span-6 items-center justify-end bg-gray-50 text-right flex gap-6"
          v-if="company.deleted_at"
        >
          <div
            class="bg-red-600 font-bold text-white p-2 rounded-sm flex gap-2 w-full"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke-width="1.5"
              stroke="currentColor"
              class="w-6 h-6"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M12 9v3.75m0-10.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.75c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.75h-.152c-3.196 0-6.1-1.249-8.25-3.286zm0 13.036h.008v.008H12v-.008z"
              />
            </svg>
            {{ $t("words.company-is-deleted") }}
          </div>
        </div>
      </div>

      <div class="grid grid-cols-4 gap-6">
        <div
          class="col-span-4 flex items-center justify-end bg-gray-50 text-right gap-6"
        >
          <inertia-link
            v-can="'restrict-user-companies-settings'"
            :href="
              route('back.companies.allowed-users.index', {
                company_id: company.id,
              })
            "
            class="flex items-center justify-start rounded-md px-4 py-2 bg-gray-200 hover:bg-gray-300 text-right"
          >
            {{ $t("words.open-permissions") }}
          </inertia-link>
          <inertia-link
            v-if="$page.props.user.email === 'shafiqalshaar@clarastars.com'"
            :href="`/back/companies/${this.company.id}/ptcnet`"
            class="flex items-center justify-start rounded-md mx-4 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-right"
          >
            Mark as PTCNet <span v-if="this.company.is_ptc_net">T</span>
          </inertia-link>
          <inertia-link
            :href="`/back/companies/${this.company.id}/restore`"
            v-can="'restore-deleted-companies'"
            class="flex items-center justify-start rounded-md px-4 py-2 bg-gray-200 hover:bg-gray-300 text-right"
          >
            {{ $t("words.restore") }}
          </inertia-link>
          <inertia-link
            v-if="!company.deleted_at"
            :href="`/back/companies/${this.company.id}/edit`"
            class="flex items-center justify-start rounded-md px-4 py-2 bg-gray-200 hover:bg-gray-300 text-right"
          >
            {{ $t("words.edit") }}
          </inertia-link>
          <button
            v-if="!company.deleted_at"
            class="flex items-center justify-start rounded-md py-2 px-2 bg-red-600 text-white hover:bg-red-700 text-right"
            tabindex="-1"
            type="button"
            @click.prevent="deleteCompany()"
          >
            {{ $t("words.archive") }}
          </button>
        </div>

        <div class="col-span-4 sm:col-span-1">
          <jet-label for="center_id" :value="$t('words.center')" />
          <div class="relative mt-2">
            <select
              class="mt-1 block w-full bg-gray-100 appearance-none border border-gray-300 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:border-gray-500"
              v-model="company.center_id"
              id="center_id"
              disabled
            >
              <option
                v-for="center in centers"
                :key="center.id"
                :value="center.id"
              >
                {{ center.name }}
              </option>
            </select>
          </div>
          <jet-input-error :message="form.error('center_id')" class="mt-2" />
        </div>
        <div class="col-span-4 sm:col-span-1">
          <jet-label for="recruitment_company_id" :value="$t('words.recruitment-company')" />
          <div class="relative mt-2">
            <select
              class="mt-1 block w-full bg-gray-100 appearance-none border border-gray-300 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:border-gray-500"
              v-model="company.recruitment_company_id"
              id="recruitment_company_id"
              disabled
            >
              <option
                v-for="recruitmentCompany in recruitmentCompanies"
                :key="recruitmentCompany.id"
                :value="recruitmentCompany.id"
              >
                {{ recruitmentCompany.name }}
              </option>
            </select>
          </div>
          <jet-input-error :message="form.error('recruitment_company_id')" class="mt-2" />
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
            'nature_of_work',
            'salesperson_email',
            'salesperson_name',

          ]"
        >
          <div class="col-span-4 sm:col-span-1">
            <jet-label for="name" :value="$t('words.' + fieldName)" />
            <jet-input
              :id="fieldName"
              type="text"
              class="mt-1 block w-full bg-gray-200"
              :value="company[fieldName]"
              disabled
            />
            <div v-if="fieldName === 'name_ar'">
              <div class="flex justify-between text-xs mt-2">
                <inertia-link
                  class="text-blue-600"
                  :href="route('back.companies.aliases.index', company.id)"
                  >{{ $t("words.name-aliases") }} ({{
                    company.aliases_count
                  }})</inertia-link
                >
              </div>
            </div>
            <div v-if="fieldName === 'nature_of_work' && company.nature_of_work === 'عمل عن بعد'" class="mt-2 p-2 bg-red-100 text-red-800 rounded-md text-sm">
              هذه الشركة تعمل عن بعد
            </div>
          </div>
        </template>
        <div class="col-span-4 sm:col-span-1">
          <jet-label for="region_id" :value="$t('words.region')" />
          <div class="relative mt-2">
            <select
              class="mt-1 block w-full bg-gray-100 appearance-none border border-gray-300 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:border-gray-500"
              v-model="company.region_id"
              id="region_id"
              disabled
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
          <jet-input-error :message="form.error('region_id')" class="mt-2" />
        </div>
      </div>

      <!-- my comment -->

      <div class="mt-3">
        <inertia-link
          class="text-blue-600 border-2 border-blue-600 text-xs p-1"
          :href="route('back.companies.files.index', company.id)"
        >
          {{ $t("words.upload-image") }}
        </inertia-link>
      </div>

      <!--            <span v-if="this.company.is_ptc_net">&#45;&#45;</span>-->

      <!--            <span class="text-xs" v-if="this.company.region">{{ this.company.region.name }}</span>-->

      <div class="grid grid-cols-1 md:grid-cols-6 gap-6 mt-2">
        <div class="md:col-span-2 sm:col-span-3">
          <p class="text-xs">
            {{ $t("words.created-at") }}:
            <span dir="ltr">{{ company.created_at_timezone }}</span>
          </p>
        </div>
      </div>

      <jet-section-border></jet-section-border>

      <div
        v-can="'view-company-contracts'"
        class="grid grid-cols-1 md:grid-cols-6 gap-6 mt-2"
      >
        <div class="md:col-span-2 sm:col-span-3">
          <div class="px-4 sm:px-0">
            <h3 class="text-lg font-medium text-gray-900">
              {{ $t("words.contracts") }}
            </h3>

            <p class="mt-1 text-sm text-gray-600">
              {{ $t("words.contracts-help") }}
            </p>
          </div>
        </div>

        <div class="md:col-span-4 sm:col-span-1">
          <company-contracts-pagination
            :company-id="company.id"
            :instructors="instructors"
          />
        </div>
      </div>

      <jet-section-border></jet-section-border>

      <div
        v-can="'view-company-contracts'"
        class="grid grid-cols-1 md:grid-cols-6 gap-6 mt-2"
      >
        <div class="md:col-span-2 sm:col-span-3">
          <div class="px-4 sm:px-0">
            <h3 class="text-lg font-medium text-gray-900">
              {{ $t("words.trainees") }}
              <div class="mt-2 text-sm text-gray-600">
                <div>{{ $t("words.total-trainees") }}: {{ company.total_trainees_count }}</div>
                <div>{{ $t("words.active-trainees") }}: {{ company.trainees_count }}</div>
                <div>{{ $t("words.trashed-trainees") }}: {{ trainees_trashed_count }}</div>
                <div class="ml-4">- {{ $t("words.deleted-not-posted") }}: {{ company.trashed_not_posted_count }}</div>
                <div class="ml-4">- {{ $t("words.posted-trainees") }}: {{ company.posted_trainees_count }}</div>
              </div>
            </h3>
            <inertia-link
              class="text-sm mt-2 text-blue-500 hover:text-blue-700"
              :href="route('back.companies.trainees.activity-log', company.id)"
            >
              {{ $t("words.activity-log") }}
            </inertia-link>
          </div>
        </div>

        <div class="md:col-span-4 sm:col-span-1">
          <div class="flex justify-end items-center gap-4">



            <!-- <button
              @click="deleteTrainees(selected)"
              type="button"
              :disabled="!isSelected"
              class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest focus:outline-none focus:shadow-outline-gray transition ease-in-out duration-150 disabled:cursor-not-allowed bg-red-500 hover:bg-red-600 active:bg-red-700 foucs:bg-red-700"
            >
              {{ $t("words.block") }}
            </button> -->
   <div>
    <!-- <h1>
        {{ $t('words.reason') }}
    </h1> -->

    <div class="flex flex-row items-center justify-between">
        <select class="mt-1 w-full max-w-xs bg-gray-100 appearance-none border border-gray-300 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:border-gray-500"
                v-if="showSelect"
                v-model="deleted_remark"
                id="reason">
            <option value="عدم سداد">عدم سداد</option>
            <option value="استبعاد من الشركة">استبعاد من الشركة</option>
            <option value="استقالة">استقالة</option>
            <option value="انسحاب">انسحاب</option>
            <option value="عمل مشاكل">عمل مشاكل</option>
            <option value="غير مسجلة في الشركة">غير مسجلة في الشركة</option>
            <option value="عدم الالتزام في شروط المعهد">عدم الالتزام في شروط المعهد</option>
            <option value="رجيع">رجيع</option>
            <option value="غير مؤهل">غير مؤهل</option>
            <option value="حساب مكرر">حساب مكرر</option>
            <option value="استبعاد لعدم الحضور">استبعاد لعدم الحضور</option>
            <option value="عدم التقييد بشرح المعهد">عدم التقييد بشرح المعهد</option>
            <option value="لديها سجل التجاري">لديها سجل التجاري</option>
            <option value="مستنفذة للدعم">مستنفذة للدعم</option>
            <option value="عدم سداد مستحق مالي / غير نشط في التأمينات">عدم سداد مستحق مالي / غير نشط في التأمينات</option>
            <option value="رفضت التوقيع على الاعتراض">رفضت التوقيع على الاعتراض</option>
            <option value="حذف من قبل التأمينات">حذف من قبل التأمينات</option>
            <option value="قائمة سوداء">قائمة سوداء</option>
            <option value="المباشرة في مقر الشركة">المباشرة في مقر الشركة</option>
        </select>

        <button @click="showSelect = true"
                 v-if="!showSelect"
                class="inline-flex items-center px-8 mr-5  py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest focus:outline-none focus:shadow-outline-gray transition ease-in-out duration-150 disabled:cursor-not-allowed bg-red-500 hover:bg-red-600 active:bg-red-700"
                :disabled="!isSelected">
            {{ $t('words.suspend') }}
        </button>

          <button
              @click="deleteTrainees(selected)"
               v-if="showSelect"
              type="button"
              :disabled="!isSelected"
              class="inline-flex items-center px-8 mr-5  py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest focus:outline-none focus:shadow-outline-gray transition ease-in-out duration-150 disabled:cursor-not-allowed bg-red-500 hover:bg-red-600 active:bg-red-700"
            >
              {{ $t("words.block") }}
            </button>
    </div>
</div>





            <button

              @click="unBlockTrainees(selected)"
              type="button"
              :disabled="!isSelected"
              class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest focus:outline-none focus:shadow-outline-gray transition ease-in-out duration-150 disabled:cursor-not-allowed bg-green-500 hover:bg-green-600 active:bg-green-700 foucs:bg-green-700"
            >
              {{ $t("words.unblock") }}
            </button>
            <post-trainees-button
              :company-id="company.id"
            ></post-trainees-button>
            <inertia-link
              class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150"
              :href="
                route('back.companies.trainees.company-trainee-link-audit', {
                  company_id: company.id,
                })
              "
            >
              <span>{{ $t("words.history") }}</span>
            </inertia-link>
            <a
              class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150"
              target="_blank"
              :href="
                route('back.companies.trainees.excel', {
                  company_id: company.id,
                })
              "
            >
              <span>{{ $t("words.export") }}</span>
            </a>
          </div>
          <table
            class="w-full whitespace-no-wrap bg-white rounded-lg my-5 p-5 shadow text-sm"
          >
            <tr class="text-left font-bold">
              <th class="p-4 pr-8">
                <input
                  type="checkbox"
                  v-model="select_all"
                  @click="selectAll()"
                />
              </th>
              <th class="p-4">{{ $t("words.name") }}</th>
              <th class="p-4">{{ $t("words.group-name") }}</th>
              <th class="p-4">{{ $t("words.phone") }}</th>
            </tr>
            <tr
              v-for="trainees in company.trainees"
              :key="trainees.id"
              class="hover:bg-gray-100 focus-within:bg-gray-100"
            >
              <td class="border-t pr-8">
                <input
                  type="checkbox"
                  :value="trainees.id"
                  v-model="selected"
                />
              </td>
              <td class="border-t">
                <div class="px-4 py-2 flex items-center focus:text-indigo-500">
                  <inertia-link :href="trainees.show_url">
                    <span
                      v-if="trainees.deleted_at"
                      class="inline-block mt-2 p-1 px-2 bg-red-600 text-white rounded-lg"
                    >
                      {{ $t("words.blocked") }}
                    </span>
                    <span
                      v-if="trainees.is_pending_uploading_files"
                      class="inline-block mt-2 p-1 px-2 bg-blue-300 rounded-lg"
                    >
                      {{ $t("words.incomplete-application") }}
                    </span>
                    <span
                      v-if="trainees.is_pending_approval"
                      class="inline-block mt-2 p-1 px-2 bg-yellow-200 rounded-lg"
                    >
                      {{ $t("words.nominated-instructor") }}
                    </span>
                    <span
                      v-if="trainees.is_approved"
                      class="inline-block mt-2 p-1 px-2 bg-green-300 rounded-lg"
                    >
                      {{ $t("words.approved") }}
                    </span>
                    {{ trainees.name }}
                  </inertia-link>
                </div>
              </td>
              <td class="border-t">
                <inertia-link
                  id="group-name"
                  class="px-4 py-2 flex items-center"
                  :href="route('back.trainees.show', trainees.id)"
                  tabindex="-1"
                >
                  <div v-if="trainees.trainee_group_id">
                    <span v-if="trainees.trainee_group">{{
                      trainees.trainee_group.name
                    }}</span>
                  </div>
                </inertia-link>
              </td>
              <td class="border-t">
                <inertia-link
                  class="px-4 py-2 flex items-center"
                  :href="route('back.trainees.show', trainees.id)"
                  tabindex="-1"
                >
                  <div v-if="trainees.phone">
                    {{ trainees.phone }}
                  </div>
                </inertia-link>
              </td>

              <td class="border-t w-px">
                <inertia-link
                  class="px-4 flex items-center"
                  :href="route('back.trainees.show', trainees.id)"
                  tabindex="-1"
                >
                  <ion-icon
                    name="arrow-forward-outline"
                    class="block w-6 h-6 fill-gray-400"
                  ></ion-icon>
                </inertia-link>
              </td>
            </tr>
            <tr v-if="company.trainees.length === 0">
              <td class="border-t px-4 py-4" colspan="4">
                <empty-slate />
              </td>
            </tr>
          </table>
        </div>
      </div>

      <jet-section-border></jet-section-border>

      <div v-can="'issue-monthly-invoices'" class="grid grid-cols-1 gap-6 mt-2">
        <div class="flex justify-between items-center">
          <div class="px-4 sm:px-0">
            <h3 class="text-lg font-medium text-gray-900">
              {{ $t("words.invoices") }}
            </h3>
          </div>

          <inertia-link
            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150"
            :href="
              route('back.companies.invoices.create', { company: company.id })
            "
          >
            <span>{{ $t("words.issue-invoice") }}</span>
          </inertia-link>
        </div>

        <div>
          <div class="flex">
            <div class="flex ml-2">
              <h3>{{ $t("words.from-date") }}</h3>
              <input
                class="bg-gray-200 mx-1 px-2 rounded-sm"
                type="date"
                v-model="reportDateFrom"
              />
            </div>
            <div class="flex mx-2">
              <h3>{{ $t("words.to-date") }}</h3>
              <input
                class="bg-gray-200 mx-1 px-2 rounded-sm"
                type="date"
                v-model="reportDateTo"
              />
            </div>
            <a
              class="mx-2"
              :href="
                route('back.companies.invoices.bulk-pdf', {
                  company_id: company.id,
                  from_date: reportDateFrom,
                  to_date: reportDateTo,
                })
              "
              target="_blank"
            >
              {{ $t("words.print") }}
            </a>
          </div>
          <table
            class="w-full whitespace-no-wrap bg-white rounded-lg my-5 p-5 shadow text-sm"
          >
            <tr class="text-left font-bold">
              <th class="p-4">{{ $t("words.date-created") }}</th>
              <th class="p-4">{{ $t("words.date-period") }}</th>
              <th class="p-4">{{ $t("words.trainees") }}</th>
              <th class="p-4">{{ $t("words.grand-total") }}</th>
              <th class="p-4">{{ $t("words.initiated-by") }}</th>
              <th class="p-4">{{ $t("words.actions") }}</th>
            </tr>
            <tr
              v-for="invoice in invoices"
              :key="invoice.id"
              class="border-t hover:bg-gray-100 focus-within:bg-gray-100"
            >
              <td class="px-4 py-4">
                {{ invoice.created_at_date }}
              </td>

              <td class="px-4 py-4">
                {{ invoice.from_date | formatDate }}
                <br />
                {{ invoice.to_date | formatDate }}
              </td>

              <td class="px-4 py-4">
                {{ invoice.trainee_count }}
              </td>

              <td class="px-4 py-4">
                {{ invoice.grand_total }}
              </td>

              <td class="px-4 py-4">
                {{ invoice.created_by ? invoice.created_by.name : "Unknown" }}
              </td>

              <td class="w-px">
                <a
                  class="px-4 flex items-center"
                  :href="
                    route('back.companies.invoices.pdf', {
                      company_id: company.id,
                      from_date: invoice.from_date,
                      to_date: invoice.to_date,
                      created_by_id: invoice.created_by_id,
                      created_at_date: invoice.created_at_date,
                    })
                  "
                  tabindex="-1"
                  target="_blank"
                >
                  {{ $t("words.download") }}
                  <ion-icon
                    name="arrow-forward-outline"
                    class="block w-6 h-6 fill-gray-400"
                  ></ion-icon>
                </a>
              </td>
              <td class="rtl:text-right text-black">
                <!--                                {{ invoice.id }}-->
                <button
                  @click="deleteInvoice(invoice)"
                  v-can="'can-delete-invoice-anytime'"
                  type="button"
                  v-if="!invoice.paid_at"
                  class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest focus:outline-none focus:shadow-outline-gray transition ease-in-out duration-150 disabled:cursor-not-allowed mx-2 bg-red-500 hover:bg-red-600 active:bg-red-700 foucs:bg-red-700"
                >
                  {{ $t("words.delete") }}
                </button>
              </td>
              <td class="rtl:text-right text-black">
                <!--                                {{ invoice.id }}-->
                <a
                  class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest focus:outline-none focus:shadow-outline-gray transition ease-in-out duration-150 disabled:cursor-not-allowed mx-2 bg-gray-700 hover:bg-gray-600 active:bg-red-700 foucs:bg-red-700"
                  :href="
                    route('back.companies.invoices.date-period', {
                      company_id: company.id,
                      from_date: invoice.from_date,
                      to_date: invoice.to_date,
                      created_by_id: invoice.created_by_id,
                      created_at_date: invoice.created_at_date,
                    })
                  "
                  tabindex="-1"
                  target="_blank"
                >
                  {{ $t("words.change-date-period") }}
                </a>
                <!--                                <button @click="ChangeDatePeriod(invoice)"-->
                <!--                                        v-can="'can-delete-invoice-anytime'"-->
                <!--                                        type="button"-->
                <!--                                        v-if="invoice.status <= 4 && invoice.payment_method !=1"-->
                <!--                                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest focus:outline-none focus:shadow-outline-gray transition ease-in-out duration-150 disabled:cursor-not-allowed mx-2 bg-gray-700 hover:bg-gray-600 active:bg-red-700 foucs:bg-red-700">-->
                <!--                                    {{ $t('words.change-date-period') }}-->
                <!--                                </button>-->
              </td>
            </tr>

            <tr v-if="invoices.length === 0">
              <td class="border-t px-4 py-4" colspan="6">
                <empty-slate />
              </td>
            </tr>
          </table>
        </div>
      </div>

      <jet-section-border></jet-section-border>

      <div v-can="'manage-resignations'" class="grid grid-cols-1 gap-6 mt-2">
        <div class="flex justify-between items-center">
          <div class="px-4 sm:px-0">
            <h3 class="text-lg font-medium text-gray-900">
              {{ $t("words.resignations") }}
            </h3>
          </div>

          <inertia-link
            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150"
            :href="
              route('back.resignations.create', { company_id: company.id })
            "
          >
            <span>{{ $t("words.new") }}</span>
          </inertia-link>
        </div>

        <div>
          <table
            class="w-full whitespace-no-wrap bg-white rounded-lg my-5 p-5 shadow text-sm"
          >
            <tr class="text-left font-bold">
              <th class="p-4">{{ $t("words.created-by") }}</th>
              <th class="p-4">{{ $t("words.emails") }}</th>
              <th class="p-4">{{ $t("words.trainees") }}</th>
              <th class="p-4">{{ $t("words.status") }}</th>
              <th class="p-4">{{ $t("words.actions") }}</th>
            </tr>
            <tr
              v-for="resignation in company.resignations"
              :key="resignation.id"
              class="hover:bg-gray-100 focus-within:bg-gray-100"
            >
              <td class="border-t">
                <div class="px-4 py-2 focus:text-indigo-500">
                  {{ resignation.number }}<br />
                  {{ resignation.created_by.email }}
                  <br />
                  <p dir="ltr" class="text-right text-gray-400 text-sm">
                    {{ resignation.created_at_timezone }}
                  </p>
                </div>
              </td>
              <td class="border-t">
                <div class="px-4 py-2 focus:text-indigo-500">
                  {{ resignation.emails_to }}
                  <template v-if="resignation.emails_cc"
                    ><br />{{ resignation.emails_cc }}</template
                  >
                  <template v-if="resignation.emails_bcc"
                    ><br />{{ resignation.emails_bcc }}</template
                  >
                </div>
              </td>
              <td class="border-t w-px">
                <div class="px-4 py-2 flex items-center focus:text-indigo-500">
                  {{ resignation.trainees_count }}
                </div>
              </td>
              <td class="border-t w-px">
                <div class="px-4 py-2 flex items-center focus:text-indigo-500">
                  <template v-if="resignation.sent_at">
                    <br />
                    <p class="text-right text-sm">
                      {{ $t("words.sent") }} <br /><span dir="ltr">{{
                        resignation.sent_at_timezone
                      }}</span>
                    </p>
                  </template>
                  <p v-else>{{ $t("words." + resignation.status) }}</p>
                </div>
              </td>

              <td class="border-t w-px">
                <div
                  class="px-4 py-2 flex items-center focus:text-indigo-500 gap-2"
                >
                  <button
                    class="bg-red-500 text-white px-2 py-1 rounded disabled:bg-yellow-100"
                    v-if="!resignation.sent_at"
                    @click="destroyResignation(resignation)"
                    :href="
                      route('back.resignations.destroy', {
                        company_id: resignation.company_id,
                        resignation: resignation.id,
                      })
                    "
                  >
                    {{ $t("words.delete") }}
                  </button>
                  <inertia-link
                    class="bg-yellow-300 text-black px-2 py-1 rounded"
                    :href="
                      route('back.resignations.upload', {
                        company_id: resignation.company_id,
                        id: resignation.id,
                      })
                    "
                  >
                    {{ $t("words.resignation-file") }}
                  </inertia-link>
                  <button
                    class="bg-yellow-300 text-black px-2 py-1 rounded disabled:bg-yellow-100"
                    v-if="resignation.has_file && !resignation.sent_at"
                    @click="approveResignation(resignation)"
                    :href="
                      route('back.resignations.approve', {
                        company_id: resignation.company_id,
                        id: resignation.id,
                      })
                    "
                  >
                    {{ $t("words.approve") }}
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="company.trainees.length === 0">
              <td class="border-t px-4 py-4" colspan="4">
                <empty-slate />
              </td>
            </tr>
          </table>
        </div>
      </div>
      <div class="grid grid-cols-1 gap-6 mt-2">
        <div>
          <table
            class="w-full whitespace-no-wrap bg-white rounded-lg my-5 p-5 shadow text-sm"
          >
            <tr class="text-left font-bold">
              <th class="p-4">{{ $t("words.sender") }}</th>
              <th class="p-4">{{ $t("words.mail-subject") }}</th>
              <th class="p-4">{{ $t("words.created-at") }}</th>
              <th class="p-4">{{ $t("words.actions") }}</th>
            </tr>
            <tr
              v-for="company_mail in company.company_mails"
              :key="company_mail.id"
              class="hover:bg-gray-100 focus-within:bg-gray-100"
            >
              <td class="border-t">
                <div class="px-4 py-2 focus:text-indigo-500">
                  {{ company_mail.from }}<br />
                  {{ company_mail.sender }}
                </div>
              </td>
              <td class="border-t">
                <div class="px-4 py-2 focus:text-indigo-500">
                  {{ company_mail.subject }}
                </div>
              </td>
              <td class="border-t w-px">
                <div class="px-4 py-2 flex items-center focus:text-indigo-500">
                  {{ company_mail.created_at }}
                </div>
              </td>

              <td class="border-t w-px">
                <div class="px-4 py-2 flex items-center focus:text-indigo-500">
                  <inertia-link
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest focus:outline-none focus:shadow-outline-gray transition ease-in-out duration-150 disabled:cursor-not-allowed mx-2 bg-gray-500 hover:bg-gray-600 active:bg-gray-700 foucs:bg-gray-700"
                    :href="
                      route('back.companies.mail', {
                        company_id: company_mail.company_id,
                        id: company_mail.id,
                      })
                    "
                  >
                    {{ $t("words.view") }}
                  </inertia-link>
                </div>
              </td>
            </tr>

            <tr v-if="company.trainees.length === 0">
              <td class="border-t px-4 py-4" colspan="4">
                <empty-slate />
              </td>
            </tr>
          </table>
        </div>
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
import CompanyContractsPagination from "@/Components/CompanyContractsPagination";
import BreadcrumbContainer from "@/Components/BreadcrumbContainer";
import EmptySlate from "@/Components/EmptySlate";
import PostTraineesButton from "@/Components/PostTraineesButton";
import Input from "../../../Jetstream/Input";

export default {
  metaInfo: { title: "Files" },
  props: [
    "sessions",
    "company",
    "generalFiles",
    "instructors",
    "invoices",
    "trainees_trashed_count",
    "regions",
    "centers",
    "trainees",
    "company_mails",
    "recruitmentCompanies",
  ],

  components: {
    Input,
    PostTraineesButton,
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
    CompanyContractsPagination,
    BreadcrumbContainer,
    EmptySlate,
  },
  data() {
    return {
      showSelect: false,
      deleted_remark: null,
      select_all: false,
      isSelected: false,
      // selectedTrainees: [],
      selected: [],
      reportDateFrom: null,
      reportDateTo: null,
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
            nature_of_work: "",
          salesperson_email: "",
          salesperson_name: "",
          region_id: "",
          center_id: "",
          recruitment_company_id: "",

        },
        {
          bag: "createCompany",
        }
      ),
    };
  },
  mounted() {
    this.$wait.end("SAVING_FILE");
  },

  watch: {
    selected: function (selected) {
      this.isSelected = selected.length > 0;
      this.select_all = selected.length === this.company.trainees.length;
    },
  },
  methods: {
    getSelected() {
      axios({
        url: "/back/companies",
        method: "get",
      })
        .then((res) => {
          this.selectedTrainees = res.company.trainees.rows;
        })
        .catch((err) => {
          console.log(err);
        });
    },
    selectAll() {
      this.selected = [];
      if (!this.select_all) {
        for (let i in this.company.trainees) {
          this.selected.push(this.company.trainees[i].id);
        }
      } else {
        this.select_all = false;
      }
    },
    // selectSinlge() {
    //     if(!this.selectedTrainees.length === this.selected.length){
    //         this.select_all = true
    //     } else {
    //         this.select_all = false
    //     }
    // if(this.selected !== 0){
    //     this.isSelected = true;
    // } else {
    //     this.isSelected = false;
    // }
    // },
    destroyResignation(resignation) {
      if (confirm(this.$t("words.are-you-sure"))) {
        this.$inertia.delete(
          route("back.resignations.destroy", {
            company_id: resignation.company_id,
            resignation: resignation.id,
          })
        );
      }
    },
    approveResignation(resignation) {
      if (confirm(this.$t("words.are-you-sure"))) {
        this.$inertia.post(
          route("back.resignations.approve", {
            company_id: resignation.company_id,
            id: resignation.id,
          })
        );
      }
    },
    deleteInvoice(invoiceCollection) {
        let reason = prompt(this.$t('words.reason'));
            if (reason === null || reason === '') {
                alert('يجب وجود سبب لحذف الفاتورة');
                return;
            }

      if (confirm(this.$t("words.are-you-sure"))) {
        this.$inertia.delete(
          route("back.finance.invoices.destroy", {
            deleted_reason: reason,
            invoice: invoiceCollection.id,
            from_date: invoiceCollection.from_date,
            to_date: invoiceCollection.to_date,
            created_at_date: invoiceCollection.created_at_date,
            created_by_id: invoiceCollection.created_by_id,
            company_id: invoiceCollection.company_id,
          })
        );
      }
    },
    suspendTrainees(traineeCollection) {
      if (confirm(this.$t("words.are-you-sure"))) {
        this.$inertia.delete(
          route("back.trainees.suspend.all", {
            trainees: traineeCollection,
          })
        );
      }
    },
    createCompany() {
      this.form.post("/back/companies", {
        preserveScroll: true,
      });
    },
    deleteCompany() {
      if (confirm(this.$t("words.are-you-sure"))) {
        this.$inertia.delete("/back/companies/" + this.company.id);
      }
    },
  deleteTrainees: function (selected) {
  const batchSize = 50;
  const batches = [];

  for (let i = 0; i < selected.length; i += batchSize) {
    batches.push(selected.slice(i, i + batchSize));
  }

  batches.forEach((batch) => {
    if (confirm(this.$t("words.are-you-sure"))) {
      console.log(batch);
      this.$inertia.post(
        route("back.trainees.suspend.all", {
          data: batch,
          deleted_remark: this.deleted_remark,
        })
      );
    }
  });
},

    unBlockTrainees: function (selected) {
      if (confirm(this.$t("words.are-you-sure"))) {
        this.$inertia.post(
          route("back.trainees.unblock.all", {
            data: selected,
          })
        );
      }
    },

    handleFileUpload: function (event) {
      this.form.image = event.target.files[0];
    },
  },
};
</script>

