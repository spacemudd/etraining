<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'trainees', link: route('back.trainees.index')},
                    {title_raw: trainee.name},
                ]"
            ></breadcrumb-container>

            <validation-errors :errors="validationErrors" v-if="validationErrors"></validation-errors>

            <div class="grid grid-cols-6 gap-6">

                <div class="col-span-6 items-center justify-end bg-gray-50 text-right flex gap-6"
                     v-if="trainee.dont_edit_notice">
                    <div class="bg-red-600 font-bold text-white p-2 rounded-sm flex gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0-10.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.75c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.75h-.152c-3.196 0-6.1-1.249-8.25-3.286zm0 13.036h.008v.008H12v-.008z" />
                        </svg>
                        {{ $t('words.caution-dont-take-action-against-this-account-without-admin-approval') }}
                    </div>
                </div>

                <div class="col-span-6 items-center justify-end bg-gray-50 text-right flex gap-6"
                     v-if="in_block_list">
                    <div class="bg-red-600 p-2">
                        <div class="font-bold text-white p-2 rounded-sm flex gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0-10.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.75c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.75h-.152c-3.196 0-6.1-1.249-8.25-3.286zm0 13.036h.008v.008H12v-.008z" />
                            </svg>
                            {{ $t('words.trainee-in-block-directory') }}
                        </div>
                        <div class="font-bold text-white p-2 rounded-sm flex gap-2">
                        <table class="table table-bordered table-hover text-xs w-full">
                        	<tbody>
                        			<tr v-if="in_block_list.name">
                        				<td class="border px-1">{{ $t('words.name') }}</td>
                                        <td class="border px-1">{{ in_block_list.name }}</td>
                        			</tr>
                                    <tr v-if="in_block_list.phone">
                        				<td class="border px-1">{{ $t('words.phone') }}</td>
                                        <td class="border px-1">{{ in_block_list.phone }}</td>
                        			</tr>
                                    <tr v-if="in_block_list.email">
                        				<td class="border px-1">{{ $t('words.email') }}</td>
                                        <td class="border px-1">{{ in_block_list.email }}</td>
                        			</tr>
                                    <tr v-if="in_block_list.identity_number">
                        				<td class="border px-1">{{ $t('words.identity_number') }}</td>
                                        <td class="border px-1">{{ in_block_list.identity_number }}</td>
                        			</tr>
                                    <tr v-if="in_block_list.reason">
                        				<td class="border px-1">{{ $t('words.reason') }}</td>
                                        <td class="border px-1">{{ in_block_list.reason }}</td>
                        			</tr>
                                    <tr>
                                        <td colspan="2">
                                            <button type="button" @click="deleteFromBlockList" class="mt-2 px-2 py-1 bg-white text-black rounded-sm">{{ $t('words.delete') }}</button>
                                        </td>
                                    </tr>
                        	</tbody>
                        </table>
                    </div>
                    </div>
                </div>

                <div class="col-span-6 items-center justify-end bg-gray-50 text-right flex gap-6">

                    <inertia-link
                        :href="route('back.trainees.private-notifications.create', trainee.id)"
                        class="items-center justify-start text-left float-left rounded-md px-4 py-2 bg-gray-200 hover:bg-gray-300 text-right"
                    >
                        {{ $t('words.private-message') }}
                    </inertia-link>

                    <a
                        v-if="trainee.user_id"
                        v-can="'can-impersonate'"
                        :href="route('impersonate', trainee.user_id)"
                        class="items-center justify-start text-left float-left rounded-md px-4 py-2 bg-gray-200 hover:bg-gray-300 text-right"
                    >
                        {{ $t('words.login-as-user') }}
                    </a>

                    <a
                        :href="route('back.trainees.admin.attendance-sheet.pdf', trainee.id)"
                        target="_blank"
                        class="items-center justify-start text-left float-left rounded-md px-4 py-2 bg-gray-200 hover:bg-gray-300 text-right"
                    >
                        {{ $t('words.attendance-sheet') }}
                    </a>

                    <button
                        v-if="!editButton.editOption"
                        @click="editTrainee"
                        class="items-center justify-end rounded-md px-4 py-2 bg-gray-200 hover:bg-gray-300 text-right"
                    >
                        {{ editButton.text }}
                    </button>
                    <button
                        v-else
                        @click="editTrainee"
                        :disabled="$wait.is('UPDATING_TRAINEE')"
                        :class="{'bg-green-200 cursor-wait': $wait.is('UPDATING_TRAINEE')}"
                        class=" items-center justify-end rounded-md px-4 py-2 bg-green-300 hover:bg-green-400 text-right"
                    >
                        <svg v-if="$wait.is('UPDATING_TRAINEE')" role="status" class="inline w-4 h-4 text-black animate-spin" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="#E5E7EB"/>
                            <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentColor"/>
                        </svg>
                        {{ editButton.text }}
                    </button>

                    <button
                        v-if="editButton.editOption"
                        @click="cancelEdit"
                        class=" items-center justify-end rounded-md px-4 py-2 bg-red-300 hover:bg-red-400 text-right"
                    >
                        {{ cancelButton.text }}
                    </button>

                    <template v-if="trainee.user">
                        <change-trainee-password :trainee="trainee" />

                        <button
                            v-if="!trainee.user.last_login_at"
                            @click="resendInvitation"
                            :class="{'btn-disabled': this.$wait.is('SENDING_INVITATION')}"
                            :disabled="this.$wait.is('SENDING_INVITATION')"
                            class="inline-flex items-center rounded-md px-4 py-2 bg-yellow-200 hover:bg-yellow-300 text-right"
                        >

                            <svg
                                v-if="this.$wait.is('SENDING_INVITATION')"
                                class="animate-spin ml-1 ml-3 h-3 w-3 text-dark"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                <circle
                                    class="opacity-25"
                                    cx="12"
                                    cy="12"
                                    r="10"
                                    stroke="currentColor"
                                    stroke-width="4"
                                ></circle>
                                <path
                                    class="opacity-75"
                                    fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                ></path>
                            </svg>

                            {{ $t('words.resend-invitation') }}
                        </button>
                    </template>

                    <button
                        v-if="!trainee.user_id"
                        @click="openTraineeAccount"
                        class=" items-center justify-end rounded-md px-4 py-2 bg-yellow-200 hover:bg-yellow-300 text-right"
                    >
                        {{ $t('words.open-an-account') }}
                    </button>

                    <button
                        v-if="trainee.is_pending_approval"
                        @click="approveTrainee"
                        class=" items-center justify-end rounded-md px-4 py-2 bg-yellow-200 hover:bg-yellow-300 text-right"
                    >
                        {{ $t('words.approve-trainee') }}
                    </button>

                    <button
                        v-can="'block-trainee'"
                        @click="blockTrainee"
                        class=" items-center justify-start text-left float-left rounded-md px-4 py-2 bg-red-300 hover:bg-red-400 text-right"
                    >
                        {{ $t('words.block-trainee') }}
                    </button>

                    <inertia-link
                        v-can="'block-trainee'"
                        :href="route('back.trainees.suspend.create', {trainee_id: trainee.id})"
                        class=" items-center justify-start text-left float-left rounded-md px-4 py-2 bg-red-300 hover:bg-red-400 text-right"
                    >
                        {{ $t('words.suspend') }}
                    </inertia-link>
                </div>

                <div class="col-span-6 items-center justify-end bg-gray-50 text-right flex gap-6" v-can="'view-gosi'">
                    <gosi-container :nin-or-iqama="trainee.identity_number"></gosi-container>
                </div>

                <div
                    v-if="!editButton.editOption"
                    class="col-span-6 sm:col-span-2"
                >
                    <jet-label
                        for="trainee_group_name"
                        :value="$t('words.group-name')"
                    />
                    <template v-if="trainee.trainee_group_id">
                        <jet-input
                            id="group-name"
                            type="text"
                            :class="editButton.inputClass"
                            v-model="trainee.trainee_group.name"
                            autocomplete="off"
                            :disabled="!editButton.editOption"
                        />
                    </template>
                    <template v-else>
                        <jet-input
                            id="group-name"
                            type="text"
                            :class="editButton.inputClass"
                            v-model="trainee.trainee_group"
                            autocomplete="off"
                            :disabled="!editButton.editOption"
                        />
                    </template>
                </div>
                <div
                    v-else
                    class="col-span-6 sm:col-span-2"
                >
                    <jet-label
                        for="trainee_group_name"
                        :value="$t('words.group-name')"
                    />
                    <select
                        :class="editButton.selectInputClass"
                        v-model="trainee.trainee_group"
                        id="trainee_group_id"
                        :disabled="!editButton.editOption"
                    >
                        <option value=""></option>
                        <option
                            v-for="group in trainee_groups"
                            :key="group.id"
                            :value="group"
                        >
                            {{ group.name }}
                        </option>
                    </select>
                </div>


                <div
                    v-if="!editButton.editOption"
                    class="col-span-6 sm:col-span-2"
                >
                    <jet-label
                        for="company_id"
                        :value="$t('words.company')"
                    />
                    <inertia-link v-if="trainee.company"
                                  class="block text-blue-600 hover:text-blue-800 border rounded-lg p-2 mt-1 bg-gray-100"
                                  :href="route('back.companies.show', trainee.company.id)">{{ trainee.company.name_ar }}</inertia-link>
                    <jet-input
                        v-else
                        id="company_id"
                        type="text"
                        :class="editButton.inputClass"
                        :value="trainee.company ? trainee.company.name_ar : ''"
                        autocomplete="off"
                        :disabled="!editButton.editOption"
                    />
                </div>
                <div
                    v-else
                    class="col-span-6 sm:col-span-2"
                >
                    <jet-label
                        for="company_id"
                        :value="$t('words.company')"
                    />
                    <select
                        :class="editButton.selectInputClass"
                        v-model="trainee.company_id"
                        ref="company_id_selector"
                        id="company_id_selector"
                        :disabled="!editButton.editOption"
                    >
                        <option value=""></option>
                        <option
                            v-for="company in companies"
                            :key="company.id"
                            :value="company.id"
                        >
                            {{ company.name_ar }}
                        </option>
                    </select>
                </div>

                <div class="col-span-6 sm:col-span-2">
                    <jet-label
                        for="name"
                        :value="$t('words.name')"
                    />
                    <jet-input
                        id="name"
                        type="text"
                        :class="editButton.inputClass"
                        v-model="trainee.name"
                        autocomplete="off"
                        :disabled="!editButton.editOption"
                    />
                </div>

                <div class="col-span-6 sm:col-span-2">
                    <jet-label
                        for="identity_number"
                        :value="$t('words.identity_number')"
                    />
                    <jet-input
                        id="identity_number"
                        type="text"
                        :class="editButton.inputClass"
                        v-model="trainee.identity_number"
                        :disabled="!editButton.editOption"
                    />
                </div>

                <div class="col-span-6 sm:col-span-2">
                    <jet-label
                        for="birthday"
                        :value="$t('words.birthday')"
                    />
                    <jet-input
                        id="birthday"
                        type="date"
                        :class="editButton.inputClass"
                        v-model="trainee.birthday"
                        :disabled="!editButton.editOption"
                    />
                </div>

                <div class="col-span-6 sm:col-span-2">
                    <label for="phone" class="block font-medium text-sm text-gray-700 flex justify-between">
                        <span>
                            <a class="mt-10" :href="trainee.whatsapp_link" target="_blank">
                                <span>{{ $t('words.phone') }}</span>
                                <ion-icon name="logo-whatsapp" class="w-4 h-4 text-green-600"></ion-icon>
                            </a>
                        </span>
                        <span class="border px-2 gap-2 text-sm rounded pull-right flex"
                              :class="trainee.phone_is_owned ? 'border-green-600 bg-green-300' : 'border-red-600 bg-red-300'"
                        >
                            <img src="/img/absher.svg" alt="Absher" width="9px">
                            {{ trainee.phone_ownership_status }}
                        </span>
                    </label>
                    <jet-input
                        id="phone"
                        type="text"
                        :class="editButton.inputClass"
                        v-model="trainee.phone"
                        placeholder="9665XXXXXXXX"
                        :disabled="!editButton.editOption"
                    />
                </div>

                <div class="col-span-6 sm:col-span-2">
                    <jet-label
                        for="phone_additional"
                        :value="$t('words.phone_additional')"
                    />
                    <jet-input
                        id="phone_additional"
                        type="text"
                        :class="editButton.inputClass"
                        v-model="trainee.phone_additional"
                        :disabled="!editButton.editOption"
                    />
                </div>

                <div class="col-span-6 sm:col-span-2">
                    <jet-label
                        for="national_address"
                        :value="$t('words.national-address')"
                    />
                    <jet-input
                        id="national_address"
                        type="text"
                        :class="editButton.inputClass"
                        v-model="trainee.national_address"
                        :disabled="!editButton.editOption"
                    />
                </div>

                <div class="col-span-6 sm:col-span-2">
                    <jet-label
                        for="email"
                        :value="$t('words.email')"
                    />
                    <jet-input
                        id="email"
                        type="text"
                        :class="editButton.inputClass"
                        v-model="trainee.email"
                        :disabled="!editButton.editOption"
                    />
                </div>

                <div
                    class="col-span-6 sm:col-span-2"
                    v-if="this.lang=='ar'"
                >
                    <jet-label
                        for="educational_level"
                        :value="$t('words.educational_level')"
                    />

                    <select
                        :class="editButton.selectInputClass"
                        v-model="trainee.educational_level_id"
                        id="educational_level_id"
                        :disabled="!editButton.editOption"
                    >
                        <option
                            v-for="educational_level in educational_levels"
                            :key="educational_level.id"
                            :value="educational_level.id"
                        >{{ educational_level.name_ar }}
                        </option>
                    </select>

                </div>

                <div
                    class="col-span-6 sm:col-span-2"
                    v-else
                >
                    <jet-label
                        for="educational_level"
                        :value="$t('words.educational_level')"
                    />

                    <select
                        :class="editButton.selectInputClass"
                        v-model="trainee.educational_level_id"
                        id="educational_level_id"
                        :disabled="!editButton.editOption"
                    >
                        <option
                            v-for="educational_level in educational_levels"
                            :key="educational_level.id"
                            :value="educational_level.id"
                        >{{ educational_level.name_en }}
                        </option>
                    </select>
                </div>

                <div class="col-span-6 sm:col-span-2">
                    <jet-label
                        for="city_id"
                        :value="$t('words.city')"
                    />

                    <select
                        :class="editButton.selectInputClass"
                        v-model="trainee.city_id"
                        id="city_id"
                        :disabled="!editButton.editOption"
                    >
                        <option
                            v-for="city in cities"
                            :key="city.id"
                            :value="city.id"
                        >{{ city.name_ar }}
                        </option>
                    </select>
                </div>

                <div
                    class="col-span-6 sm:col-span-1"
                    v-if="this.lang=='ar'"
                >
                    <jet-label
                        for="marital_status"
                        :value="$t('words.marital_status')"
                    />

                    <select
                        :class="editButton.selectInputClass"
                        v-model="trainee.marital_status_id"
                        id="city_id"
                        :disabled="!editButton.editOption"
                    >
                        <option
                            v-for="marital_status in marital_statuses"
                            :key="marital_status.id"
                            :value="marital_status.id"
                        >{{ marital_status.name_ar }}
                        </option>
                    </select>
                </div>

                <div
                    class="col-span-6 sm:col-span-1"
                    v-else
                >

                    <jet-label
                        for="marital_status"
                        :value="$t('words.marital_status')"
                    />

                    <select
                        :class="editButton.selectInputClass"
                        v-model="trainee.marital_status_id"
                        id="city_id"
                        :disabled="!editButton.editOption"
                    >
                        <option
                            v-for="marital_status in marital_statuses"
                            :key="marital_status.id"
                            :value="marital_status.id"
                        >{{ marital_status.name_en }}
                        </option>
                    </select>

                </div>

                <div class="col-span-6 sm:col-span-1">
                    <jet-label
                        for="children_count"
                        :value="$t('words.children_count')"
                    />
                    <jet-input
                        id="children_count"
                        type="text"
                        :class="editButton.inputClass"
                        v-model="trainee.children_count"
                        :disabled="!editButton.editOption"
                    />
                </div>

                <div class="col-span-6 sm:col-span-1">
                    <jet-label
                        for="last_login_at"
                        :value="$t('words.last-login-at')"
                    />
                    <jet-input
                        id="last_login_at"
                        type="text"
                        class="form-input rounded-md shadow-sm mt-1 block w-full bg-gray-200"
                        :value="trainee.user ? trainee.user.last_login_at_timezone : ''"
                        disabled
                    />
                </div>

                <div class="col-span-6 sm:col-span-1">
                    <jet-label
                        for="joining_date"
                        :value="$t('words.joining_date')"
                    />
                    <jet-input
                        id="joining_date"
                        type="text"
                        class="form-input rounded-md shadow-sm mt-1 block w-full bg-gray-200"
                        :value="trainee.user ? trainee.created_at_date : ''"
                        disabled
                    />
                </div>

                <div class="col-span-6 sm:col-span-2">
                    <jet-label
                        for="linked-date"
                        :value="$t('words.Insurance-registration-date')"
                    />
                    <jet-input
                        id="linked-date"
                        type="date"
                        :class="editButton.inputClass"
                        v-model="trainee.linked_date_formatted"
                        :disabled="!editButton.editOption"
                    />
                </div>

                <div class="col-span-6 sm:col-span-2">
                    <jet-label
                        for="bill-from-date"
                        :value="$t('words.bill-from-date')"
                    />
                    <jet-input
                        id="bill-from-date"
                        type="date"
                        :class="editButton.inputClass"
                        v-model="trainee.bill_from_date_formatted"
                        :disabled="!editButton.editOption"
                    />
                </div>

                <div class="col-span-6 sm:col-span-1">
                    <jet-label
                        for="name"
                        :value="$t('words.status')"
                    />
                    <p>
                        <span
                            v-if="trainee.is_pending_uploading_files"
                            class="text-sm inline-block mt-2 p-1 px-2 bg-blue-300 rounded-lg"
                        >
                            {{ $t('words.incomplete-application') }}
                        </span>

                        <span
                            v-if="trainee.is_pending_approval"
                            class="text-sm inline-block mt-2 p-1 px-2 bg-yellow-200 rounded-lg"
                        >
                            {{ $t('words.nominated-instructor') }}
                        </span>

                        <span
                            v-if="trainee.is_approved"
                            class="text-sm inline-block mt-2 p-1 px-2 bg-green-300 rounded-lg"
                        >
                            {{ $t('words.approved') }}
                        </span>
                    </p>
                </div>

                <div class="col-span-6 sm:col-span-1" v-can="'override-training-costs'">
                    <jet-label for="name" :value="$t('words.fixed-training-costs')"/>
                    <inertia-link :href="route('back.trainees.fixed-training-costs', trainee.id)">
                        <span v-if="trainee.override_training_costs !== null"
                              class="text-sm inline-block mt-2 p-1 px-2 bg-gray-200 rounded-lg bg-red-600 text-white">
                            {{ trainee.override_training_costs }} ر.س.
                        </span>
                        <span v-else class="text-sm inline-block mt-2 p-1 px-2 bg-gray-200 rounded-lg">
                            {{ $t('words.not-set') }}
                        </span>
                    </inertia-link>
                </div>
            </div>


            <jet-section-border></jet-section-border>

            <div class="grid grid-cols-1 md:grid-cols-6 gap-6 my-2">
                <div class="md:col-span-4 lg:col-span-1 sm:col-span-3">
                    <div class="px-4 sm:px-0">
                        <h3 class="text-lg font-medium text-gray-900">
                            {{ $t('words.documents') }}
                        </h3>

                        <p class="mt-1 text-sm text-gray-600">
                            {{ $t('words.documents-help') }}
                        </p>

                        <div class="mt-3">
                            <inertia-link class="text-blue-600 border-2 border-blue-600 text-xs p-1" :href="route('back.trainees.files.index', trainee.id)">
                                {{ $t('words.other-files') }} ({{ trainee.general_files_count }})
                            </inertia-link>
                        </div>
                    </div>
                </div>

                <div class="md:col-span-3 lg:col-span-1 sm:col-span-3">
                    <jet-label
                        :value="$t('words.identity-card-photocopy')"
                        class="mb-2"
                    />

                    <div
                        class="bg-white border-2 rounder-lg flex flex-col justify-center items-center min-container-upload"
                        v-if="trainee.identity_copy_url"
                    >
                        <a
                            class="bg-gray-700 text-white font-semibold p-2 text-center w-1/2 rounded my-1"
                            target="_blank"
                            :href="trainee.identity_copy_url"
                        >{{ $t('words.download') }}</a>
                        <button
                            class="bg-red-500 text-white font-semibold p-2 text-center w-1/2 rounded my-1"
                            @click="deleteIdentity"
                        >{{ $t('words.delete') }}
                        </button>
                    </div>
                    <vue-dropzone
                        v-else
                        id="dropzoneIdentity"
                        @vdropzone-sending="sendingCsrf"
                        :options="dropzoneOptionsIdentity"
                    ></vue-dropzone>
                </div>

                <div class="md:col-span-3 lg:col-span-1 sm:col-span-3">
                    <jet-label
                        :value="$t('words.qualification-photocopy')"
                        class="mb-2"
                    />

                    <div
                        class="bg-white border-2 rounder-lg flex flex-col justify-center items-center min-container-upload"
                        v-if="trainee.qualification_copy_url"
                    >
                        <a
                            class="bg-gray-700 text-white font-semibold p-2 text-center w-1/2 rounded my-1"
                            target="_blank"
                            :href="trainee.qualification_copy_url"
                        >{{ $t('words.download') }}</a>
                        <button
                            class="bg-red-500 text-white font-semibold p-2 text-center w-1/2 rounded my-1"
                            @click="deleteQualification"
                        >{{ $t('words.delete') }}
                        </button>
                    </div>
                    <vue-dropzone
                        v-else
                        id="dropzoneQualification"
                        @vdropzone-sending="sendingCsrf"
                        :options="dropzoneOptionsQualification"
                    ></vue-dropzone>
                </div>

                <div class="md:col-span-3 lg:col-span-1 sm:col-span-3">
                    <jet-label
                        :value="$t('words.bank-account-photocopy')"
                        class="mb-2"
                    />

                    <div
                        class="bg-white border-2 rounder-lg flex flex-col justify-center items-center min-container-upload"
                        v-if="trainee.bank_account_copy_url"
                    >
                        <a
                            class="bg-gray-700 text-white font-semibold p-2 text-center w-1/2 rounded my-1"
                            target="_blank"
                            :href="trainee.bank_account_copy_url"
                        >{{ $t('words.download') }}</a>
                        <button
                            class="bg-red-500 text-white font-semibold p-2 text-center w-1/2 rounded my-1"
                            @click="deleteBankAccount"
                        >{{ $t('words.delete') }}
                        </button>
                    </div>
                    <vue-dropzone
                        v-else
                        id="dropzoneBankAccount"
                        @vdropzone-sending="sendingCsrf"
                        :options="dropzoneOptionsBankAccount"
                    ></vue-dropzone>
                </div>

                <div class="md:col-span-3 lg:col-span-1 sm:col-span-3">
                    <jet-label
                        :value="$t('words.national-address-copy')"
                        class="mb-2"
                    />

                    <div
                        class="bg-white border-2 rounder-lg flex flex-col justify-center items-center min-container-upload"
                        v-if="trainee.national_address_copy_url"
                    >
                        <a
                            class="bg-gray-700 text-white font-semibold p-2 text-center w-1/2 rounded my-1"
                            target="_blank"
                            :href="trainee.national_address_copy_url"
                        >{{ $t('words.download') }}</a>
                        <button
                            class="bg-red-500 text-white font-semibold p-2 text-center w-1/2 rounded my-1"
                            @click="deleteNationalAddress"
                        >{{ $t('words.delete') }}
                        </button>
                    </div>
                    <vue-dropzone
                        v-else
                        id="dropzoneNationalAddress"
                        @vdropzone-sending="sendingCsrf"
                        :options="dropzoneOptionsNationalAddress"
                    ></vue-dropzone>
                </div>

                <div class="md:col-span-3 lg:col-span-1 sm:col-span-3">
                    <jet-label
                        :value="$t('words.cv')"
                        class="mb-2"
                    />

                    <div
                        class="bg-white border-2 rounder-lg flex flex-col justify-center items-center min-container-upload"
                        v-if="trainee.cv_url"
                    >
                        <a
                            class="bg-gray-700 text-white font-semibold p-2 text-center w-1/2 rounded my-1"
                            target="_blank"
                            :href="trainee.cv_url"
                        >{{ $t('words.download') }}</a>
                        <button
                            class="bg-red-500 text-white font-semibold p-2 text-center w-1/2 rounded my-1"
                            @click="deleteCv"
                        >{{ $t('words.delete') }}
                        </button>
                    </div>
                    <vue-dropzone
                        v-else
                        id="dropzoneCv"
                        @vdropzone-sending="sendingCsrf"
                        :options="dropzoneOptionsCv"
                    ></vue-dropzone>
                </div>
            </div>

            <jet-section-border></jet-section-border>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 my-2">
                <div class="md:col-span-4 lg:col-span-1 sm:col-span-3">
                    <div class="px-4 sm:px-0">
                        <h3 class="text-lg font-medium text-gray-900">
                            {{ $t('words.warnings-sheet') }}
                        </h3>
                    </div>
                </div>

                <div class="md:col-span-3 lg:col-span-1 sm:col-span-3">
                    <attendance-sheet-management-for-trainee :trainee_id="trainee.id">
                    </attendance-sheet-management-for-trainee>
                </div>
            </div>

            <jet-section-border></jet-section-border>

            <div
                v-can="'issue-monthly-invoices'"
                class="grid grid-cols-1 md:grid-cols-2 gap-6 my-2"
            >
                <div class="md:col-span-4 lg:col-span-1 sm:col-span-3">
                    <div class="px-4 sm:px-0">
                        <h3 class="text-lg font-medium text-gray-900">
                            {{ $t('words.invoices') }}
                        </h3>

                        <p class="mt-1 text-sm text-gray-600">
                            {{ $t('words.invoices-help') }}
                        </p>
                    </div>
                </div>

                <div class="md:col-span-3 lg:col-span-1 sm:col-span-3">
                    <div class="flex w-full justify-end">
                        <inertia-link :href="route('back.trainees.invoices.create', trainee.id)">
                            <jet-button type="button">
                                {{ $t('words.issue-invoice') }}
                            </jet-button>
                        </inertia-link>
                    </div>

                    <table class="w-full whitespace-no-wrap bg-white rounded-lg my-5 p-5 shadow text-sm">
                        <tr class="text-left font-bold text-center">
                            <th class="px-6 pt-6 pb-4 text-left">{{ $t('words.invoice-no') }}</th>
                            <th class="px-6 pt-6 pb-4">{{ $t('words.amount') }}</th>
                            <th class="px-6 pt-6 pb-4">{{ $t('words.status') }}</th>
                        </tr>
                        <tr
                            v-for="invoice in trainee.invoices"
                            :key="invoice.id"
                            class="border-t hover:bg-gray-100 focus-within:bg-gray-100 text-center"
                        >
                            <td class="px-4 py-4 text-left text-blue-500">
                                <inertia-link :href="route('back.finance.invoices.show', invoice.id)">
                                    {{ invoice.number_formatted }}
                                </inertia-link>
                            </td>

                            <td class="px-4 py-4">
                                {{ invoice.grand_total }}
                            </td>

                            <td class="px-4 py-4">
                                {{ invoice.status_formatted }}
                            </td>
                        </tr>

                        <tr v-if="trainee.invoices.length === 0">
                            <td
                                class="border-t px-4 py-4"
                                colspan="4"
                            >
                                <empty-slate />
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <jet-section-border></jet-section-border>

            <trainee-audit-container :trainee_id="trainee.id"></trainee-audit-container>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import JetSectionBorder from '@/Jetstream/SectionBorder'
import Breadcrumb from "@/Components/Breadcrumb";
import JetDialogModal from '@/Jetstream/DialogModal'
import JetInput from '@/Jetstream/Input'
import JetInputError from '@/Jetstream/InputError'
import JetActionMessage from '@/Jetstream/ActionMessage';
import JetButton from '@/Jetstream/Button';
import JetFormSection from '@/Jetstream/FormSection';
import JetLabel from '@/Jetstream/Label';
import CompanyContractsPagination from "@/Components/CompanyContractsPagination";
import BreadcrumbContainer from "@/Components/BreadcrumbContainer";
import VueDropzone from 'vue2-dropzone'
import 'vue2-dropzone/dist/vue2Dropzone.min.css'
import SelectTraineeGroup from "@/Components/SelectTraineeGroup";
import ChangeTraineePassword from '@/Components/ChangeTraineePassword';
import AttendanceSheetManagementForTrainee from "@/Components/AttendanceSheetManagementForTrainee";
import 'selectize/dist/js/standalone/selectize.min';
import EmptySlate from "@/Components/EmptySlate";
import TraineeAuditContainer from "@/Components/TraineeAuditContainer";
import {Inertia} from "@inertiajs/inertia";
import ValidationErrors from "@/Components/ValidationErrors";
import GosiContainer from "../../../Components/GosiContainer";

export default {
    props: [
        'sessions',
        'in_block_list',
        'trainee',
        'cities',
        'marital_statuses',
        'educational_levels',
        'trainee_groups',
        'trainee_group_trainees',
        'companies',
    ],
    components: {
        GosiContainer,
        ValidationErrors,
        TraineeAuditContainer,
        AppLayout,
        AttendanceSheetManagementForTrainee,
        Breadcrumb,
        BreadcrumbContainer,
        ChangeTraineePassword,
        CompanyContractsPagination,
        JetActionMessage,
        JetButton,
        JetDialogModal,
        JetFormSection,
        JetInput,
        JetInputError,
        JetLabel,
        JetSectionBorder,
        SelectTraineeGroup,
        VueDropzone,
        EmptySlate,
    },
    data() {
        return {
            new_trainee_group: {
                name: '',
                id: '',
            },
            cancelButton: {
                text: this.$t('words.cancel'),
            },
            validationErrors: null,
            lang: (this.$t('words.edit') == "Edit") ? 'en' : 'ar',
            editButton: {
                text: this.$t('words.edit'),
                editOption: false,
                inputClass: "mt-1 block w-full bg-gray-200",
                selectInputClass: "mt-1 block w-full border border-gray-200 bg-gray-200 py-2.5 px-4 pr-8 rounded leading-tight focus:outline-none"
            },
            dropzoneOptionsIdentity: {
                destroyDropzone: false,
                url: route('back.trainees.attachments.identity', {trainee_id: this.trainee.id}),
                dictDefaultMessage: "<ion-icon name='cloud-upload-outline' class='text-red-500' size='large'></ion-icon><br/> " + this.$t('words.upload-files-here'),
                thumbnailWidth: 150,
                maxFilesize: 20,
            },
            dropzoneOptionsQualification: {
                destroyDropzone: false,
                url: route('back.trainees.attachments.qualification', {trainee_id: this.trainee.id}),
                dictDefaultMessage: "<ion-icon name='cloud-upload-outline' class='text-red-500' size='large'></ion-icon><br/> " + this.$t('words.upload-files-here'),
                thumbnailWidth: 150,
                maxFilesize: 20,
            },
            dropzoneOptionsBankAccount: {
                destroyDropzone: false,
                url: route('back.trainees.attachments.bank-account', {trainee_id: this.trainee.id}),
                dictDefaultMessage: "<ion-icon name='cloud-upload-outline' class='text-red-500' size='large'></ion-icon><br/> " + this.$t('words.upload-files-here'),
                thumbnailWidth: 150,
                maxFilesize: 20,
            },
            dropzoneOptionsNationalAddress: {
                destroyDropzone: false,
                url: route('back.trainees.attachments.national-address', {trainee_id: this.trainee.id}),
                dictDefaultMessage: "<ion-icon name='cloud-upload-outline' class='text-red-500' size='large'></ion-icon><br/> " + this.$t('words.upload-files-here'),
                thumbnailWidth: 150,
                maxFilesize: 20,
            },
            dropzoneOptionsCv: {
                destroyDropzone: false,
                url: route('back.trainees.attachments.cv', {trainee_id: this.trainee.id}),
                dictDefaultMessage: "<ion-icon name='cloud-upload-outline' class='text-red-500' size='large'></ion-icon><br/> " + this.$t('words.upload-files-here'),
                thumbnailWidth: 150,
                maxFilesize: 20,
            },
            companySelector: null,
        }
    },
    mounted() {
        if (this.trainee.trainee_group) {
            this.trainee.trainee_group_object = this.trainee_group;
        } else {
            this.trainee.trainee_group_object = this.new_trainee_group;
        }
    },
    methods: {
        deleteFromBlockList() {
            this.$inertia.delete(route('back.trainees.delete-from-block-list', {id: this.trainee.id}));
        },
        selectGroupName(input) {
            this.trainee.trainee_group = input;
        },
        blockTrainee() {
            this.$inertia.get(route('back.trainees.block', {trainee_id: this.trainee.id}));
        },
        cancelEdit() {
            this.editButton.editOption = false;
            this.editButton.inputClass = 'mt-1 block w-full bg-gray-200';
            this.editButton.selectInputClass = 'mt-1 block w-full border border-gray-200 bg-gray-200 py-2.5 px-4 pr-8 rounded leading-tight focus:outline-none';
            this.editButton.text = this.$t('words.edit');
            $(document).ready(function () {
                $('#company_id_selector').selectize()[0].selectize.destroy();
            });
            window.location.reload();
        },
        editTrainee() {
            if (!this.editButton.editOption) {
                this.editButton.editOption = true;
                this.editButton.inputClass = 'mt-1 block w-full bg-white';
                this.editButton.selectInputClass = "mt-1 block w-full border border-gray-200 bg-white py-2.5 px-4 pr-8 rounded leading-tight focus:outline-none"
                this.editButton.text = this.$t('words.save');


                let vm = this;
                $(document).ready(function () {
                    vm.companySelector = $('#company_id_selector').selectize({
                        sortField: 'text',
                        onChange: function (value) {
                            vm.trainee.company_id = value;
                            vm.trainee.company = null; // For better UX?
                        }
                    })
                });

            } else {
                $(document).ready(function () {
                    $('#company_id_selector').selectize()[0].selectize.destroy();
                });
                let newForm = {
                    trainee_group_name: this.trainee.trainee_group ? this.trainee.trainee_group.name : '',
                    company_id: this.trainee.company_id,
                    name: this.trainee.name,
                    email: this.trainee.email,
                    identity_number: this.trainee.identity_number,
                    birthday: this.trainee.birthday,
                    phone: this.trainee.phone,
                    phone_additional: this.trainee.phone_additional,
                    national_address: this.trainee.national_address,
                    educational_level: this.trainee.educational_level_id,
                    city_id: this.trainee.city_id,
                    marital_status_id: this.trainee.marital_status_id,
                    children_count: this.trainee.children_count,
                    bill_from_date: this.trainee.bill_from_date_formatted,
                    linked_date: this.trainee.linked_date_formatted,
                };

                this.$wait.start('UPDATING_TRAINEE');
                this.validationErrors = null;
                axios.put(route('back.trainees.update', this.trainee.id), newForm).then(response => {
                    Inertia.reload().then(() => {
                        this.editButton.editOption = false;
                        this.editButton.inputClass = 'mt-1 block w-full bg-gray-200';
                        this.editButton.selectInputClass = 'mt-1 block w-full border border-gray-200 bg-gray-200 py-2.5 px-4 pr-8 rounded leading-tight focus:outline-none';
                        this.editButton.text = this.$t('words.edit');
                        this.$wait.end('UPDATING_TRAINEE');
                    });
                }).catch(error => {
                    this.$wait.end('UPDATING_TRAINEE');
                    if (error.response.status == 422) {
                        this.validationErrors = error.response.data.errors;
                    }
                })
                // this.$inertia.put(route('back.trainees.update', this.trainee.id), newForm).then(response => {
                //     this.editButton.editOption = false;
                //     this.editButton.inputClass = 'mt-1 block w-full bg-gray-200';
                //     this.editButton.selectInputClass = 'mt-1 block w-full border border-gray-200 bg-gray-200 py-2.5 px-4 pr-8 rounded leading-tight focus:outline-none';
                //     this.editButton.text = this.$t('words.edit');
                // });
            }
        },
        approveTrainee() {
            if (confirm(this.$t('words.are-you-sure'))) {
                this.$inertia.post(route('back.trainees.approve-user', {trainee_id: this.trainee.id})).then(response => {
                    this.$inertia.get(route('back.trainees.show', this.trainee.id))
                });
            }
        },
        sendingCsrf(file, xhr, formData) {
            xhr.setRequestHeader('X-CSRF-TOKEN', window.token ? window.token.content : '');
        },
        deleteIdentity() {
            if (confirm(this.$t('words.are-you-sure'))) {
                this.$inertia.delete(route('back.trainees.attachments.identity.destroy', {trainee_id: this.trainee.id}));
            }
        },
        deleteQualification() {
            if (confirm(this.$t('words.are-you-sure'))) {
                this.$inertia.delete(route('back.trainees.attachments.qualification.destroy', {trainee_id: this.trainee.id}));
            }
        },
        deleteBankAccount() {
            if (confirm(this.$t('words.are-you-sure'))) {
                this.$inertia.delete(route('back.trainees.attachments.bank-account.destroy', {trainee_id: this.trainee.id}));
            }
        },
        deleteNationalAddress() {
            if (confirm(this.$t('words.are-you-sure'))) {
                this.$inertia.delete(route('back.trainees.attachments.national-address.destroy', {trainee_id: this.trainee.id}));
            }
        },
        deleteCv() {
            if (confirm(this.$t('words.are-you-sure'))) {
                this.$inertia.delete(route('back.trainees.attachments.cv.destroy', {trainee_id: this.trainee.id}));
            }
        },
        openTraineeAccount() {
            if (confirm(this.$t('words.are-you-sure'))) {
                this.$inertia.post(route('back.trainees.create-user', {trainee_id: this.trainee.id}));
            }
        },
        resendInvitation() {
            this.$wait.start('SENDING_INVITATION');
            this.$inertia.post(route('back.trainees.re-send-invitation', {trainee_id: this.trainee.id})).then(response => {
                alert(this.$t('words.done-successfully'));
            }).finally(error => {
                this.$wait.end('SENDING_INVITATION');
            });
        }
    }
}
</script>

<style>
.min-container-upload {
    min-height: 168px;
}
</style>
