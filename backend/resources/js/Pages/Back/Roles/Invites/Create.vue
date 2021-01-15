<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'settings', link: route('back.settings')},
                    {title: 'roles', link: route('back.settings.roles.index')},
                    {title_raw: role.display_name},
                    {title_raw: $t('words.invite')},
                ]"
            ></breadcrumb-container>


            <div class="mt-4">
                <jet-form-section @submitted="inviteUser">
                    <template #title>
                        {{ $t('words.invite') }}
                    </template>

                    <template #description>

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
                            <jet-label for="email" :value="$t('words.email')" />
                            <jet-input id="email" type="email" class="mt-1 block w-full" v-model="form.email" autocomplete="off" />
                            <jet-input-error :message="form.error('email')" class="mt-2" />
                        </div>

                        <div class="col-span-2 sm:col-span-2">
                            <jet-label for="password" :value="$t('password')" />
                            <jet-input id="password" type="password" class="mt-1 block w-full" v-model="form.password" autocomplete="off" />
                            <jet-input-error :message="form.error('password')" class="mt-2" />
                        </div>
                    </template>

                    <template #actions>
                        <jet-action-message :on="form.recentlySuccessful" class="mr-3">
                            {{ $t('words.saved-successfully') }}
                        </jet-action-message>

                        <inertia-link :href="route('back.instructors.index')" class="flex items-center justify-start rtl:ml-4 ltr:mr-4 rounded-md px-4 py-2 bg-white hover:bg-gray-300 text-right">
                            {{ $t('words.cancel') }}
                        </inertia-link>

                        <jet-button :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            {{ $t('words.invite') }}
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
    props: ['role'],

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
            form: this.$inertia.form({
                name: '',
                phone: '',
                email: '',
                password: '',
            }, {
                bag: 'inviteUser',
            })
        }
    },
    methods: {
        inviteUser() {
            this.form.post(route('back.settings.roles.users.invite', this.role.id), {
                preserveScroll: true
            });
        },
    }
}
</script>
