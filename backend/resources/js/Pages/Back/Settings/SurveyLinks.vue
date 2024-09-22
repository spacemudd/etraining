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
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'settings', link: route('back.settings')},
                    {title: 'edit-survey-links'}
                ]"
            ></breadcrumb-container>

            <div class="grid md:grid-cols-6 grid-cols-1 gap-6">
                <div class="col-span-3 bg-white shadow-xs rounded-lg p-5">
                    <h1>{{ $t('words.instructor-survey-url') }}</h1>
                    <form class="mt-2" @submit.prevent="saveInstructorSurveyLink">
                        <jet-input type="text"
                                   class="mt-1 block w-full"
                                   v-model="instructor.url"
                                   autocomplete="off"
                                   required="true" />

                        <jet-button :class="{ 'opacity-25': $wait.is('SAVING_CONTRACT') }"
                                    class="mt-2"
                                    :disabled="$wait.is('SAVING_CONTRACT')">
                            {{ $t('words.save') }}
                        </jet-button>
                    </form>
                </div>
                <div class="col-span-3 bg-white shadow-xs rounded-lg p-5">
                    <h1>{{ $t('words.trainee-survey-url') }}</h1>
                    <form class="mt-2" @submit.prevent="saveTraineeSurveyLink">
                        <jet-input type="text"
                                   class="mt-1 block w-full"
                                   v-model="trainee.url"
                                   autocomplete="off"
                                   required="true" />

                        <jet-button :class="{ 'opacity-25': $wait.is('SAVING_CONTRACT') }"
                                    class="mt-2"
                                    :disabled="$wait.is('SAVING_CONTRACT')">
                            {{ $t('words.save') }}
                        </jet-button>
                    </form>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import IconNavigate from 'vue-ionicons/dist/ios-arrow-dropright'
import BreadcrumbContainer from "@/Components/BreadcrumbContainer";
import JetInput from '@/Jetstream/Input'
import JetButton from '@/Jetstream/Button';

export default {
    metaInfo: { title: 'Settings' },
    components: {
        IconNavigate,
        AppLayout,
        BreadcrumbContainer,
        JetInput,
        JetButton,
    },
    props: [
        'instructor_survey_link',
        'trainee_survey_link',
    ],
    mounted() {
        if (this.instructor_survey_link) {
            this.instructor.url = this.instructor_survey_link;
        }

        if (this.trainee_survey_link) {
            this.trainee.url = this.trainee_survey_link;
        }
    },
    data() {
        return {
            instructor: {
                type: 'instructor',
                url: '',
            },
            trainee: {
                type: 'trainee',
                url: '',
            }
        }
    },
    methods: {
        saveInstructorSurveyLink() {
            this.$inertia.post(route('back.settings.survey-links.store'), this.instructor);
        },
        saveTraineeSurveyLink() {
            this.$inertia.post(route('back.settings.survey-links.store'), this.trainee);
        },
    }
}
</script>

<style lang="sass">
//
</style>
