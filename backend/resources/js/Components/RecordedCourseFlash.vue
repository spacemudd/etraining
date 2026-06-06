<template>
  <div>
    <div
      v-if="$page.props.flash && $page.props.flash.success"
      class="mb-4 p-3 bg-green-100 text-green-800 rounded"
    >
      {{ $page.props.flash.success }}
    </div>
    <div
      v-if="$page.props.flash && $page.props.flash.warning"
      class="mb-4 p-3 bg-yellow-100 text-yellow-800 rounded"
    >
      {{ $page.props.flash.warning }}
    </div>
  </div>
</template>

<script>
export default {
  name: "RecordedCourseFlash",
};
</script>
