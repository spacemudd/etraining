<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'trainees', link: route('back.trainees.index')},
                    {title_raw: trainee.name, link: route('back.trainees.show', trainee.id)},
                    {title: 'issue-new-certificate'},
                ]"
            ></breadcrumb-container>

            <div class="mt-4">
                <jet-form-section @submitted="createCertificate">
                    <template #title>
                        {{ $t('words.issue-new-certificate') }}
                    </template>

                    <template #description>
                        {{ $t('words.issue-new-certificate') }}
                    </template>

                    <template #form>
                        <div class="col-span-4 sm:col-span-4">
                            <jet-label for="title" :value="$t('words.certificate-title')" />
                            <jet-input 
                                id="title" 
                                type="text" 
                                class="mt-1 block w-full" 
                                v-model="form.title" 
                                autocomplete="off" 
                                autofocus 
                            />
                            <jet-input-error :message="form.error('title')" class="mt-2" />
                        </div>

                        <div class="col-span-4 sm:col-span-4">
                            <jet-label for="issued_at" :value="$t('words.issue-date')" />
                            <jet-input 
                                id="issued_at" 
                                type="date" 
                                class="mt-1 block w-full" 
                                v-model="form.issued_at" 
                                autocomplete="off" 
                            />
                            <jet-input-error :message="form.error('issued_at')" class="mt-2" />
                        </div>
                    </template>

                    <template #actions>
                        <jet-action-message :on="form.recentlySuccessful" class="mr-3">
                            {{ $t('words.created-successfully') }}
                        </jet-action-message>

                        <inertia-link 
                            :href="route('back.trainees.show', trainee.id)"
                            class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring focus:ring-gray-300 disabled:opacity-25 transition mr-2"
                        >
                            {{ $t('words.cancel') }}
                        </inertia-link>

                        <jet-button 
                            :class="{ 'opacity-25': form.processing }" 
                            :disabled="form.processing"
                        >
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
import JetInput from '@/Jetstream/Input'
import JetInputError from '@/Jetstream/InputError'
import JetActionMessage from '@/Jetstream/ActionMessage';
import JetButton from '@/Jetstream/Button';
import JetFormSection from '@/Jetstream/FormSection';
import JetLabel from '@/Jetstream/Label';
import BreadcrumbContainer from "@/Components/BreadcrumbContainer";

export default {
    props: [
        'trainee',
    ],

    components: {
        AppLayout,
        JetSectionBorder,
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
                title: '',
                issued_at: new Date().toISOString().split('T')[0], // Today's date
            }, {
                bag: 'createCertificate',
            })
        }
    },

    methods: {
        createCertificate() {
            this.form.post(route('back.trainees.custom-certificates.store', this.trainee.id), {
                onSuccess: () => {
                    // Redirect to trainee show page on success
                    this.$inertia.visit(route('back.trainees.show', this.trainee.id));
                }
            });
        },
    },
}
</script> 