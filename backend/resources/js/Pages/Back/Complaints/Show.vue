<template>
    <app-layout>
        <breadcrumb-container
            :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'complaints', link: route('complaints.index')},
                    {title_raw: complaint.complaints_number_formatted},
                ]"
        ></breadcrumb-container>

        <div class="flex flex-col md:flex-row md:space-x-5 place-content-center">
            <div class="w-full md:w-8/12">
                <div class="flex justify-between">
                    <div class="mb-6 flex justify-end items-center">
                        <h1 class="mb-8 font-bold text-2xl img {display:block} inline-flex">{{ $t('words.complaint') }} </h1>


                    </div>
                    <div class="place-items-start ml-20" style="margin-left: 100px">
                        <button @click="RollOutNew(complaint.id)"
                                type="button"
                                v-if="complaint.complaints_status === 0"
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest focus:outline-none focus:shadow-outline-gray transition ease-in-out duration-150 disabled:cursor-not-allowed mx-2 bg-gray-700 hover:bg-red-600 active:bg-red-700 foucs:bg-red-700">
                            {{ $t('words.roll-out') }}
                        </button>
                        <button @click="RollOutInProgress(complaint.id)"
                                type="button"
                                v-if="complaint.complaints_status === 1"
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest focus:outline-none focus:shadow-outline-gray transition ease-in-out duration-150 disabled:cursor-not-allowed mx-2 bg-gray-700 hover:bg-red-600 active:bg-red-700 foucs:bg-red-700">
                            {{ $t('words.done') }}
                        </button>
                        <button @click="RollOutDone(complaint.id)"
                                type="button"
                                v-if="complaint.complaints_status === 2"
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest focus:outline-none focus:shadow-outline-gray transition ease-in-out duration-150 disabled:cursor-not-allowed mx-2 bg-gray-700 hover:bg-red-600 active:bg-red-700 foucs:bg-red-700">
                            {{ $t('words.return-complaints') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex flex-col md:flex-row md:space-x-5 place-content-center">
            <div class="w-full md:w-8/12">
                <div class="flex justify-between">
                    <div class="mb-6 flex justify-end items-center">
                        <div class="flex flex-col md:flex-row justify-start rounded bg-gray-50 rounded shadow-lg p-5">
                            <div class="w-full p-4 mx-5">
                                <div class="grid grid-cols-2">
                                    <div class="font-bold ml-6">{{ $t('words.complaints-number') }}</div>
                                    <div class="">{{ complaint.complaints_number_formatted }}</div>
                                    <div class="font-bold ml-6">{{ $t('words.name') }}</div>
                                    <div class="">{{ complaint.trainee.name }}</div>
                                    <div class="font-bold ml-6">{{ $t('words.identity-number') }}</div>
                                    <div class="">{{ complaint.trainee.identity_number }}</div>
                                    <div class="font-bold ml-6">{{ $t('words.phone') }}</div>
                                    <div class="">{{ complaint.trainee.phone }}</div>
                                    <div class="font-bold ml-6">{{ $t('words.company') }}</div>
                                    <div class="">{{ complaint.company.name_ar }}</div>
                                </div>
                            </div>
                            <div class="w-full p-4 mx-5">
                                <div class="grid grid-cols-2">
                                    <div class="font-bold mx-6">{{ $t('words.contact-way') }}</div>
                                    <div class="">{{ complaint.contact_way }}</div>
                                    <div class="font-bold mx-6">{{ $t('words.complaints') }}</div>
                                    <div class="">{{ complaint.complaints }}</div>
                                    <div class="font-bold mx-6">{{ $t('words.order-date') }}</div>
                                    <div class="">{{ complaint.created_at | formatDate }}</div>
                                    <div class="font-bold mx-6">{{ $t('words.reply') }}</div>
                                    <div class="">{{ complaint.reply }}</div>
                                    <div class="font-bold mx-6">{{ $t('words.results') }}</div>
                                    <div class="">{{ complaint.results }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="flex flex-col md:flex-row md:space-x-5 place-content-center">
            <div class="w-full md:w-8/12">
                <div class="flex justify-between">
                    <div class="mb-6 flex justify-end items-center">
                        <h1 class="mb-8 font-bold text-2xl img {display:block} inline-flex">{{ $t('words.comments') }} </h1>
                    </div>
                    <div class="place-items-start ml-20" style="margin-left: 100px">
                        <button @click="AddComment(complaint.id)"
                                type="button"
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest focus:outline-none focus:shadow-outline-gray transition ease-in-out duration-150 disabled:cursor-not-allowed mx-2 bg-gray-700 hover:bg-red-600 active:bg-red-700 foucs:bg-red-700">
                            {{ $t('words.add-note') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex flex-col md:flex-row md:space-x-5 place-content-center">
            <div class="w-full md:w-8/12 mx-20 ml-20">
                <div class="mb-3 pt-0"  style="margin-left: 100px">
                    <input type="text" placeholder="ملاحظات" class=" shadow-lg px-3 pb-20 pt-1 placeholder-gray-500 text-slate-600 relative bg-white rounded text-base border-0 outline-none focus:outline-none focus:ring w-full"/>
                </div>
            </div>
        </div>

    </app-layout>
</template>
<script>
import AppLayout from "../../../Layouts/AppLayout";
import EmptySlate from "@/Components/EmptySlate";
import BreadcrumbContainer from "@/Components/BreadcrumbContainer";
import Pagination from '@/Shared/Pagination'
import VueDropzone from "vue2-dropzone";
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
import 'vue2-dropzone/dist/vue2Dropzone.min.css'
import SelectTraineeGroup from "@/Components/SelectTraineeGroup";
import ChangeTraineePassword from '@/Components/ChangeTraineePassword';
import AttendanceSheetManagementForTrainee from "@/Components/AttendanceSheetManagementForTrainee";
import 'selectize/dist/js/standalone/selectize.min';
export default {
    metaInfo: { title: 'Complaints trainees_complaints' },
    props: [
        'complaint',
    ],
    components: {
        AppLayout,
        BreadcrumbContainer,
        Pagination,
        AttendanceSheetManagementForTrainee,
        Breadcrumb,
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
    methods: {
        RollOut(trainees_complaint) {
            this.$inertia.put(route('complaints.NewToInProgressStatus', trainees_complaint), {
                id: trainees_complaint,
                complaints_status: 1,
            })
        },
        RollOutInProgress(trainees_complaint) {
            this.$inertia.put(route('complaints.InProgressToDoneStatus', trainees_complaint), {
                id: trainees_complaint,
                complaints_status: 1,
            })
        },
        RollOutDone(trainees_complaint) {
            this.$inertia.put(route('complaints.DoneToInProgressStatus', trainees_complaint), {
                id: trainees_complaint,
                complaints_status: 1,
            })
        },
        AddComment(){

        }


    }
}
</script>
