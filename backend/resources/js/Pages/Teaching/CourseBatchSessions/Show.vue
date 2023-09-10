<template>
    <div>
        <div>
            <div style="margin-top: 2rem; margin-left: 40px;">
                <div>
                    <span style="color:white;">{{ account_id }}</span>
                    <!--<button @click="joinMeeting"-->
                    <!--        :disabled="$wait.is('STARTING_INSTRUCTOR_SESSION')"-->
                    <!--        class="btn btn-primary">-->
                    <!--    {{ $t('words.join-meeting-via-browser') }}-->
                    <!--</button>-->

                    <br/><br/>

                    <a  :href="course_batch_session.start_url"
                        :disabled="$wait.is('STARTING_INSTRUCTOR_SESSION')"
                        dir="rtl"
                        style="margin-top:3rem;"
                        class="btn btn-primary">
                        {{ $t('words.join-meeting-via-client') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import { ZoomMtg } from '@zoomus/websdk';
    import ZoomArabic from '@/zoom-ar-language-key-value.js';

    export default {
        props: ['course_batch_session', 'account_id'],

        components: {
            //
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
