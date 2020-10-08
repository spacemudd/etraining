<template>
    <li class="relative px-6 py-3">
    <span class="absolute inset-y-0 sidebar-highlight w-1 bg-red-600 ltr:rounded-tr-lg rtl:rounded-tl-lg ltr:rounded-br-lg rtl:rounded-bl-lg"
        aria-hidden="true"></span>
        <inertia-link
            class="inline-flex items-center w-full text-sm font-semibold text-gray-800 transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 dark:text-gray-100"
            :href="linkValue">
            <slot name="icon"/>
            <slot name="title"></slot>
        </inertia-link>
    </li>
</template>

<script>
    export default {
        props: {
            linkValue: {
                default: '',
            }
        }
    }
</script>
