<template>
    <ul class="mt-6">
        <sidebar-link link-value="/dashboard" :active="$page.currentRouteName == 'dashboard'">
            <template #icon><DesktopIcon w="20px" h="20px" class="w-5 h-5" /></template>
            <template #title>
                <span class="ltr:ml-4 rtl:mr-4">{{ $t('words.dashboard') }}</span>
            </template>
        </sidebar-link>

        <sidebar-link :link-value="route('teaching.courses.index')" :active="$page.currentRouteName == 'teaching.courses.index'">
            <template #icon>
                <ion-icon name="business-outline" class="w-5 h-5"></ion-icon>
            </template>
            <template #title>
                <span class="ltr:ml-4 rtl:mr-4">{{ $t('words.courses') }}</span>
            </template>
        </sidebar-link>

        <button @click="moveElement"
                :disabled="$wait.is('STARTING_INSTRUCTOR_SESSION')"
                class="flex items-center justify-start rounded-md px-4 py-2 bg-gray-200 hover:bg-gray-300 text-right">
            Move element
        </button>

        <button @click="createSignature"
                :disabled="$wait.is('STARTING_INSTRUCTOR_SESSION')"
                class="flex items-center justify-start rounded-md px-4 py-2 bg-gray-200 hover:bg-gray-300 text-right">
            Make Signature
        </button>

        <button @click="createMeeting"
                :disabled="$wait.is('STARTING_INSTRUCTOR_SESSION')"
                class="flex items-center justify-start rounded-md px-4 py-2 bg-gray-200 hover:bg-gray-300 text-right">
            Create meeting
        </button>

        <button @click="joinMeeting"
                :disabled="$wait.is('STARTING_INSTRUCTOR_SESSION')"
                class="flex items-center justify-start rounded-md px-4 py-2 bg-gray-200 hover:bg-gray-300 text-right">
            Join meeting
        </button>


        <!--<sidebar-link link-value="/">-->
        <!--    <template #icon>-->
        <!--        <ion-icon name="settings-outline" class="w-5 h-5"></ion-icon>-->
        <!--    </template>-->
        <!--    <template #title>-->
        <!--        <span class="ltr:ml-4 rtl:mr-4">{{ $t('words.settings') }}</span>-->
        <!--    </template>-->
        <!--</sidebar-link>-->
    </ul>
</template>

<script>
    import DesktopIcon from 'vue-ionicons/dist/ios-desktop.vue'
    import BusinessIcon from 'vue-ionicons/dist/ios-business';
    import SidebarLink from "./SidebarLink";
    // import { ZoomMtg } from '@zoomus/websdk';

    export default {
        components: {
            DesktopIcon,
            SidebarLink,
            BusinessIcon,
        },
        data() {
            return {
                meetingConfig: [],
                createdMeetingNumber: null,
            }
        },
        mounted() {
            //
        },
        methods: {
            moveElement() {
                $('#zmmtg-root').appendTo('#zoom-container');
            },
            createSignature() {
                axios.post(route('back.zoom.signature'))
            },
            createMeeting() {
                axios.post(route('back.zoom.meetings.store', {
                    course_batch_session_id: '123',
                }))
                    .then(response => {
                        this.createdMeetingNumber = response.data.id
                        console.log(response.data);
                    })
            },
            joinMeeting() {
                ZoomMtg.preLoadWasm();
                ZoomMtg.prepareJssdk()

                axios.post(route('back.zoom.meetings.configs'), {
                    createdMeetingNumber: this.createdMeetingNumber,
                })
                .then(response => {
                    this.meetingConfig = response.data;

                    return axios.post(route('back.zoom.signature'), {
                        meeting_id: response.data.meetingNumber,
                        role: response.data.role,
                    })
                }).then(signatureResponse => {
                    let vm = this;
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

<style lang="sass">
    //
</style>
