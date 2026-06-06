<template>
  <app-layout>
    <div class="container px-6 mx-auto grid pt-6 max-w-6xl">
      <breadcrumb-container :crumbs="breadcrumbs" />
      <recorded-course-flash />

      <h1 class="font-bold text-2xl text-gray-900 mb-1">{{ courseTitle }}</h1>
      <p class="text-sm text-gray-600 mb-6">
        {{ $t("words.recorded-course-enrollments-progress") }}
      </p>

      <recorded-course-step-nav
        :course-id="recordedCourse.id"
        current-step="enrollments"
        :readiness="readiness"
      />

      <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200 text-sm text-gray-700">
        {{ $t("words.recorded-course-enrollments-hint-from-trainee") }}
      </div>

      <div class="bg-white rounded shadow overflow-x-auto">
        <template v-if="enrollments.length">
          <table class="min-w-full text-sm border border-gray-200 rounded-lg overflow-hidden">
            <thead class="bg-gray-50 text-left rtl:text-right">
              <tr>
                <th class="border-b border-gray-200 px-3 py-2 font-semibold text-gray-700">
                  {{ $t("words.name") }}
                </th>
                <th class="border-b border-gray-200 px-3 py-2 font-semibold text-gray-700 whitespace-nowrap">
                  {{ $t("words.recorded-course-enrolled-at") }}
                </th>
                <th
                  v-for="lesson in lessons"
                  :key="'h-' + lesson.id"
                  class="border-b border-gray-200 px-2 py-2 font-semibold text-gray-700 text-center min-w-[7rem]"
                >
                  <span class="line-clamp-2">{{ lesson.title_en || lesson.title_ar }}</span>
                </th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="row in enrollments"
                :key="row.id"
                class="border-b border-gray-100 last:border-0"
              >
                <td class="px-3 py-2 text-gray-900">
                  {{ row.trainee_name || row.trainee_id }}
                </td>
                <td class="px-3 py-2 text-gray-600 whitespace-nowrap text-xs">
                  {{ row.enrolled_at }}
                </td>
                <td
                  v-for="(lp, idx) in row.lesson_progress"
                  :key="row.id + '-' + (lp.lesson_id || idx)"
                  class="px-2 py-2 text-center text-xs border-l border-gray-100"
                >
                  <span v-if="lp.completed_at" class="text-green-700 font-medium">{{
                    $t("words.recorded-course-progress-done")
                  }}</span>
                  <span v-else-if="lp.unlocked_at" class="text-indigo-700 font-medium">{{
                    $t("words.recorded-course-progress-unlocked")
                  }}</span>
                  <span v-else class="text-gray-400">—</span>
                </td>
              </tr>
            </tbody>
          </table>
        </template>
        <p v-else class="px-6 py-8 text-sm text-gray-500">
          {{ $t("words.nothing-is-here") }}
        </p>
      </div>

      <div class="mt-6">
        <inertia-link
          class="btn-gray"
          :href="route('back.settings.recorded-courses.show', recordedCourse.id)"
        >
          {{ $t("words.go-back") }}
        </inertia-link>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import BreadcrumbContainer from "@/Components/BreadcrumbContainer";
import RecordedCourseFlash from "@/Components/RecordedCourseFlash";
import RecordedCourseStepNav from "@/Components/RecordedCourseStepNav";

export default {
  metaInfo: { title: "Course enrollments" },
  components: {
    AppLayout,
    BreadcrumbContainer,
    RecordedCourseFlash,
    RecordedCourseStepNav,
  },
  props: {
    recordedCourse: { type: Object, required: true },
    readiness: { type: Object, required: true },
    lessons: { type: Array, default: () => [] },
    enrollments: { type: Array, default: () => [] },
  },
  computed: {
    locale() {
      return this.$page.props.locale || "ar";
    },
    courseTitle() {
      return this.locale === "ar"
        ? this.recordedCourse.name_ar
        : this.recordedCourse.name_en;
    },
    breadcrumbs() {
      return [
        { title: "dashboard", link: this.route("dashboard") },
        { title: "settings", link: this.route("back.settings") },
        {
          title: "recorded-courses",
          link: this.route("back.settings.recorded-courses.index"),
        },
        {
          title_raw: this.courseTitle,
          link: this.route("back.settings.recorded-courses.show", this.recordedCourse.id),
        },
        { title: "recorded-course-step-enrollments" },
      ];
    },
  },
};
</script>
