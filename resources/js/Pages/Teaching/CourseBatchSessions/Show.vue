<template>
    <zoom-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'courses', link: route('teaching.courses.index')},
                    {title_raw: course_batch_session.course.name_ar},
                ]"
            ></breadcrumb-container>

            <div class="grid grid-cols-6 gap-6">
                <div class="col-span-6 flex items-center justify-end bg-gray-50 text-right">
                    <button @click="joinMeeting"
                            :disabled="$wait.is('STARTING_INSTRUCTOR_SESSION')"
                            class="flex items-center justify-start rounded-md px-4 py-2 bg-gray-200 hover:bg-gray-300 text-right">
                        Join meeting
                    </button>
                </div>
            </div>
        </div>
    </zoom-layout>
</template>

<script>
    import ZoomLayout from "@/Layouts/ZoomLayout";
    import JetSectionBorder from '@/Jetstream/SectionBorder'
    import Breadcrumb from "@/Components/Breadcrumb";
    import JetDialogModal from '@/Jetstream/DialogModal'
    import JetInput from '@/Jetstream/Input'
    import JetInputError from '@/Jetstream/InputError'
    import JetActionMessage from '@/Jetstream/ActionMessage';
    import JetButton from '@/Jetstream/Button';
    import JetFormSection from '@/Jetstream/FormSection';
    import JetLabel from '@/Jetstream/Label';
    import BreadcrumbContainer from "@/Components/BreadcrumbContainer";
    import { ZoomMtg } from '@zoomus/websdk';
    import ZoomArabic from '@/zoom-ar-language-key-value.js';


    export default {
        props: ['course_batch_session'],

        components: {
            ZoomLayout,
            JetSectionBorder,
            Breadcrumb,
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
                meetingConfig: [],
                createdMeetingNumber: null,
            }
        },
        mounted() {

            console.log(JSON.stringify(ZoomMtg.checkSystemRequirements()));
            ZoomMtg.preLoadWasm();
            ZoomMtg.prepareJssdk()

            //Add your own custom language key
            var langArray = ['en-US', 'ar-SA'];

            // set the userLangTemplate variable to a default language code
            var userLangTemplate = $.i18n.getAll("en-US");

            // Define the userLangDict variable
            // Use the language-key-value.json file to determine which keys to set the custom language
            // https://zoom.github.io/sample-app-web/languages/en-US.json
            console.log(ZoomArabic);
            var userLangDict = Object.assign({}, userLangTemplate, ZoomArabic);
            // Set the userLangDict and custom code language in the load method
            $.i18n.load(userLangDict, "ar-SA");

            //Add the language code to the internationalization.reload method.
            $.i18n.reload("ar-SA");
            //Add the language code to the ZoomMtg.reRender method.
            ZoomMtg.reRender({lang: "ar-SA"});
        },
        methods: {
            joinMeeting() {
                axios.post(route('back.zoom.meetings.configs'))
                    .then(response => {
                        this.meetingConfig = response.data;

                        return axios.post(route('back.zoom.signature'), {
                            meeting_id: response.data.meetingNumber,
                            role: response.data.role,
                        })
                    }).then(signatureResponse => {
                    let vm = this;
                    document.getElementById("zmmtg-root").style.display = "block";
                    console.log(vm.meetingConfig);
                    ZoomMtg.init({
                        leaveUrl: vm.meetingConfig.leaveUrl,
                        isSupportAV: true,
                        success: function() {
                            ZoomMtg.join({
                                signature: signatureResponse.data,
                                apiKey: vm.meetingConfig.apiKey,
                                meetingNumber: vm.meetingConfig.meetingNumber,
                                userName: vm.meetingConfig.userName,
                                passWord: vm.meetingConfig.password,
                                error(res) {
                                    console.log(res);
                                },
                            })
                        },
                    })
                })
            },
        }
    }
</script>

<style>
    #zmmtg-root {
        display: none;
    }
</style>
