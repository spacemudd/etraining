<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'trainees', link: route('back.trainees.index')},
                    {title: 'new'},
                ]"
            ></breadcrumb-container>

            <div class="mt-4">
                <jet-form-section @submitted="createTrainee">
                    <template #title>
                        {{ $t('words.open-new-trainee-file') }}
                    </template>

                    <template #description>
                        {{ $t('words.open-new-trainee-file-description') }}
                    </template>

                    <template #form>

                        <div class="col-span-4 sm:col-span-4">
                            <jet-label for="trainee_group_name" :value="$t('words.group-name')" />
                            <select-trainee-group class="mt-2"
                                                  @input="selectGroupName"
                                                  v-model="form.trainee_group_name"
                                                  :required="true"
                            />
                            <jet-input-error :message="form.error('trainee_group_name')" class="mt-2" />
                        </div>

                        <div class="col-span-2 sm:col-span-2">
                            <jet-label for="name" :value="$t('words.name')" />
                            <jet-input id="name" type="text" class="mt-1 block w-full" v-model="form.name" autocomplete="off" autofocus />
                            <jet-input-error :message="form.error('name')" class="mt-2" />
                        </div>

                        <div class="col-span-2 sm:col-span-2">
                            <jet-label for="identity_number" :value="$t('words.identity_number')" />
                            <jet-input id="identity_number" type="text" class="mt-1 block w-full" v-model="form.identity_number" autocomplete="off" />
                            <jet-input-error :message="form.error('identity_number')" class="mt-2" />
                        </div>

                        <div class="col-span-2 sm:col-span-2">
                            <jet-label for="birthday" :value="$t('words.birthday')" />
                            <jet-input id="birthday" type="date" class="mt-1 block w-full" v-model="form.birthday" autocomplete="off" />
                            <jet-input-error :message="form.error('birthday')" class="mt-2" />
                        </div>

                        <div class="col-span-2 sm:col-span-2">
                            <jet-label for="phone" :value="$t('words.phone')" />
                            <jet-input id="phone" type="text" class="mt-1 block w-full" v-model="form.phone" placeholder="9665XXXXXXXX" autocomplete="off" />
                            <jet-input-error :message="form.error('phone')" class="mt-2" />
                        </div>

                        <div class="col-span-2 sm:col-span-2">
                            <jet-label for="phone_additional" :value="$t('words.phone_additional')" />
                            <jet-input id="phone_additional" type="text" class="mt-1 block w-full" v-model="form.phone_additional" placeholder="9665XXXXXXXX" autocomplete="off" />
                            <jet-input-error :message="form.error('phone_additional')" class="mt-2" />
                        </div>

                        <div class="col-span-2 sm:col-span-2">
                            <jet-label for="educational_level" :value="$t('words.educational_level')" />
                            <div class="relative">
                                <select class="block appearance-none w-full border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                        v-model="form.educational_level_id"
                                        id="educational_level_id">
                                    <option v-for="level in educational_levels" :key="level.id" :value="level.id">{{ level.name_ar }}</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                                </div>
                            </div>
                            <jet-input-error :message="form.error('educational_level_id')" class="mt-2" />
                        </div>

                        <div class="col-span-4 sm:col-span-4">
                            <jet-label for="city_id" :value="$t('words.city')" />

                            <div class="relative">
                                <select class="block appearance-none w-full border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                        v-model="form.city_id"
                                        id="city_id">
                                    <option v-for="city in cities" :key="city.id" :value="city.id">{{ city.name_ar }}</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                                </div>
                            </div>

                            <jet-input-error :message="form.error('city_id')" class="mt-2" />
                        </div>

                        <div class="col-span-2 sm:col-span-2">
                            <jet-label for="marital_status" :value="$t('words.marital_status')" />
                            <div class="relative">
                                <select class="block appearance-none w-full border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                        v-model="form.marital_status_id"
                                        id="marital_status_id">
                                    <option v-for="status in marital_statuses" :key="status.id" :value="status.id">{{ status.name_ar }}</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                                </div>
                            </div>
                            <jet-input-error :message="form.error('marital_status_id')" class="mt-2" />
                        </div>

                        <div class="col-span-2 sm:col-span-2" v-if="needsToKnowChildrenCount">
                            <jet-label for="children_count" :value="$t('words.children_count')" />
                            <jet-input id="children_count" type="text" class="mt-1 block w-full" v-model="form.children_count" autocomplete="off" />
                            <jet-input-error :message="form.error('children_count')" class="mt-2" />
                        </div>

                        <!--<jet-input class="col-span-4 sm:col-span-4" type="text" v-model="addressSearch" @input="findAddress"/>-->
                    </template>

                    <template #actions>
                        <jet-action-message :on="form.recentlySuccessful" class="mr-3">
                            {{ $t('words.saved-successfully') }}
                        </jet-action-message>

                        <inertia-link href="/back/companies" class="flex items-center justify-start rtl:ml-4 ltr:mr-4 rounded-md px-4 py-2 bg-white hover:bg-gray-300 text-right">
                            {{ $t('words.cancel') }}
                        </inertia-link>

                        <jet-button :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            {{ $t('words.save') }}
                        </jet-button>
                    </template>
                </jet-form-section>
            </div>
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout'
    import JetSectionBorder from '@/Jetstream/SectionBorder'
    import JetDialogModal from '@/Jetstream/DialogModal'
    import JetInput from '@/Jetstream/Input'
    import JetInputError from '@/Jetstream/InputError'
    import JetActionMessage from '@/Jetstream/ActionMessage';
    import JetButton from '@/Jetstream/Button';
    import JetFormSection from '@/Jetstream/FormSection';
    import JetLabel from '@/Jetstream/Label';
    import BreadcrumbContainer from "@/Components/BreadcrumbContainer";
    import debounce from 'lodash/debounce'
    import SelectTraineeGroup from "@/Components/SelectTraineeGroup";

    export default {
        props: ['sessions', 'cities', 'marital_statuses', 'educational_levels', 'trainee_groups'],

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
            SelectTraineeGroup,
        },
        data() {
            return {
                addressSearch: '',
                form: this.$inertia.form({
                    trainee_group_name: '',
                    name: '',
                    identity_number: '',
                    birthday: '',
                    phone: '',
                    phone_additional: '',
                    educational_level_id: '',
                    city_id: '',
                    marital_status_id: '',
                    children_count: '',
                }, {
                    bag: 'createTrainee',
                })
            }
        },
        computed: {
           needsToKnowChildrenCount() {
               let selectedId = this.form.marital_status_id;

               return this.marital_statuses.some(function(marital_status) {
                   return marital_status.name_en != 'Single' && marital_status.id === selectedId;
               });
           },
        },
        methods: {
            selectGroupName(input) {
                this.form.trainee_group_name = input.name;
            },
            createTrainee() {
                this.form.post('/back/trainees', {
                    preserveScroll: true
                });
            },
            findAddress: debounce(function () {
                axios.get('/api/location-lookup?search='+this.addressSearch)
            }, 200),
        }
    }
</script>
