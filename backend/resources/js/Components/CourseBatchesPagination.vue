<!--
  - Copyright (c) 2020 - Clarastars, LLC  - All Rights Reserved.
  -
  - Unauthorized copying of this file via any medium is strictly prohibited.
  - This file is a proprietary of Clarastars LLC and is confidential / educational purpose only.
  -
  - https://clarastars.com - info@clarastars.com
  - @author Shafiq al-Shaar <shafiqalshaar@gmail.com>
  -->

<template>
    <div v-if="course_batches">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <p class="text-gray-500 text-sm font-semibold">{{ $t('words.view-all') }} <template v-if="course_batches && course_batches.length">({{ course_batches.length }})</template></p>
            <button @click="openCreateNewCourseBatch" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150">
                {{ $t('words.add-new-batch') }}
            </button>
        </div>

        <!-- All contracts -->
        <Skeleton class="mt-3 block" height="150px" v-if="$wait.is('GETTING_COURSE')" />
        <template v-else>
            <div v-if="course_batches.length===0">
                <empty-slate class="border-2 rounded-lg mt-3">
                    <template #actions>
                        <button @click="openCreateNewCourseBatch" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150">
                            {{ $t('words.add-new-batch') }}
                        </button>
                    </template>
                </empty-slate>
            </div>
            <div class="bg-white rounded-lg my-5 p-5 shadow" v-for="batch in course_batches" :key="batch.id">
                <div class="grid grid-cols-2 sm:grid-cols-1 md:grid-cols-6 gap-12">
                    <div class="col-span-2">
                        <table class="table text-sm w-full">
                            <colgroup>
                                <col style="width:50%;">
                                <col style="width:50%;">    
                            </colgroup>
                            <tbody>
                            <tr v-if="batch.trainee_group">
                                <td class="font-semibold">{{ $t('words.name') }}</td>
                                <td class="text-gray-700">{{ batch.trainee_group.name }}</td>
                            </tr>
                            <tr v-if="batch.trainee_group">
                                <td class="font-semibold">{{ $t('words.trainees') }}</td>
                                <td class="text-gray-700">{{ batch.trainee_group.trainees_count }}</td>
                            </tr>
                            <tr>
                                <td class="font-semibold">{{ $t('words.start-date') }}</td>
                                <td class="text-gray-700">{{ toDate(batch.starts_at_timezone) }}</td>
                            </tr>
                            <tr>
                                <td class="font-semibold">{{ $t('words.end-date') }}</td>
                                <td class="text-gray-700">{{ toDate(batch.ends_at_timezone) }}</td>
                            </tr>
                            <tr>
                                <td><span class="text-white">.</span></td>
                            </tr>
                            <tr>
                                <td><button @click="deleteCourseBatch(batch)" class="hover:underline">{{ $t('words.delete') }}</button></td>
                            </tr>
                            </tbody>
                            
                        </table>
                          <button
                                @click="checkCertificateEligibility(batch.id)"
                                class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 mt-2"
                                >
                                استحقاق الشهادات بعد إنتهاء الدورة
                        </button>
                          <button
                                @click="checkCertificateEligibilityByCourse(batch.course_id)"
                                class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 mt-2"
                                >
                                الغيابات
                        </button>
                    </div>
                    <div class="col-span-1"></div>
                    <div class="col-span-3 border-2 p-2">
                        <!-- View sessions  -->
                        <div class="flex justify-start w-full">
                            <new-course-batch-session :course-batch="batch" @session:saved="getCourse()" />
                        </div>
                        <p class="mt-5 bg-blue-100 p-2 rounded-lg"><span class="font-semibold">{{ $t('words.location') }}</span>: {{ batch.location_at_display }}</p>
                        <course-batch-sessions-list class="mt-5"
                                                    @session:deleted="getCourse()"
                                                    :sessions="batch.course_batch_sessions">
                        </course-batch-sessions-list>
                    </div>
                </div>
            </div>
        </template>

        <portal to="app-modal-container">
            <modal name="createCourseBatch"
                   classes="force-overflow-auto">
                <form class="bg-white block h-5 p-10" @submit.prevent="createNewCourseBatch">
                    <h1 class="text-lg font-bold">{{ $t('words.create-course-batch') }}</h1>

                    <div class="mt-5">
                        <jet-label for="training_group_id" :value="$t('words.group-name')" />
                        <select-trainee-group class="mt-2"
                                              :loadTrainees="false"
                                              @input="selectGroupName"
                                              v-model="form.trainee_group_id"
                                              :required="true"
                        />
                        <jet-input-error :message="form.error('training_group_id')" class="mt-2" />
                    </div>

                    <div class="mt-5">
                        <jet-label for="starts_at" :value="$t('words.start-date')" />
                        <jet-input id="starts_at" type="date" class="mt-1 block w-full" v-model="form.starts_at" required />
                        <jet-input-error :message="form.error('starts_at')" class="mt-2" />
                    </div>

                    <div class="mt-5">
                        <jet-label for="ends_at" :value="$t('words.end-date')" />
                        <jet-input id="ends_at" type="date" class="mt-1 block w-full" v-model="form.ends_at" required />
                        <jet-input-error :message="form.error('ends_at')" class="mt-2" />
                    </div>

                    <div class="mt-5">
                        <jet-label for="location_at" :value="$t('words.location')" />
                        <jet-input id="location_at" type="text" class="mt-1 block w-full" v-model="form.location_at" required />
                        <jet-input-error :message="form.error('location_at')" class="mt-2" />
                    </div>

                    <div class="mt-5 mb-5">
                        <jet-secondary-button @click.native="openCreateNewCourseBatch">
                            {{ $t('words.cancel') }}
                        </jet-secondary-button>

                        <jet-button class="rtl:mr-5 ltr:ml-5"
                                    :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing">
                            {{ $t('words.save') }}
                        </jet-button>
                    </div>
                </form>
            </modal>
        </portal>
    </div>
</template>

<script>
    import JetButton from '@/Jetstream/Button';
    import { Skeleton } from 'vue-loading-skeleton';
    import Logrocket from 'logrocket';
    import EmptySlate from "@/Components/EmptySlate";
    import JetDialogModal from '@/Jetstream/DialogModal';
    import JetSecondaryButton from '@/Jetstream/SecondaryButton';
    import JetLabel from '@/Jetstream/Label';
    import JetInput from '@/Jetstream/Input';
    import JetInputError from '@/Jetstream/InputError';
    import CourseBatchSessionsList from "@/Components/CourseBatchSessionsList";
    import NewCourseBatchSession from "./NewCourseBatchSession";
    import SelectTraineeGroup from '@/Components/SelectTraineeGroup';

    export default {
        components: {
            JetButton,
            Skeleton,
            EmptySlate,
            JetDialogModal,
            JetSecondaryButton,
            JetLabel,
            JetInput,
            JetInputError,
            CourseBatchSessionsList,
            NewCourseBatchSession,
            SelectTraineeGroup,
        },
        props: ['courseId'],
        name: "CourseBatchesPagination",
        data() {
            return {
                form: this.$inertia.form({
                   trainee_group_id: null,
                   starts_at: Date(),
                   ends_at: Date(),
                   location_at: 'online',
                }, {
                    resetOnSuccess: true,
                    bag: 'form',
                }),
                course_batches: null,
            }
        },
        mounted() {
            this.$wait.start('GETTING_COURSE');
            this.getCourse();
        },
        computed: {
            rtl() {
                let lang = document.documentElement.lang.substr(0, 2);
                return lang === 'ar';
            },
        },
        methods: {
            selectGroupName(input) {
                this.form.trainee_group_id = input.id.split('-group')[0];
            },
            getCourse() {
                axios.get(route('back.course-batches.index', {id: this.courseId}))
                    .then(response => {
                        this.course_batches = response.data;
                        this.$wait.end('GETTING_COURSE')
                    }).catch(error => {
                    Logrocket.captureException(error);
                    throw error;
                }).finally(() => {
                    this.$wait.end('GETTING_COURSE')
                })
            },
            toDate(timestamp) {
                return moment(timestamp).local().format('YYYY-MM-DD');
            },
            openCreateNewCourseBatch() {
                this.$modal.toggle('createCourseBatch');
            },
            createNewCourseBatch() {
                this.form.post(route('back.course-batches.store', {course_id: this.courseId}))
                    .then(response => {
                        this.openCreateNewCourseBatch();
                        this.getCourse();
                    });
            },
            deleteCourseBatch(batch) {

                if (batch.course_batch_sessions.length) {
                    alert(this.$t('words.you-must-delete-the-course-batches-first'));
                    return false;
                }

                if (confirm(this.$t('words.are-you-sure'))) {
                    axios.delete(route('back.course-batches.destroy', {
                        course_id: batch.course_id,
                        course_batch: batch.id,
                    }))
                    .then(response => {
                        this.getCourse();
                    }).catch(error => {
                        Logrocket.captureException(error);
                        alert(this.$t('words.error-occurred'));
                        throw error;
                    });
                }
            },

            checkCertificateEligibility(id){
                    window.location.href=`/attendance/export-by-group/${id}`;
            },  
            checkCertificateEligibilityByCourse(id){
                    window.location.href=`/attendance/export-by-course/${id}`;
            },  

            
        }
    }
</script>
