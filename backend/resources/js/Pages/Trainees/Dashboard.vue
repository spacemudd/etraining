<template>
    <app-layout>
        <div class="container px-6 mx-auto grid">

            <!-- Payment notice -->
            <div class="container mx-auto grid p-6" v-if="user.trainee.has_outstanding_amount">
                <div class="bg-red-100 rounded-lg p-10 border-red-500 border-2">
                    <p class="text-red-800 flex">
                        <svg style="margin-left:10px;" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ $t('words.due-balance-notice') }}
                    </p>
                    <inertia-link class="text-center mt-5 inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-red-700 active:bg-red-900 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150 disabled:cursor-not-allowed mx-"
                                  :href="route('trainees.payment.options')">
                        {{ $t('words.settle') }}
                    </inertia-link>

                    <inertia-link class="text-center mt-5 inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-red-700 active:bg-red-900 border border-transparent rounded-md font-semibold text-xs text-white uppercase ltr:tracking-widest focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150 disabled:cursor-not-allowed mx-"
                                  :href="route('trainees.payment.options')">
                        {{ $t('words.attach-receipt') }}
                    </inertia-link>
                </div>
            </div>

            <div class="container mx-auto grid p-6">
                <div class="bg-white rounded-lg p-10 flex gap-10 border-1 border-emerald-500"
                     style="border-top: 20px solid rgb(32 161 2);background: #eff1ef;">
                    <div style="width: 100%;">
                        <p class="mt-2 text-gray-500" style="text-align: center;
    font-size: 20px;
    color: #323232;
    letter-spacing: 1px;
}">تم الدفع بنجاح!</p>
                    </div>
                </div>
            </div>

            <div class="container mx-auto grid p-6">
                <div class="bg-white rounded-lg p-10 flex gap-10">
                    <img src="/img/student.svg" alt="student" class="h-20">
                    <div>
                        <h1 class="text-2xl font-heavy">{{ $t('words.welcome') }}!</h1>
                        <p class="mt-2 text-gray-500">{{ user.email }}</p>
                        <p class="mt-2 text-xs text-gray-500">{{ $t('words.last-login-at') }}: <span dir="ltr">{{ user.last_login_at_timezone }}</span></p>
                    </div>
                </div>
            </div>

            <!-- Instructor's upcoming sessions. -->
            <div class="container px-6 mx-auto grid pt-6" v-if="!user.trainee.deleted_at">
                <div v-for="session in sessions.data"
                     :key="session.id">
                    <div class="bg-white my-5 p-5 flex gap-6">
                        <div class="w-32">
                            <div class="w-full h-full bg-red rounded-lg">
                                <div class="bg-gray-200 rounded-lg flex items-center align-middle justify-center"
                                     style="display: block;width:100px;height:100px;">
                                    <!--<p class="align-middle">{{ $t('words.course') }}</p>-->
                                </div>
                                <!--<img class="rounded-lg" src="https://source.unsplash.com/300x300/?training,classroom" alt="">-->
                            </div>
                        </div>

                        <div class="flex flex-col justify-center w-full">
                            <p class="font-bold text-lg py-2 text-center md:rtl:text-right md:ltr:text-left">{{ session.course_batch.course.name_ar }}</p>
                            <div class="my-3">
                                <ion-icon name="calendar-outline" class="inline-block w-4 h-4 fill-gray-400"></ion-icon>
                                {{ session.starts_at_timezone | toDate }}
                                -
                                <ion-icon name="time-outline" class="inline-block w-4 h-4 fill-gray-500"></ion-icon>
                                <span class="font-bold text-gray-500" dir="ltr">{{ session.starts_at_timezone | toHours }}</span>
                            </div>
                            <div>
                                <ion-icon name="calendar-outline" class="inline-block w-4 h-4 fill-gray-400"></ion-icon>
                                {{ session.ends_at_timezone | toDate }}
                                -
                                <ion-icon name="time-outline" class="inline-block w-4 h-4 fill-gray-500"></ion-icon>
                                <span class="font-bold text-gray-500" dir="ltr">{{ session.ends_at_timezone | toHours }}</span>
                            </div>
                            <p v-if="session.course_batch.course.instructor" class="text-sm">
                                {{ $t('words.provided-by') }}:
                                <span >{{ session.course_batch.course.instructor.name }}</span>
                            </p>
                            <div class="mt-5 flex gap-3 flex-col md:flex-row">
                                <!-- Course options -->
                                <a target="_blank"
                                   :href="session.course_batch.course.training_package_url"
                                   class="text-xs bg-yellow-200 py-3 px-6 rounded-lg font-bold hover:bg-yellow-300">
                                    {{ $t('words.training-package') }}
                                </a>

                                <!-- When the course is online -->
                                <button v-if="!session.can_join"
                                        class="btn-disabled"
                                        :disabled="!session.can_join">

                                    {{ $t('words.join-the-online-course') }}
                                </button>
                                <template v-else>
                                    <a target="_blank"
                                       v-if="session.course_batch.location_at === 'online'"
                                       class="text-xs bg-yellow-200 py-3 px-6 rounded-lg font-bold hover:bg-yellow-300 disabled:bg-gray-500"
                                       :href="route('trainees.course-batch-session.show', {course_id: session.course_id, course_batch_id: session.course_batch_id, course_batch_session: session.id})"
                                    >
                                        {{ $t('words.join-the-online-course') }}
                                    </a>
                                    <a v-else target="_blank" :href="session.course_batch.location_at" class="text-xs bg-blue-300 py-3 px-6 rounded-lg font-bold hover:bg-blue-400">
                                        {{ session.course_batch.location_at }}
                                    </a>
                                </template>

                                <!--<button class="btn-disabled" disabled>{{ $t('words.print-attendance') }}</button>-->
                                <!--<button class="btn-disabled" disabled>{{ $t('words.print-certificate') }}</button>-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayoutTrainee'
    import Welcome from '@/Jetstream/Welcome'
    import LanguageSelector from "@/Shared/LanguageSelector";
    import HeaderCard from "@/Components/HeaderCard";

    export default {
        props: ['sessions', 'user'],
        components: {
            AppLayout,
            Welcome,
            LanguageSelector,
            HeaderCard,
        },
        data() {
            return {
                checkCoursesEnabledInterval: null,
            }
        },
        filters: {
            toDate(timestamp) {
                return moment(timestamp, 'YYYY-MM-DD LT').format('DD-MM-YYYY');
            },
            toHours(timestamp) {
                return moment(timestamp, 'YYYY-MM-DD LT').format('hh:mm A');
            }
        },
        mounted() {
            let vm = this;
            this.checkCoursesEnabledInterval = setInterval(function() {
                vm.updateCoursesEnabled();
            }, 2000)
        },
        methods: {
            updateCoursesEnabled() {
                this.sessions.data.forEach((session, index) => {

                    let accessibleAt = moment(session.starts_at);
                    let disableAccess = moment(session.ends_at);

                    let canTheUserJoin = moment().isBetween(accessibleAt, disableAccess);

                    if (canTheUserJoin) {
                        this.$set(this.sessions.data[index], 'can_join', true);
                    } else {
                        this.$set(this.sessions.data[index], 'can_join', false);
                    }
                })
            },
        },
        beforeDestroy() {
            clearInterval(this.checkCoursesEnabledInterval);
        }
    }
</script>
