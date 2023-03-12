<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'companies', link: route('back.companies.index')},
                    {title_raw: company.name_ar, link: route('back.companies.show', company.id)},
                    {title: 'send-sms-to-all'},
                ]"
            ></breadcrumb-container>

            <div class="grid grid-cols-12">
                <div class="col-span-6">
                    <p>{{ $t('words.sms-body') }}</p>
                    <form @submit.prevent="sendForm">
                        <jet-textarea id="notes" type="textarea" class="mt-1 block w-full" v-model="form.body" max="320" />
                        <jet-button :class="{ 'opacity-25': $wait.is('SAVING_CONTRACT') }"
                                    class="mt-5"
                                    :disabled="$wait.is('SAVING_CONTRACT')">
                            {{ $t('words.send') }}
                        </jet-button>
                    </form>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import Table from '@/Components/Tailwind2/Table';
import Pagination from '@/Shared/Pagination'

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
import JetTextarea from '@/Jetstream/Textarea'
import CompanyContractsPagination from "@/Components/CompanyContractsPagination";
import BreadcrumbContainer from "@/Components/BreadcrumbContainer";
import EmptySlate from "@/Components/EmptySlate";

export default {
    props: [
        'company',
    ],
    components: {
        Table,
        Pagination,
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
        JetTextarea,
    },
    data() {
        return {
            form: this.$inertia.form({
                    body: '',
                }, {
                    bag: 'sendNotification',
                })
        }
    },
    mounted() {
    },
    methods: {
        sendForm() {
            this.form.post(route('back.companies.trainees.notification.send', {company_id: this.company.id}));
        }
    }
}
</script>
