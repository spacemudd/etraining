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
    <app-layout-trainee>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'my-attendance', link: route('trainees.attendance-sheet.index')},
                    {title: 'upload-absence-reason'}
                ]"
            ></breadcrumb-container>

            <div class="bg-white rounded shadow overflow-x-auto my-5 p-5">
                <h1 class="text-2xl">{{ $t('words.upload-absence-reason') }}</h1>

                <!-- <p>{{ $t('words.course') }}: {{ attendance_report_record.course_batch_session.course.name_ar }}</p> -->
                <!-- <p>{{ $t('words.time') }}: <span dir="ltr">{{ attendance_report_record.course_batch_session.starts_at_timezone }}</span></p> -->

                <form class="mt-5" @submit.prevent="saveForm" enctype="multipart/form-data">
                    <div class="mt-3">
                        <jet-label for="files" :value="$t('words.upload-absence-reason')"/>
                        <div class="flex justify-center">
                            <div class="mb-3 w-full">
                                <input class="form-control block w-full mt-2 px-3 py-1.5 text-base font-normal text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none"
                                       @input="form.files = $event.target.files"
                                       type="file"
                                       id="formFileMultiple"
                                       multiple>
                            </div>
                        </div>
                        <jet-input-error :message="form.error('files')" class="mt-2" />
                    </div>
                    <button class="btn btn-primary">{{ $t('words.save') }}</button>
                </form>
            </div>
        </div>
    </app-layout-trainee>
</template>

<script>
import AppLayoutTrainee from '@/Layouts/AppLayoutTrainee'
import BreadcrumbContainer from "@/Components/BreadcrumbContainer";
import JetInputError from "@/Jetstream/InputError";
import JetLabel from "@/Jetstream/Label";

export default {
    components: {
        AppLayoutTrainee,
        BreadcrumbContainer,
        JetInputError,
        JetLabel,
    },
    props: [
        'absence_note',
    ],
    data() {
        return {
            form: this.$inertia.form({
                files: '',
            }, {
                bag: 'uploadNote',
            })
        }
    },
    mounted() {
        //
    },
    methods: {
        saveForm() {
            this.form.put(route('trainees.attendance-report-record.absence-notes.update',
                {'absence_note_id': this.absence_note.id}
            ));
        },
    }
}
</script>
