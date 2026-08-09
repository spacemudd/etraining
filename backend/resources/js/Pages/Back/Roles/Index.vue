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
        <div class="container px-6 mx-auto pt-6 pb-10">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'settings', link: route('back.settings')},
                    {title: 'roles', link: route('back.settings.roles.index')},
                ]"
            ></breadcrumb-container>

            <div class="flex flex-col lg:flex-row gap-4 min-h-[70vh]">
                <!-- Roles list -->
                <aside class="lg:w-80 shrink-0 bg-white rounded-lg shadow border border-gray-100 overflow-hidden flex flex-col">
                    <div class="px-4 py-3 border-b bg-gray-50">
                        <h2 class="font-bold text-gray-800">{{ $t('words.roles') }}</h2>
                        <p class="text-xs text-gray-500 mt-1">{{ $t('words.roles-pick-hint') }}</p>
                    </div>
                    <div class="overflow-y-auto flex-1 divide-y">
                        <button
                            v-for="role in rolesOrdered"
                            :key="role.id"
                            type="button"
                            @click="selectRole(role.id)"
                            class="w-full text-left px-4 py-3 hover:bg-gray-50 transition"
                            :class="selectedRoleId === role.id ? 'bg-blue-50 border-l-4 border-blue-600' : ''"
                        >
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <p class="font-semibold text-sm text-gray-900 truncate">{{ role.display_name }}</p>
                                    <p class="text-[11px] text-gray-500 mt-1">{{ role.role_description }}</p>
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-2 mt-2 text-[11px]">
                                <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-700">
                                    {{ role.users_count || 0 }} {{ $t('words.users') }}
                                </span>
                                <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-700">
                                    {{ (role.permissions || []).length }} {{ $t('words.permissions') }}
                                </span>
                            </div>
                        </button>
                    </div>
                </aside>

                <!-- Role details -->
                <section class="flex-1 bg-white rounded-lg shadow border border-gray-100 min-w-0 flex flex-col">
                    <div v-if="!selectedRole" class="flex-1 flex items-center justify-center text-gray-400 p-10 text-center">
                        {{ $t('words.roles-pick-hint') }}
                    </div>

                    <template v-else>
                        <div class="px-5 py-4 border-b flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <h1 class="text-xl font-bold text-gray-900">{{ selectedRole.display_name }}</h1>
                                <p class="text-sm text-gray-500 mt-1">{{ selectedRole.role_description }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button
                                    type="button"
                                    @click="activeTab = 'permissions'"
                                    class="px-3 py-1.5 rounded text-xs font-semibold"
                                    :class="activeTab === 'permissions' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                >
                                    {{ $t('words.permissions') }}
                                    ({{ enabledCount }}/{{ permissions.length }})
                                </button>
                                <button
                                    type="button"
                                    @click="activeTab = 'users'"
                                    class="px-3 py-1.5 rounded text-xs font-semibold"
                                    :class="activeTab === 'users' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                >
                                    {{ $t('words.users') }}
                                    ({{ (selectedRole.users || []).length }})
                                </button>
                            </div>
                        </div>

                        <!-- Permissions tab -->
                        <div v-if="activeTab === 'permissions'" class="flex-1 flex flex-col min-h-0">
                            <div class="px-5 py-3 border-b bg-gray-50 space-y-3">
                                <div class="flex flex-col sm:flex-row gap-2 sm:items-center">
                                    <input
                                        v-model="permissionSearch"
                                        type="search"
                                        class="form-input text-sm rounded-lg border-gray-300 flex-1"
                                        :placeholder="$t('words.search-permissions')"
                                    />
                                    <div class="flex flex-wrap gap-2" v-if="canEditPermissions">
                                        <button
                                            type="button"
                                            @click="enableFiltered"
                                            class="px-3 py-1.5 text-xs font-semibold rounded border bg-white hover:bg-gray-100 text-gray-700"
                                        >
                                            {{ $t('words.enable-filtered') }}
                                        </button>
                                        <button
                                            type="button"
                                            @click="disableFiltered"
                                            class="px-3 py-1.5 text-xs font-semibold rounded border bg-white hover:bg-gray-100 text-gray-700"
                                        >
                                            {{ $t('words.disable-filtered') }}
                                        </button>
                                        <button
                                            type="button"
                                            @click="savePermissions"
                                            :disabled="!isDirty || saving"
                                            class="px-3 py-1.5 text-xs font-semibold rounded bg-green-600 hover:bg-green-700 text-white disabled:opacity-40"
                                        >
                                            {{ saving ? ($t('words.loading') + '...') : $t('words.save-permissions') }}
                                        </button>
                                    </div>
                                </div>
                                <p v-if="isDirty" class="text-xs text-amber-700">{{ $t('words.unsaved-permission-changes') }}</p>
                                <p v-if="saveMessage" class="text-xs" :class="saveError ? 'text-red-600' : 'text-green-700'">{{ saveMessage }}</p>
                            </div>

                            <div class="overflow-y-auto flex-1 p-4 space-y-4">
                                <div
                                    v-for="group in permissionGroups"
                                    :key="group.key"
                                    class="border rounded-lg overflow-hidden"
                                >
                                    <div class="px-3 py-2 bg-gray-50 border-b flex items-center justify-between">
                                        <span class="text-xs font-bold text-gray-700 uppercase tracking-wide">
                                            {{ group.label }}
                                            <span class="font-normal text-gray-500">({{ group.items.length }})</span>
                                        </span>
                                        <div v-if="canEditPermissions" class="flex gap-2">
                                            <button type="button" class="text-[11px] text-blue-700 hover:underline" @click="setGroup(group.items, true)">
                                                {{ $t('words.all') }}
                                            </button>
                                            <button type="button" class="text-[11px] text-gray-600 hover:underline" @click="setGroup(group.items, false)">
                                                {{ $t('words.none') }}
                                            </button>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-0 divide-y md:divide-y-0">
                                        <label
                                            v-for="permission in group.items"
                                            :key="permission.name"
                                            class="flex items-start gap-2 px-3 py-2.5 hover:bg-gray-50 cursor-pointer border-b md:border-b border-gray-100"
                                            :class="{ 'opacity-60 cursor-not-allowed': !canEditPermissions }"
                                        >
                                            <input
                                                type="checkbox"
                                                class="form-checkbox mt-0.5 text-green-600"
                                                :checked="!!draftPermissions[permission.name]"
                                                :disabled="!canEditPermissions"
                                                @change="togglePermission(permission.name, $event.target.checked)"
                                            />
                                            <span class="min-w-0">
                                                <span class="block text-sm text-gray-800 leading-snug">{{ permission.display_name }}</span>
                                                <span class="block text-[10px] text-gray-400 mt-0.5" dir="ltr">{{ permission.name }}</span>
                                            </span>
                                        </label>
                                    </div>
                                </div>

                                <p v-if="permissionGroups.length === 0" class="text-sm text-gray-500 text-center py-8">
                                    {{ $t('words.no-results') }}
                                </p>
                            </div>
                        </div>

                        <!-- Users tab -->
                        <div v-else class="flex-1 flex flex-col min-h-0">
                            <div class="px-5 py-3 border-b bg-gray-50 flex items-center justify-between gap-3">
                                <p class="text-sm text-gray-600">
                                    {{ (selectedRole.users || []).length }} {{ $t('words.users') }}
                                </p>
                                <inertia-link
                                    v-if="selectedRole.can_manage_users"
                                    class="btn-gray text-sm"
                                    :href="route('back.settings.roles.users.invite', { id: selectedRole.id })"
                                >
                                    {{ $t('words.invite') }}
                                </inertia-link>
                            </div>

                            <div class="overflow-y-auto flex-1">
                                <table class="w-full text-sm">
                                    <thead class="bg-white sticky top-0">
                                        <tr class="text-left font-semibold text-gray-600 border-b">
                                            <th class="px-5 py-3">{{ $t('words.name') }}</th>
                                            <th class="px-5 py-3">{{ $t('words.email') }}</th>
                                            <th class="px-5 py-3"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr
                                            v-for="user in selectedRole.users || []"
                                            :key="user.id"
                                            class="border-b hover:bg-gray-50"
                                        >
                                            <td class="px-5 py-3">{{ user.name }}</td>
                                            <td class="px-5 py-3" dir="ltr">{{ user.email }}</td>
                                            <td class="px-5 py-3 text-right">
                                                <button
                                                    v-if="selectedRole.can_manage_users && user.id != $page.props.user.id"
                                                    type="button"
                                                    @click="deleteUser(user.id)"
                                                    class="bg-red-500 text-white text-xs font-semibold px-2 py-1 rounded"
                                                >
                                                    {{ $t('words.delete') }}
                                                </button>
                                            </td>
                                        </tr>
                                        <tr v-if="!(selectedRole.users || []).length">
                                            <td colspan="3" class="px-5 py-10 text-center text-gray-400">
                                                {{ $t('words.no-results') }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </template>
                </section>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout';
import BreadcrumbContainer from '@/Components/BreadcrumbContainer';
import axios from 'axios';

export default {
    metaInfo() {
        return {
            title: this.$t('words.roles'),
        };
    },
    components: {
        AppLayout,
        BreadcrumbContainer,
    },
    props: {
        roles: {
            type: Array,
            default: () => [],
        },
        permissions: {
            type: Array,
            default: () => [],
        },
        selectedRoleId: {
            type: String,
            default: null,
        },
        canEditPermissions: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        return {
            localSelectedRoleId: this.selectedRoleId || null,
            activeTab: 'permissions',
            permissionSearch: '',
            draftPermissions: {},
            baselinePermissions: {},
            saving: false,
            saveMessage: '',
            saveError: false,
            localRoles: this.roles || [],
        };
    },
    computed: {
        rolesOrdered() {
            return _.sortBy(this.localRoles, 'order');
        },
        selectedRole() {
            if (!this.localSelectedRoleId) {
                return null;
            }
            return this.rolesOrdered.find((role) => role.id === this.localSelectedRoleId) || null;
        },
        enabledCount() {
            return Object.keys(this.draftPermissions).filter((name) => this.draftPermissions[name]).length;
        },
        isDirty() {
            const names = this.permissions.map((p) => p.name);
            return names.some((name) => !!this.draftPermissions[name] !== !!this.baselinePermissions[name]);
        },
        filteredPermissions() {
            const q = (this.permissionSearch || '').trim().toLowerCase();
            if (!q) {
                return this.permissions;
            }
            return this.permissions.filter((permission) => {
                return (permission.display_name || '').toLowerCase().includes(q)
                    || (permission.name || '').toLowerCase().includes(q);
            });
        },
        permissionGroups() {
            const groupsMap = {};
            this.filteredPermissions.forEach((permission) => {
                const key = this.groupKeyFor(permission.name);
                if (!groupsMap[key]) {
                    groupsMap[key] = {
                        key,
                        label: this.groupLabel(key),
                        items: [],
                    };
                }
                groupsMap[key].items.push(permission);
            });

            return Object.values(groupsMap).sort((a, b) => a.label.localeCompare(b.label));
        },
    },
    watch: {
        selectedRole: {
            immediate: true,
            handler(role) {
                this.hydrateDraftFromRole(role);
            },
        },
    },
    mounted() {
        if (!this.localSelectedRoleId && this.rolesOrdered.length) {
            this.selectRole(this.rolesOrdered[0].id);
        }
    },
    methods: {
        selectRole(roleId) {
            if (this.isDirty && !window.confirm(this.$t('words.unsaved-permission-changes-confirm'))) {
                return;
            }
            this.localSelectedRoleId = roleId;
            this.activeTab = 'permissions';
            this.permissionSearch = '';
            this.saveMessage = '';
            this.saveError = false;
            if (window.history && window.history.replaceState) {
                const url = route('back.settings.roles.index', { role: roleId });
                window.history.replaceState({}, '', url);
            }
        },
        hydrateDraftFromRole(role) {
            const draft = {};
            (this.permissions || []).forEach((permission) => {
                draft[permission.name] = false;
            });
            ((role && role.permissions) || []).forEach((permission) => {
                draft[permission.name] = true;
            });
            this.draftPermissions = { ...draft };
            this.baselinePermissions = { ...draft };
        },
        togglePermission(name, enabled) {
            this.$set(this.draftPermissions, name, !!enabled);
            this.saveMessage = '';
        },
        setGroup(items, enabled) {
            items.forEach((permission) => {
                this.$set(this.draftPermissions, permission.name, !!enabled);
            });
            this.saveMessage = '';
        },
        enableFiltered() {
            this.setGroup(this.filteredPermissions, true);
        },
        disableFiltered() {
            this.setGroup(this.filteredPermissions, false);
        },
        async savePermissions() {
            if (!this.selectedRole || !this.canEditPermissions) {
                return;
            }

            this.saving = true;
            this.saveMessage = '';
            this.saveError = false;

            const selected = Object.keys(this.draftPermissions).filter((name) => this.draftPermissions[name]);

            try {
                const { data } = await axios.post(route('back.settings.roles.sync-permissions'), {
                    role_id: this.selectedRole.id,
                    permissions: selected,
                });

                const roleIndex = this.localRoles.findIndex((role) => role.id === this.selectedRole.id);
                if (roleIndex !== -1) {
                    const updated = {
                        ...this.localRoles[roleIndex],
                        permissions: data.role.permissions || [],
                    };
                    this.$set(this.localRoles, roleIndex, updated);
                }

                this.baselinePermissions = { ...this.draftPermissions };
                this.saveMessage = data.message || this.$t('words.permissions-saved');
            } catch (error) {
                this.saveError = true;
                this.saveMessage = (error.response && error.response.data && error.response.data.message)
                    || this.$t('words.permissions-save-failed');
            } finally {
                this.saving = false;
            }
        },
        deleteUser(userId) {
            if (!window.confirm(this.$t('words.delete') + '?')) {
                return;
            }
            this.$inertia.delete(route('back.settings.roles.users.delete', {
                role_id: this.selectedRole.id,
                user_id: userId,
            }));
        },
        groupKeyFor(name) {
            const value = (name || '').toLowerCase();
            if (value.includes('whatsapp') || value.includes('chat')) return 'whatsapp';
            if (value.includes('compan')) return 'companies';
            if (value.includes('trainee') || value.includes('gosi') || value.includes('resignation')) return 'trainees';
            if (value.includes('invoice') || value.includes('payment') || value.includes('finance') || value.includes('cost')) return 'finance';
            if (value.includes('course') || value.includes('attendance') || value.includes('instructor')) return 'courses';
            if (value.includes('permission') || value.includes('role') || value.includes('setting') || value.includes('website') || value.includes('survey') || value.includes('complaint')) return 'settings';
            if (value.includes('report') || value.includes('dashboard') || value.includes('order')) return 'reports';
            return 'other';
        },
        groupLabel(key) {
            const map = {
                whatsapp: this.$t('words.permission-group-whatsapp'),
                companies: this.$t('words.permission-group-companies'),
                trainees: this.$t('words.permission-group-trainees'),
                finance: this.$t('words.permission-group-finance'),
                courses: this.$t('words.permission-group-courses'),
                settings: this.$t('words.permission-group-settings'),
                reports: this.$t('words.permission-group-reports'),
                other: this.$t('words.permission-group-other'),
            };
            return map[key] || key;
        },
    },
};
</script>
