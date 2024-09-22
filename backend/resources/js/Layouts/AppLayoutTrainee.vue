<style lang="css">
.whatsapp-bubble-head {
    position: fixed;
    bottom: 20px;
    left: 20px;
}
</style>
<template>
    <div
        class="flex h-screen bg-gray-50 dark:bg-gray-900"
        :class="{ 'overflow-hidden': isSideMenuOpen }"
    >
        <portal-target name="app-modal-container">
        </portal-target>

        <!-- Desktop sidebar -->
        <aside
            class="z-20 hidden w-64 overflow-y-auto bg-white dark:bg-gray-800 md:block flex-shrink-0"
        >
            <div class="py-4 text-gray-500 dark:text-gray-400">
                <!-- <inertia-link href="/dashboard" class="ltr:ml-6 rtl:mr-6 text-lg block w-100 text-center font-bold text-gray-800 dark:text-gray-200">
                    <jet-application-mark class="w-48" />
                </inertia-link> -->
                <sidebar-container-trainee/>
            </div>
        </aside>
        <!-- Mobile sidebar -->
        <!-- Backdrop -->
        <div
            v-show="isSideMenuOpen"
            x-transition:enter="transition ease-in-out duration-150"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in-out duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-10 flex items-end bg-black bg-opacity-50 sm:items-center sm:justify-center"
        ></div>
        <aside
            class="fixed inset-y-0 z-20 flex-shrink-0 w-64 mt-16 overflow-y-auto bg-white dark:bg-gray-800 md:hidden"
            v-show="isSideMenuOpen"
            x-transition:enter="transition ease-in-out duration-150"
            x-transition:enter-start="opacity-0 transform -translate-x-20"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in-out duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0 transform -translate-x-20"
            @click.away="closeSideMenu"
            @keydown.escape="closeSideMenu"
        >
            <div class="py-4 text-gray-500 dark:text-gray-400">
                <a
                    class="ml-6 text-lg font-bold text-gray-800 dark:text-gray-200"
                    href="#"
                >
                    <jet-application-mark/>
                </a>
                <ul class="mt-6">
                    <sidebar-link link-value="/dashboard" :active="$page.currentRouteName == 'dashboard'">
                        <template #icon><DesktopIcon w="20px" h="20px" class="w-5 h-5" /></template>
                        <template #title>
                            <span class="ltr:ml-4 rtl:mr-4">{{ $t('words.dashboard') }}</span>
                        </template>
                    </sidebar-link>

                    <sidebar-link :link-value="route('trainees.courses.index')" :active="$page.currentRouteName == 'trainees.courses.index'">
                        <template #icon>
                            <ion-icon name="business-outline" class="w-5 h-5"></ion-icon>
                        </template>
                        <template #title>
                            <span class="ltr:ml-4 rtl:mr-4">{{ $t('words.courses') }}</span>
                        </template>
                    </sidebar-link>

                    <sidebar-link :link-value="route('trainees.training-packages.index')" :active="$page.currentRouteName == 'trainees.training-packages.index'">
                        <template #icon>
                            <ion-icon name="albums-outline" class="w-5 h-5"></ion-icon>
                        </template>
                        <template #title>
                            <span class="ltr:ml-4 rtl:mr-4">{{ $t('words.training-packages') }}</span>
                        </template>
                    </sidebar-link>

                    <sidebar-link :link-value="route('trainees.training-plan.index')" :active="$page.currentRouteName == 'trainees.training-plan.index'">
                        <template #icon>
                            <ion-icon name="school-outline" class="w-5 h-5"></ion-icon>
                        </template>
                        <template #title>
                            <span class="ltr:ml-4 rtl:mr-4">{{ $t('words.training-plan') }}</span>
                        </template>
                    </sidebar-link>

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

                    <li class="pr-8 mt-2 text-xs"><inertia-link class="hover:text-blue-600" :href="route('survey.index')">الاستبيانات</inertia-link>
                    <li class="pr-8 mt-2 text-xs"><inertia-link class="hover:text-blue-600" :href="route('training-schedule.index')">الخطة التدريبية والجدول الزمني</inertia-link>
                    <li class="pr-8 mt-2 text-xs"><inertia-link class="hover:text-blue-600" :href="route('training-plan.index')">خطة تدريب المدربين</inertia-link>
                    <li class="pr-8 mt-2 text-xs"><inertia-link class="hover:text-blue-600" :href="route('user-guides.index')">الادلة الارشادية</inertia-link>
                    <li class="pr-8 mt-2 text-xs"><inertia-link class="hover:text-blue-600" :href="route('obligations.index')">الالتزامات</inertia-link>
                    <li class="pr-8 mt-2 text-xs"><inertia-link class="hover:text-blue-600" :href="route('management-roles.index')">دليل الكادر الاشرافي</inertia-link>
                    <li class="pr-8 mt-2 text-xs"><inertia-link class="hover:text-blue-600" :href="route('company-roles.index')">وثيقة الادوار والمسؤوليات</inertia-link>
                    <li class="pr-8 mt-2 text-xs"><inertia-link class="hover:text-blue-600" :href="route('suggestions.index')">للاقتراحات والاستفسارات</inertia-link>
                    <li class="pr-8 mt-2 text-xs"><inertia-link class="hover:text-blue-600" :href="route('complaints.index')">الشكاوى والملاحظات</inertia-link>
                    <li class="pr-8 mt-2 text-xs"><a class="hover:text-blue-600" href="/terms#attendance-policy">السياسة الاكاديمية والتدريبية</a>
                    <li class="pr-8 text-xs"><a class="hover:text-blue-600" href="/terms">{{ $t('words.support-policy') }}</a></li>
                </ul>
            </div>
        </aside>
        <div class="flex flex-col flex-1 w-full mb-10">
            <header class="z-10 py-4 bg-white shadow-md dark:bg-gray-800">
                <div
                    class="container flex items-center justify-between h-full px-6 mx-auto text-red-600 dark:text-red-300"
                >
                    <!-- Mobile hamburger -->
                    <button
                        class="p-1 ltr:mr-5 ltr:ml-1 rtl:ml-5 rtl:mr-1 rounded-md md:hidden focus:outline-none focus:shadow-outline-red"
                        @click="toggleSideMenu"
                        aria-label="Menu"
                    >
                        <svg
                            class="w-6 h-6"
                            aria-hidden="true"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                                clip-rule="evenodd"
                            ></path>
                        </svg>
                    </button>
                    <!-- Search input -->
                    <div class="flex justify-center flex-1 ltr:lg:mr-32 rtl:lg:ml-32">
                        <div
                            class="relative w-full max-w-xl ltr:mr-6 rtl:ml-6 focus-within:text-red-500"
                        >
                            <div class="absolute inset-y-0 flex items-center ltr:pl-2 rtl:pr-2">
                                <svg
                                    class="w-4 h-4"
                                    aria-hidden="true"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                        clip-rule="evenodd"
                                    ></path>
                                </svg>
                            </div>
                            <input
                                class="w-full ltr:pl-8 ltr:pr-2 rtl:pr-8 rtl:pl-8 text-sm text-gray-700 placeholder-gray-600 bg-gray-100 border-0 rounded-md dark:placeholder-gray-500 dark:focus:shadow-outline-gray dark:focus:placeholder-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:placeholder-gray-500 focus:bg-white focus:border-red-300 focus:outline-none focus:shadow-outline-red form-input"
                                type="text"
                                :placeholder="$t('words.search')"
                                aria-label="Search"
                            />
                        </div>
                    </div>
                    <language-selector></language-selector>
                    <ul class="flex items-center flex-shrink-0 space-x-6">
                        <!-- Profile menu -->
                        <li class="relative">
                            <button
                                class="align-middle rounded-full focus:shadow-outline-red focus:outline-none"
                                @click="toggleProfileMenu"
                                @keydown.escape="closeProfileMenu"
                                aria-label="Account"
                                aria-haspopup="true"
                            >
                                <img class="object-cover w-8 h-8 rounded-full" :src="$page.props.user.profile_photo_url" :alt="$page.props.user.name" aria-hidden="true" />
                            </button>
                            <template v-if="isProfileMenuOpen">
                                <ul
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100"
                                    x-transition:leave-end="opacity-0"
                                    @click.away="closeProfileMenu"
                                    @keydown.escape="closeProfileMenu"
                                    class="absolute right-0 w-56 p-2 mt-2 space-y-2 text-gray-600 bg-white border border-gray-100 rounded-md shadow-md dark:border-gray-700 dark:text-gray-300 dark:bg-gray-700"
                                    aria-label="submenu"
                                >
                                    <li class="flex">
                                        <a
                                            class="inline-flex items-center w-full px-2 py-1 text-sm font-semibold transition-colors duration-150 rounded-md hover:bg-gray-100 hover:text-gray-800 dark:hover:bg-gray-800 dark:hover:text-gray-200"
                                            href="#"
                                        >
                                            <svg
                                                class="w-4 h-4 ltr:mr-3 rtl:ml-3"
                                                aria-hidden="true"
                                                fill="none"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                                ></path>
                                            </svg>
                                            <span>{{ $t('words.profile') }}</span>
                                        </a>
                                    </li>
                                    <li class="flex">
                                        <a
                                            class="inline-flex items-center w-full px-2 py-1 text-sm font-semibold transition-colors duration-150 rounded-md hover:bg-gray-100 hover:text-gray-800 dark:hover:bg-gray-800 dark:hover:text-gray-200"
                                            href="#"
                                        >
                                            <ion-icon class="w-4 h-4 ltr:mr-3 rtl:ml-3" name="settings-outline"></ion-icon>
                                            <span>{{ $t('words.settings') }}</span>
                                        </a>
                                    </li>
                                    <li class="flex">
                                        <button @click="logoutUser" class="inline-flex items-center w-full px-2 py-1 text-sm font-semibold transition-colors duration-150 rounded-md hover:bg-gray-100 hover:text-gray-800 dark:hover:bg-gray-800 dark:hover:text-gray-200">
                                            <svg class="w-4 h-4 ltr:mr-3 rtl:ml-3"
                                                 aria-hidden="true"
                                                 fill="none"
                                                 stroke-linecap="round"
                                                 stroke-linejoin="round"
                                                 stroke-width="2"
                                                 viewBox="0 0 24 24"
                                                 stroke="currentColor">
                                                <path d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"
                                                ></path>
                                            </svg>
                                            <span>{{ $t('words.logout') }}</span>
                                        </button>
                                    </li>
                                </ul>
                            </template>
                        </li>
                    </ul>
                </div>
            </header>

            <main class="h-full overflow-y-auto">
                <div id="zoom-container"></div>
                <slot></slot>
            </main>
        </div>

        <div class="whatsapp-bubble-head">
            <a target="_blank"
                class="inline-flex items-center w-full text-sm text-gray-800 transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 dark:text-gray-100"
                href="https://api.whatsapp.com/send?phone=966553139979">
                <ion-icon name="logo-whatsapp" class="w-10 h-10 text-green-600"></ion-icon>
            </a>
        </div>
    </div>
</template>

<script>
    import JetApplicationLogo from './../Jetstream/ApplicationLogo'
    import JetApplicationMark from './../Jetstream/ApplicationMark'
    import JetDropdown from './../Jetstream/Dropdown'
    import JetDropdownLink from './../Jetstream/DropdownLink'
    import JetNavLink from './../Jetstream/NavLink'
    import JetResponsiveNavLink from './../Jetstream/ResponsiveNavLink'
    import LanguageSelector from "../Shared/LanguageSelector";
    import SidebarLink from '../Components/SidebarLink';
    import SidebarContainer from "../Components/SidebarContainer";
    import SidebarContainerTrainee from "@/Components/SidebarContainerTrainee";
    import DesktopIcon from 'vue-ionicons/dist/ios-desktop.vue'
    import BusinessIcon from 'vue-ionicons/dist/ios-business';

    export default {
        components: {
            SidebarContainerTrainee,
            JetApplicationLogo,
            JetApplicationMark,
            JetDropdown,
            JetDropdownLink,
            JetNavLink,
            JetResponsiveNavLink,
            LanguageSelector,
            SidebarLink,
            SidebarContainer,
            DesktopIcon,
            BusinessIcon,
        },

        data() {
            return {
                showingNavigationDropdown: false,
                isSideMenuOpen: false,
                isNotificationsMenuOpen: false,
                isProfileMenuOpen: false,
                isPagesMenuOpen: false,
                // Modal
                isModalOpen: false,
                trapCleanup: null,
            }
        },

        methods: {
            switchToTeam(team) {
                this.$inertia.put('/current-team', {
                    'team_id': team.id
                }, {
                    preserveState: false
                })
            },
            logoutUser() {
                axios.post('/logout').then(response => {
                    window.location = '/';
                })
            },
            toggleSideMenu() {
                this.isSideMenuOpen = !this.isSideMenuOpen
            },
            closeSideMenu() {
                this.isSideMenuOpen = false
            },
            toggleNotificationsMenu() {
                this.isNotificationsMenuOpen = !this.isNotificationsMenuOpen
            },
            closeNotificationsMenu() {
                this.isNotificationsMenuOpen = false
            },
            toggleProfileMenu() {
                this.isProfileMenuOpen = !this.isProfileMenuOpen
            },
            closeProfileMenu() {
                this.isProfileMenuOpen = false
            },
            togglePagesMenu() {
                this.isPagesMenuOpen = !this.isPagesMenuOpen
            },
            openModal() {
                this.isModalOpen = true
                // this.trapCleanup = focusTrap(document.querySelector('#modal'))
            },
            closeModal() {
                this.isModalOpen = false
                this.trapCleanup()
            },
        },

        computed: {
            path() {
                return window.location.pathname
            },
            selectable_locale() {
                if(this.$page.props.locale == 'ar') {
                    return 'en';
                }
                return 'ar'
            },
        }
    }
</script>
