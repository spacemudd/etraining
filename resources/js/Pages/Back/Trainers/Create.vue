<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'trainers', link: route('back.trainers.index')},
                    {title: 'new'},
                ]"
            ></breadcrumb-container>


            <div class="mt-4">
                <jet-form-section @submitted="createTrainee">
                    <template #title>
                        {{ $t('words.open-new-trainer-file') }}
                    </template>

                    <template #description>
                        {{ $t('words.open-new-trainer-file-description') }}
                    </template>

                    <template #form>
                        <div class="col-span-2 sm:col-span-2">
                            <jet-label for="name" :value="$t('words.name')" />
                            <jet-input id="name" type="text" class="mt-1 block w-full" v-model="form.name" autocomplete="off" autofocus />
                            <jet-input-error :message="form.error('name')" class="mt-2" />
                        </div>

                        <div class="col-span-2 sm:col-span-2">
                            <jet-label for="phone" :value="$t('words.phone')" />
                            <jet-input id="phone" type="text" class="mt-1 block w-full" v-model="form.phone" placeholder="966XXXXXXX" autocomplete="off" autofocus />
                            <jet-input-error :message="form.error('phone')" class="mt-2" />
                        </div>

                        <div class="col-span-2 sm:col-span-2">
                            <jet-label for="identity_number" :value="$t('words.identity_number')" />
                            <jet-input id="identity_number" type="text" class="mt-1 block w-full" v-model="form.identity_number" autocomplete="off" />
                            <jet-input-error :message="form.error('identity_number')" class="mt-2" />
                        </div>

                        <div class="col-span-2 sm:col-span-2">
                            <jet-label for="email" :value="$t('words.email')" />
                            <jet-input id="email" type="email" class="mt-1 block w-full" v-model="form.email" autocomplete="off" />
                            <jet-input-error :message="form.error('email')" class="mt-2" />
                        </div>

                        <div class="col-span-2 sm:col-span-2">
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
                            <jet-label for="twitter_link" :value="$t('words.twitter_link')" />
                            <jet-input id="twitter_link" type="text" class="mt-1 block w-full" v-model="form.twitter_link" autocomplete="off" />
                            <jet-input-error :message="form.error('twitter_link')" class="mt-2" />
                        </div>
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

    export default {
        props: ['sessions', 'cities'],

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
                addressSearch: '',
                form: this.$inertia.form({
                    name: '',
                    identity_number: '',
                    phone: '',
                    email: '',
                    city_id: '',
                    twitter_link: '',
                }, {
                    bag: 'createTrainee',
                })
            }
        },
        methods: {
            createTrainee() {
                this.form.post('/back/trainers', {
                    preserveScroll: true
                });
            },
        }
    }
</script>
