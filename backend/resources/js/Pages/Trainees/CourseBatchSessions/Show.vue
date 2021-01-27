<template>
    <div>
        <div class="">
            <div class="" style="margin-top: 2rem;width:200px;margin-left:5rem;">
                <div class="">
                    <button @click="joinMeeting"
                            :disabled="$wait.is('STARTING_TRAINEE_SESSION')"
                            class="btn btn-primary">
                        {{ $t('words.join-meeting-via-browser') }}
                    </button>

                    <br/>
                    <!--<button @click="joinMeeting"-->
                    <!--        :disabled="$wait.is('STARTING_TRAINEE_SESSION')"-->
                    <!--        class="mt-5 rounded-md px-4 py-2 bg-gray-200 hover:bg-gray-300 text-right">-->
                    <!--    {{ $t('words.join-meeting-via-client') }}-->
                    <!--</button>-->
                    <a  :href="course_batch_session.join_url"
                        :disabled="$wait.is('STARTING_INSTRUCTOR_SESSION')"
                        dir="rtl"
                        style="margin-top:5rem;"
                        class="btn btn-primary">
                        {{ $t('words.join-meeting-via-client') }}
                    </a>
                </div>
            </div>
        </div>
        <img id="center-logo"
             v-if="showCenterLogo"
             src="/img/logo-lg.png"
             style="z-index: 999999;display: block;position: absolute;top: 0;width: 150px;background-color: white;border-radius: 0 0 10px 0;padding-bottom: 10px;opacity: 0.9;">
    </div>
</template>

<script>
    import { ZoomMtg } from '@zoomus/websdk';
    import ZoomArabic from '@/zoom-ar-language-key-value.js';

    export default {
        props: ['course_batch_session'],

        components: {
            //
        },
        data() {
            return {
                meetingConfig: [],
                createdMeetingNumber: null,
                showCenterLogo: false,
            }
        },
        mounted() {

            ZoomMtg.preLoadWasm();
            ZoomMtg.prepareJssdk()

            //Add your own custom language key
            //var langArray = ['en-US', 'ar-SA'];

            // set the userLangTemplate variable to a default language code
            //var userLangTemplate = $.i18n.getAll("en-US");

            // Define the userLangDict variable
            // Use the language-key-value.json file to determine which keys to set the custom language
            // https://zoom.github.io/sample-app-web/languages/en-US.json
            //var userLangDict = Object.assign({}, userLangTemplate, ZoomArabic);
            // Set the userLangDict and custom code language in the load method
            //$.i18n.load(userLangDict, "ar-SA");

            //Add the language code to the internationalization.reload method.
            //$.i18n.reload("ar-SA");
            //Add the language code to the ZoomMtg.reRender method.
            //ZoomMtg.reRender({lang: "ar-SA"});
        },
        methods: {
            joinMeeting() {
                this.showCenterLogo = true;

                axios.post(route('back.zoom.meetings.configs'), {
                    course_batch_session_id: this.course_batch_session.id,
                })
                    .then(response => {
                        this.meetingConfig = response.data;

                        return axios.post(route('back.zoom.signature'), {
                            meeting_id: response.data.meetingNumber,
                            role: response.data.role,
                        })
                    }).then(signatureResponse => {
                    let vm = this;
                    document.getElementById("zmmtg-root").style.display = "block";
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
