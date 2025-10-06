<style lang="css">
.search-results-box {
    position: absolute;
    width: 600px;
    margin-top: -10px;
}
</style>
<template>
    <app-layout>
        <div class="container px-6 mx-auto grid">
            <div class="mt-20" v-if="company">
                <div class="md:grid md:grid-cols-3 md:gap-6">
                    <div class="md:col-span-1">
                        <div class="px-4 sm:px-0">
                            <h3 class="text-lg font-medium text-gray-900">
                                {{ company.name_ar }}<br/>
                                {{ company.name_en }}
                            </h3>
                            <p class="mt-1 text-sm text-gray-600">
                                {{ $t('words.resignations') }}
                            </p>
                            <p class="mt-1 text-sm text-gray-600">
                                {{ $t('words.upload') }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-5 md:mt-0 md:col-span-2">
                        <form @submit.prevent="submitForm" enctype="multipart/form-data">
                            <div class="shadow overflow-hidden sm:rounded-md">
                                <div class="px-4 py-5 bg-white sm:p-6">
                                    <div class="grid bg-yellow-200 rounded p-2">
                                        <p v-if="resignation.sent_at">تم الرسال الاستقالات في التاريخ: {{ resignation.sent_at_timezone }}</p>
                                        <p v-else>يرجى دمج جميع الاستقالات في ملف PDF واحد فقط.</p>
                                    </div>

                                    <div class="mt-2 grid grid-cols-1 gap-4">
                                        <table class="table-auto w-full text-xs table-5">
                                            <colgroup>
                                                <col style="width:10px;">
                                                <col style="width:200px;">
                                                <col style="width:100px;">
                                            </colgroup>
                                            <thead>
                                                <tr>
                                                    <th class="text-center border-black border bg-gray-300 px-2">#</th>
                                                    <th class="text-right border-black border bg-gray-300 px-2">{{ $t('words.name') }}</th>
                                                    <th class="text-right border-black border bg-gray-300 px-2">{{ $t('words.identity_number') }}</th>
                                                    <th class="text-right border-black border bg-gray-300 px-2">{{ $t('words.current-company-as-of-today') }}</th>
                                                     <th class="text-right border-black border bg-gray-300 px-2">تاريخ الاستقالة</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(trainee, key) in resignation.trainees" :key="trainee.id">
                                                    <td class="text-center border-black border px-2">{{ ++key }}</td>
                                                    <td class="border-black border px-2">{{ trainee.name }}</td>
                                                    <td class="border-black border px-2">{{ trainee.identity_number }}</td>
                                                    <td class="border-black border px-2"><span v-if="trainee.company">{{ trainee.company.name_ar }}</span></td>
                                                     <td class="border-black border px-2">
                                                        {{ resignation.resignation_date }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="mt-5" v-if="resignation.media && resignation.media.length">
                                        <a class="text-sm text-blue-500" :href="route('back.media.download', {media_id: resignation.media[0].id})" target="_blank">
                                            تحميل ملف الاستقالة الحالي بالضغط من هنا
                                        </a>
                                    </div>

                                    <div class="mt-5" v-if="!resignation.sent_at">
                                        <jet-label for="files" :value="$t('words.resignation-file')+' (PDF)'"/>
                                        <p class="text-sm text-gray-600 mt-1">الحد الأقصى لحجم الملف: 500 ميجابايت</p>
                                        <div class="flex justify-center">
                                            <div class="mb-3 w-full">
                                                <input class="form-control block w-full mt-2 px-3 py-1.5 text-base font-normal text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none"
                                                       @input="form.resignation_file = $event.target.files[0]"
                                                       type="file"
                                                       id="files"
                                                       accept=".pdf"
                                                       required>
                                            </div>
                                        </div>
                                        <jet-input-error :message="form.error('resignation_file')" class="mt-2" />
                                    </div>


                                    <div class="grid grid-cols-4 gap-6">
                                        <div class="col-span-4 sm:col-span-4">
                                            <!--<jet-label for="notes" :value="$t('words.notes')" />-->
                                            <!--<jet-textarea id="notes" type="textarea" class="mt-1 block w-full" />-->
                                        </div>

                                        <div class="flex items-center justify-start px-4 py-3 text-right">
                                            <inertia-link :href="`/back/companies/${company.id}`" class="flex items-center justify-start rtl:ml-4 ltr:mr-4 rounded-md px-4 py-2 bg-white hover:bg-gray-300 text-right">
                                                {{ $t('words.cancel') }}
                                            </inertia-link>

                                            <jet-button v-if="!resignation.sent_at" :class="{ 'opacity-25': $wait.is('SAVING_CONTRACT') }" :disabled="$wait.is('SAVING_CONTRACT')">
                                                {{ $t('words.save') }}
                                            </jet-button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
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
import JetTextarea from '@/Jetstream/Textarea'
import VueDropzone from 'vue2-dropzone'
import 'vue2-dropzone/dist/vue2Dropzone.min.css'
import _ from "lodash";
import { Skeleton } from 'vue-loading-skeleton';

export default {
    props: ['sessions', 'company', 'resignation'],

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
        JetTextarea,
        VueDropzone,
        Skeleton,
    },
    data() {
        return {
            form: this.$inertia.form({
                company_id: null,
                resignation_file: null,
            }),
            searchString: '',
            searchResults: [],
            searchBoxVisible: false,
        }
    },
    mounted() {

    },
    methods: {
        submitForm() {
            if(confirm(this.$t('words.are-you-sure'))) {
                this.form.post(route('back.resignations.upload.store', {company_id: this.company.id, id: this.resignation.id}), {
                    preserveScroll: true
                }).catch(error => {
                    this.form.processing = false;
                }).finally(() => {
                    this.form.processing = false;
                });
            }
        },
    }
}
</script>
