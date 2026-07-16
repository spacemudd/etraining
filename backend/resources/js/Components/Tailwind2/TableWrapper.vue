<template>
  <div class="flex flex-col min-w-0 max-w-full">
    <div class="overflow-x-auto -mx-1 px-1 table-scroll">
      <div class="py-2 align-middle min-w-full">
        <div class="shadow border-b border-gray-200 relative">
          <slot />
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.table-scroll::-webkit-scrollbar {
  height: 8px;
}

.table-scroll::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 4px;
}

.table-scroll::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 4px;
}

.table-scroll::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}
</style>
