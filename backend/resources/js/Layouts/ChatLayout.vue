<template>
    <div class="chat-layout-root h-screen w-full overflow-hidden bg-gray-50">
        <slot />
        <portal-target name="app-modal-container"></portal-target>
    </div>
</template>

<script>
export default {
    name: 'ChatLayout',
};
</script>

<style scoped>
.chat-layout-root {
    height: 100vh;
    height: 100dvh;
    padding-top: env(safe-area-inset-top, 0px);
    padding-bottom: env(safe-area-inset-bottom, 0px);
    box-sizing: border-box;
}
</style>
