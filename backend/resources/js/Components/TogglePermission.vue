<!--
  - Copyright (c) 2020 - Clarastars, LLC  - All Rights Reserved.
  -
  - Unauthorized copying of this file via any medium is strictly prohibited.
  - This file is a proprietary of Clarastars LLC and is confidential / educational purpose only.
  -
  - https://clarastars.com - info@clarastars.com
  - @author Shafiq al-Shaar <shafiqalshaar@gmail.com>
  -->

<style>
.toggle__dot {
    top: -.25rem;
    left: -.25rem;
    transition: all 0.3s ease-in-out;
}

[dir=rtl] .toggle__dot {
    left: unset;
    right: -.25rem;
}

input:checked ~ .toggle__dot {
    transform: translateX(100%);
    background-color: #48bb78;
    border: none;
}

[dir=rtl] input:checked ~ .toggle__dot {
    transform: translateX(-100%);
}

input:checked ~ .toggle__line {
    background-color: #95e9b9;
}
</style>

<template>
    <div class="field">
        <!-- Toggle Button -->
        <label :for="permissionName" class="flex items-center cursor-pointer">
            <!-- toggle -->
            <div class="relative"  @click.prevent="toggle">
                <!-- input -->
                <input :id="permissionName" type="checkbox" class="hidden" v-model="enabled" />
                <!-- line -->
                <div
                    class="toggle__line w-10 h-4 bg-gray-400 rounded-full shadow-inner"
                ></div>
                <!-- dot -->
                <div
                    class="toggle__dot absolute w-6 h-6 bg-white rounded-full shadow left-0 border-2"
                ></div>
            </div>
            <!-- label -->
            <div class="mx-10 text-gray-700 font-medium">
                <slot></slot>
            </div>
        </label>

    </div>
</template>

<script>
export default {
    props: {
        roleId: {
            type: String,
            required: true,
        },
        permissionName: {
            type: String,
            required: true,
        },
        enabledProp: {
            type: Boolean,
            required: true,
        },
    },
    data() {
        return {
            enabled: false,
            isLoading: false,
        }
    },
    mounted() {
        this.enabled = this.enabledProp;
    },
    methods: {
        toggle() {
            if(!this.isLoading) {
                if(this.enabled) {
                    this.detachPermission();
                } else {
                    this.attachPermission();
                }
            }
        },
        attachPermission() {
            this.isLoading = true;
            this.enabled = true; // We assume it gets enabled. For UX speed. ;)
            axios.post(route('back.settings.roles.attach-permission'), {
                role_id: this.roleId,
                permission_name: this.permissionName,
            }).then(() => {
                this.isLoading = false;
                this.enabled = true;
            }).catch(error => {
                this.enabled = false;
                this.isLoading = false;
            });
        },
        detachPermission() {
            this.isLoading = true;
            this.enabled = false; // We assume it gets disabled. For UX speed. ;)
            axios.post(route('back.settings.roles.detach-permission'), {
                role_id: this.roleId,
                permission_name: this.permissionName,
            }).then(() => {
                this.isLoading = false;
                this.enabled = false;
            }).catch(error => {
                this.enabled = true;
                this.isLoading = false;
            });
        }
    }
}
</script>
