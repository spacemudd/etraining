<template>
    <!-- Limited view: Simple layout without sidebar, only search bar -->
    <div v-if="is_limited_view" class="min-h-screen bg-gray-50">
        <!-- Header with search bar -->
        <header class="z-10 py-4 bg-white shadow-md">
            <div class="container flex items-center justify-between h-full px-6 mx-auto text-red-600">
                <!-- Search input -->
                <div class="flex justify-center flex-1">
                    <admin-searchbar />
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
                                @keydown.escape="closeProfileMenu"
                                class="absolute right-0 w-56 p-2 mt-2 space-y-2 text-gray-600 bg-white border border-gray-100 rounded-md shadow-md z-50"
                                aria-label="submenu"
                                ref="profileMenu"
                            >
                                <li>
                                    <inertia-link :href="route('profile.show')" @click="closeProfileMenu" class="block px-4 py-2 text-sm transition-colors duration-150 hover:bg-gray-100">
                                        {{ $t('words.profile') }}
                                    </inertia-link>
                                </li>
                                <li>
                                    <button @click="logout" class="block w-full text-left px-4 py-2 text-sm transition-colors duration-150 hover:bg-gray-100">
                                        {{ $t('words.logout') }}
                                    </button>
                                </li>
                            </ul>
                        </template>
                    </li>
                </ul>
            </div>
        </header>

        <!-- Empty content area - only search is available -->
        <div class="container mx-auto px-6 py-8">
            <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-md p-8 text-center">
                <h1 class="text-2xl font-bold mb-4 text-gray-800">{{ $t('words.welcome') }}</h1>
                <p class="text-gray-600">{{ $t('words.use-search-to-find-trainees') }}</p>
            </div>
        </div>
    </div>

    <!-- Full view: Normal layout with sidebar -->
    <app-layout v-else>
        <div class="container px-6 mx-auto grid">
            <!--<h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">-->
            <!--    {{ $t('words.dashboard') }}-->
            <!--</h2>-->
            <!-- Cards -->
            <div class="grid gap-6 mb-8 md:grid-cols-2 xl:grid-cols-4 mt-6" v-can="'view-dashboard-counters'">
                <!-- Card -->
                <header-card :href="route('back.companies.index')" :title-value="$t('words.companies')" :count-value="companies_count" icon-path="/img/building.svg"></header-card>
                <header-card :href="route('back.instructors.index')" :title-value="$t('words.instructors')" :count-value="instructors_count" icon-path="/img/teacher.svg"></header-card>
                <header-card-trainees :href="route('back.trainees.index')" :title-value="$t('words.trainees')"
                                      :count-value="trainees_count"
                                      :candidates-count="trainees_candidates_count"
                                      :approved-count="trainees_approved_count"
                                      :incomplete-count="trainees_incomplete_count"
                                      icon-path="/img/student.svg"></header-card-trainees>
                <header-card :href="route('back.courses.index')" :title-value="$t('words.courses')" :count-value="courses_count" icon-path="/img/book.svg"></header-card>
            </div>

            <!-- Quick actions actions -->
            <h2 class="my-6 font-semibold text-gray-700 dark:text-gray-200 border-b pb-1">
                {{ $t('words.quick-actions') }}
            </h2>

            <div class="grid gap-6 mb-8 md:grid-cols-2 xl:grid-cols-4">
                <inertia-link :href="route('back.companies.create')" class="shadow-lg flex items-center p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800 text-center hover:bg-red-500 hover:text-red-600 hover:font-semibold hover:shadow-xl">
                    {{ $t('words.add-new-company') }}
                </inertia-link>
                <inertia-link :href="route('back.instructors.create')" class="shadow-lg flex items-center p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800 text-center hover:bg-red-500 hover:text-red-600 hover:font-semibold hover:shadow-xl">
                    {{ $t('words.add-new-instructor') }}
                </inertia-link>
                <inertia-link :href="route('back.trainees.create')" class="shadow-lg flex items-center p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800 text-center hover:bg-red-500 hover:text-red-600 hover:font-semibold hover:shadow-xl">
                    {{ $t('words.add-new-trainee') }}
                </inertia-link>
                <inertia-link :href="route('back.courses.create')" class="shadow-lg flex items-center p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800 text-center hover:bg-red-500 hover:text-red-600 hover:font-semibold hover:shadow-xl">
                    {{ $t('words.add-new-course') }}
                </inertia-link>
            </div>

            <br>
            <h2 class="my-6 font-semibold text-gray-700 dark:text-gray-200 border-b pb-1">
                {{ $t('words.employees') }}
            </h2>

            <div class="grid gap-6 mb-8 md:grid-cols-2 xl:grid-cols-4">
                <inertia-link :href="route('orders.index')" class="shadow-lg flex items-center p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800 text-center hover:bg-red-500 hover:text-red-600 hover:font-semibold hover:shadow-xl">
                    {{ $t('words.apply') }}
                </inertia-link>
            </div>
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from './../Layouts/AppLayout'
    import Welcome from './../Jetstream/Welcome'
    import LanguageSelector from "../Shared/LanguageSelector";
    import HeaderCard from "../Components/HeaderCard";
    import HeaderCardTrainees from "../Components/HeaderCardTrainees";
    import AdminSearchbar from "@/Components/AdminSearchbar";

    export default {
        props: [
            'companies_count',
            'instructors_count',
            'trainees_count',
            'courses_count',
            'trainees_candidates_count',
            'trainees_approved_count',
            'trainees_incomplete_count',
            'is_limited_view',
        ],
        components: {
            AppLayout,
            Welcome,
            LanguageSelector,
            HeaderCard,
            HeaderCardTrainees,
            AdminSearchbar,
        },
        data() {
            return {
                isProfileMenuOpen: false,
            }
        },
        mounted() {
            // Add click outside listener for profile menu
            document.addEventListener('click', this.handleClickOutside);
        },
        beforeDestroy() {
            // Remove click outside listener
            document.removeEventListener('click', this.handleClickOutside);
        },
        methods: {
            toggleProfileMenu() {
                this.isProfileMenuOpen = !this.isProfileMenuOpen;
            },
            closeProfileMenu() {
                this.isProfileMenuOpen = false;
            },
            handleClickOutside(event) {
                if (this.isProfileMenuOpen && this.$refs.profileMenu) {
                    // Check if click is not on the profile menu or profile button
                    const profileButton = event.target.closest('button[aria-label="Account"]');
                    const profileMenu = this.$refs.profileMenu;
                    
                    if (!profileMenu.contains(event.target) && !profileButton) {
                        this.closeProfileMenu();
                    }
                }
            },
            logout() {
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                const token = csrfToken ? csrfToken.getAttribute('content') : '';
                
                axios.post('/logout', {}, {
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }).then(response => {
                    window.location = '/';
                }).catch(error => {
                    console.error('Logout error:', error);
                    // Fallback: try direct redirect even on error
                    window.location = '/';
                });
            },
        },
    }
</script>
