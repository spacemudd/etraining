<template>
    <div>
        <div class="flex flex-col flex-1 w-full mb-10">
            <main class="h-full overflow-y-auto">
                <slot></slot>
            </main>
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
    import SidebarContainerInstructor from "../Components/SidebarContainerInstructor";

    export default {
        components: {
            SidebarContainerInstructor,
            JetApplicationLogo,
            JetApplicationMark,
            JetDropdown,
            JetDropdownLink,
            JetNavLink,
            JetResponsiveNavLink,
            LanguageSelector,
            SidebarLink,
            SidebarContainer,
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
                if(this.$page.locale == 'ar') {
                    return 'en';
                }
                return 'ar'
            },
        }
    }
</script>
