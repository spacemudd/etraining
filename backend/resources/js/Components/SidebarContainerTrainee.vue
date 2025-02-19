<template>
    <ul class="mt-6">
        <li class="relative px-6 py-3">
            <span class="inline-flex items-center w-full text-sm font-semibold text-gray-800 transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 dark:text-gray-100" aria-hidden="true"></span>
            <a href="https://jisr-ksa.com" class="inline-flex items-center w-full text-sm text-gray-800 transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 dark:text-gray-100">
                <EarthIcon w="20px" h="20px" class="w-5 h-5" />
                <span class="ltr:ml-4 rtl:mr-4 hover:text-red-600 hover:font-bold">{{ $t('words.main') }}</span>
            </a>
        </li>

        <sidebar-link link-value="/dashboard" :active="$page.currentRouteName == 'dashboard'">
            <template #icon><DesktopIcon w="20px" h="20px" class="w-5 h-5" /></template>
            <template #title>
                <span class="ltr:ml-4 rtl:mr-4">{{ $t('words.dashboard') }}</span>
            </template>
        </sidebar-link>

        <!-- <sidebar-link :link-value="route('trainees.courses.index')" :active="$page.currentRouteName == 'trainees.courses.index'">
            <template #icon>
                <ion-icon name="business-outline" class="w-5 h-5"></ion-icon>
            </template>
            <template #title>
                <span class="ltr:ml-4 rtl:mr-4">{{ $t('words.courses') }}</span>
            </template>
        </sidebar-link> -->
<!-- 
        <sidebar-link :link-value="route('trainees.training-packages.index')" :active="$page.currentRouteName == 'trainees.training-packages.index'">
            <template #icon>
                <ion-icon name="albums-outline" class="w-5 h-5"></ion-icon>
            </template>
            <template #title>
                <span class="ltr:ml-4 rtl:mr-4">{{ $t('words.training-packages') }}</span>
            </template>
        </sidebar-link> -->
<!-- 
        <sidebar-link :link-value="route('trainees.training-plan.index')" :active="$page.currentRouteName == 'trainees.training-plan.index'">
            <template #icon>
                <ion-icon name="school-outline" class="w-5 h-5"></ion-icon>
            </template>
            <template #title>
                <span class="ltr:ml-4 rtl:mr-4">{{ $t('words.training-plan') }}</span>
            </template>
        </sidebar-link> -->

        <sidebar-link :link-value="route('trainees.attendance-sheet.index')"
                      :active="$page.currentRouteName == 'trainees.attendance-sheet.index'">
            <template #icon>
                <ion-icon name="calendar-outline" class="w-5 h-5"></ion-icon>
            </template>
            <template #title>
                <span class="ltr:ml-4 rtl:mr-4">{{ $t('words.my-attendance') }}</span>
            </template>
        </sidebar-link>

        <sidebar-link :link-value="route('inbox.index')" :active="$page.currentRouteName == 'inbox.index'">
            <template #icon>
                <ion-icon name="mail-outline" class="w-5 h-5"></ion-icon>
            </template>
            <template #title>
                <span class="ltr:ml-4 rtl:mr-4">{{ $t('words.messages') }} <span v-if="$page.props.user.inbox_messages_count" class="bg-red-500 rounded p-1 text-white">{{ $page.props.user.inbox_messages_count }}</span></span>
            </template>
        </sidebar-link>

       <!-- <sidebar-link v-if="contractStatus !== 'completed'">
            <template #icon>
            </template>
            <template #title>
                <span class=""><button class="px-6 py-2 bg-blue-500 text-white font-semibold rounded-lg shadow-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50"
                 @click="sendEmbeddedContract">{{ $t('words.sign-contract') }}</button> </span>
            </template>
        </sidebar-link> -->

        
        <template v-if="contractStatus === 'completed'">
                <!-- <ion-icon name="document-text-outline" class="w-5 h-5 mx-2"></ion-icon> -->
        
                <span class="ltr:ml-4 rtl:mr-4">
                <!-- <button 
                    @click="viewContract"
                    class="px-6 py-2 bg-blue-500 text-white font-semibold rounded-lg shadow-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                    {{ $t('words.view-contract') }}
                </button>  -->
                
                 <p class="text-red-500 mx-4 px-4"> تم توثيق العقد</p>
                </span>
        </template>


        
        <!-- <sidebar-link v-if="contractStatus !== 'completed'" :link-value="route('contract-guides')" >
            <template #icon>
                <ion-icon name="document-attach" class="w-5 h-5"></ion-icon>
            </template>
            <template #title>
                <span class="ltr:ml-4 rtl:mr-4">إرشادات لتوثيق العقد </br> (يجب القراءة أولا)
                    <span v-if="$page.props.user.inbox_messages_count" class="bg-red-500 rounded p-1 text-white">{{ $page.props.user.inbox_messages_count }}</span>
                </span>
        
            </template>
        </sidebar-link> -->


        <!-- <sidebar-link :link-value="route('agreement.show')" :active="$page.currentRouteName == 'agreement.show'">
            <template #icon>
                    <ion-icon name="document-outline" class="w-5 h-5"></ion-icon>
            </template>
            <template #title>
                <span class="ltr:ml-4 rtl:mr-4">{{ $t('words.my-agreement') }} <span v-if="$page.props.user.inbox_messages_count" class="bg-red-500 rounded p-1 text-white">{{ $page.props.user.inbox_messages_count }}</span></span>
            </template>
        </sidebar-link> -->

       <!-- <li class="pr-8 mt-2 text-xs"><inertia-link class="hover:text-blue-600" :href="route('survey.index')">الاستبيانات</inertia-link>
        <li class="pr-8 mt-2 text-xs"><inertia-link class="hover:text-blue-600" :href="route('training-schedule.index')">الخطة التدريبية والجدول الزمني</inertia-link>
        <li class="pr-8 mt-2 text-xs"><inertia-link class="hover:text-blue-600" :href="route('training-plan.index')">خطة تدريب المدربين</inertia-link>
        <li class="pr-8 mt-2 text-xs"><inertia-link class="hover:text-blue-600" :href="route('user-guides.index')">الادلة الارشادية</inertia-link>
        <li class="pr-8 mt-2 text-xs"><inertia-link class="hover:text-blue-600" :href="route('obligations.index')">الالتزامات</inertia-link>
        <li class="pr-8 mt-2 text-xs"><inertia-link class="hover:text-blue-600" :href="route('company-roles.index')">وثيقة الادوار والمسؤوليات</inertia-link>
        <li class="pr-8 mt-2 text-xs"><inertia-link class="hover:text-blue-600" :href="route('suggestions.index')">للاقتراحات والاستفسارات</inertia-link>
        <li class="pr-8 mt-2 text-xs"><inertia-link class="hover:text-blue-600" :href="route('complaints.index')">الشكاوى والملاحظات</inertia-link>
        <li class="pr-8 text-xs"><a class="hover:text-blue-600" href="/terms">السياسات</a></li> -->
    </ul>
</template>

<script>
import EarthIcon from 'vue-ionicons/dist/ios-easel.vue';
import DesktopIcon from 'vue-ionicons/dist/ios-desktop.vue';
import BusinessIcon from 'vue-ionicons/dist/ios-business';
import SidebarLink from "./SidebarLink";
import axios from "axios";
import Swal from 'sweetalert2'; 

export default {
    props: {
        pdfUrl: String,
    },
    components: {
        EarthIcon,
        DesktopIcon,
        SidebarLink,
        BusinessIcon,
    },
    data() {
        return {
            meetingConfig: [],
            createdMeetingNumber: null,
            pdfUrl: null,
            contractStatus: null,
            errorMessage: null,
        };
    },
    mounted() {
        this.fetchContractStatus();
    },
    methods: {
        showContractPopup() {
            Swal.fire({
                title: 'توثيق العقد',
                text: 'يرجى توثيق العقد للمتابعة.',
                icon: 'info',
                confirmButtonText: 'توثيق العقد',
                allowOutsideClick: false,  
                allowEscapeKey: false,    
                showCancelButton: false,  
                customClass: {
                    confirmButton: 'bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded',
                },
            }).then((result) => {
                if (result.isConfirmed) {
                    this.sendEmbeddedContract();
                    window.location.href=route("contract-guides");
                }
            });
        },

        async sendEmbeddedContract() {
            try {
                const recipientName = this.$page.props.user.name;
                const recipientEmail = this.$page.props.user.email;
                const userId = this.$page.props.user.id;

                const response = await axios.post('/send-embedded-contract', {
                    recipient_name: recipientName,
                    recipient_email: recipientEmail,
                    user_id: userId,
                });

                if (response.data && response.data.sign_url) {
                    window.location.href = response.data.sign_url; 
                } else {
                    Swal.fire('خطأ', 'حدث خطأ أثناء معالجة الطلب.', 'error');
                }
            } catch (error) {
                console.error("خطأ في الاتصال: ", error);
                Swal.fire('خطأ', 'حدث خطأ أثناء إرسال العقد.', 'error');
            }
        },

        async viewContract() {
            try {
                const response = await axios.get('/zoho/view-contract');
                const pdfUrl = response.data.pdf_url;
                window.open(pdfUrl, '_blank');
            } catch (error) {
                console.error("خطأ أثناء تحميل العقد:", error);
            }
        },

     async fetchContractStatus() {
    try {
        console.log("heeeereeeeeee");
        const response = await axios.get(route('zoho.check-contract-status'));
        this.contractStatus = response.data.status;
                        console.log(this.contractStatus);

        this.errorMessage = null;

        if (this.contractStatus !== 'completed') {
            // this.showContractPopup();
        }
    } catch (error) {
        this.errorMessage = error.response?.data?.error || "حدث خطأ أثناء جلب حالة العقد.";
    }
}

    },
};
</script>

<style lang="sass">
    //
</style>
