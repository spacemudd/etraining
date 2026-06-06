<template>
  <div class="mb-8">
    <div
      class="rounded-lg border p-4 mb-6"
      :class="readiness.ready_for_engineers ? 'border-green-200 bg-green-50' : 'border-amber-200 bg-amber-50'"
    >
      <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
          <p class="text-sm font-semibold text-gray-800">
            {{ $t("words.recorded-course-setup-progress") }}
          </p>
          <p
            class="text-sm mt-1"
            :class="readiness.ready_for_engineers ? 'text-green-800' : 'text-amber-900'"
          >
            {{
              readiness.ready_for_engineers
                ? $t("words.recorded-course-ready-for-engineers")
                : $t("words.recorded-course-not-ready-for-engineers")
            }}
          </p>
        </div>
        <div class="text-xs text-gray-600">
          {{ $t("words.recorded-course-videos-ready-count", {
            ready: readiness.lessons_with_video_count,
            total: readiness.lessons_count,
          }) }}
        </div>
      </div>
    </div>

    <nav class="flex flex-wrap gap-2" aria-label="Course setup steps">
      <inertia-link
        v-for="step in steps"
        :key="step.id"
        :href="step.href"
        class="inline-flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium border transition-colors"
        :class="stepClass(step)"
      >
        <span
          class="inline-flex h-5 w-5 items-center justify-center rounded-full text-xs"
          :class="stepBadgeClass(step)"
        >
          <span v-if="step.complete">✓</span>
          <span v-else>{{ step.number }}</span>
        </span>
        <span>{{ $t("words." + step.labelKey) }}</span>
      </inertia-link>
    </nav>
  </div>
</template>

<script>
export default {
  name: "RecordedCourseStepNav",
  props: {
    courseId: {
      type: String,
      required: true,
    },
    currentStep: {
      type: String,
      required: true,
    },
    readiness: {
      type: Object,
      required: true,
    },
  },
  computed: {
    steps() {
      const id = this.courseId;
      return [
        {
          id: "overview",
          number: 1,
          labelKey: "recorded-course-step-overview",
          href: this.route("back.settings.recorded-courses.show", id),
          complete: this.readiness.details_complete && this.readiness.schedule_complete,
        },
        {
          id: "details",
          number: 2,
          labelKey: "recorded-course-step-details",
          href: this.route("back.settings.recorded-courses.details.edit", id),
          complete: this.readiness.details_complete,
        },
        {
          id: "schedule",
          number: 3,
          labelKey: "recorded-course-step-schedule",
          href: this.route("back.settings.recorded-courses.schedule.edit", id),
          complete: this.readiness.schedule_complete,
        },
        {
          id: "lessons",
          number: 4,
          labelKey: "recorded-course-step-lessons",
          href: this.route("back.settings.recorded-courses.lessons.index", id),
          complete: this.readiness.all_lessons_have_video,
        },
        {
          id: "enrollments",
          number: 5,
          labelKey: "recorded-course-step-enrollments",
          href: this.route("back.settings.recorded-courses.enrollments.index", id),
          complete: (this.readiness.enrollments_count || 0) > 0,
        },
      ];
    },
  },
  methods: {
    stepClass(step) {
      if (step.id === this.currentStep) {
        return "border-indigo-600 bg-indigo-50 text-indigo-800";
      }
      return "border-gray-200 bg-white text-gray-700 hover:bg-gray-50";
    },
    stepBadgeClass(step) {
      if (step.complete) {
        return "bg-green-600 text-white";
      }
      if (step.id === this.currentStep) {
        return "bg-indigo-600 text-white";
      }
      return "bg-gray-200 text-gray-700";
    },
  },
};
</script>
