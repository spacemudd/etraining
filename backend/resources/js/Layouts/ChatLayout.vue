<template>
    <div class="h-screen w-full overflow-hidden bg-gray-50">
        <slot />
        <portal-target name="app-modal-container"></portal-target>
    </div>
</template>

<script>
export default {
    name: 'ChatLayout',
};
</script>
