<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'trainees', link: route('back.trainees.index')},
                    {title_raw: $t('words.link-groups')},
                ]"
            ></breadcrumb-container>

            <div class="flex justify-between items-center mb-8">
                <h1 class="font-bold text-3xl">{{ $t('words.link-groups') }}</h1>
            </div>

            <!-- Success/Error Messages -->
            <div v-if="successMessage" class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ successMessage }}
            </div>
            <div v-if="errorMessage" class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ errorMessage }}
            </div>

            <div class="bg-white rounded shadow overflow-hidden">
                <form @submit.prevent="submit">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <p class="text-gray-600">{{ $t('words.link-groups-help') || 'هنا يمكنك ربط كل شعبة بمدرب بشكل دائم. عند ربط شعبة بمدرب، سيتم ربط جميع المتدربين في هذه الشعبة بهذا المدرب تلقائياً.' }}</p>
                    </div>

                    <div class="px-6 py-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div v-for="group in form.groups" :key="group.group_id" class="border border-gray-200 rounded-lg p-4">
                                <div class="mb-4">
                                    <h3 class="font-bold text-lg mb-2">{{ group.group_name }}</h3>
                                    <p class="text-sm text-gray-600 mb-2">
                                        {{ $t('words.trainees-count') }}: <span class="font-semibold">{{ group.trainees_count }}</span>
                                    </p>
                                    <p v-if="group.current_instructor_name || getInstructorName(group.instructor_id)" class="text-sm text-green-600 mb-2">
                                        <span class="font-semibold">{{ $t('words.current-instructor') }}:</span>
                                        <span class="font-bold">{{ group.current_instructor_name || getInstructorName(group.instructor_id) }}</span>
                                    </p>
                                </div>

                                <div>
                                    <jet-label :for="'instructor_' + group.group_id" :value="$t('words.instructor')" />
                                    <select
                                        :id="'instructor_' + group.group_id"
                                        v-model="group.instructor_id"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                    >
                                        <option :value="null">{{ $t('words.no-instructor') }}</option>
                                        <option v-for="instructor in instructors" :key="instructor.id" :value="instructor.id">
                                            {{ instructor.name }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end">
                        <jet-secondary-button @click.native="$inertia.visit(route('back.trainees.index'))" class="mr-3">
                            {{ $t('words.cancel') }}
                        </jet-secondary-button>
                        <jet-button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            {{ $t('words.save') }}
                        </jet-button>
                    </div>
                </form>
            </div>
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout'
    import BreadcrumbContainer from "@/Components/BreadcrumbContainer"
    import JetButton from '@/Jetstream/Button'
    import JetSecondaryButton from '@/Jetstream/SecondaryButton'
    import JetLabel from '@/Jetstream/Label'

    export default {
        components: {
            AppLayout,
            BreadcrumbContainer,
            JetButton,
            JetSecondaryButton,
            JetLabel,
        },
        props: {
            traineeGroups: Array,
            instructors: Array,
        },
        computed: {
            successMessage() {
                if (!this.$page || !this.$page.props || !this.$page.props.flash) {
                    return null;
                }
                return this.$page.props.flash.success || null;
            },
            errorMessage() {
                if (!this.$page || !this.$page.props || !this.$page.props.flash) {
                    return null;
                }
                return this.$page.props.flash.error || null;
            },
        },
        data() {
            return {
                form: this.$inertia.form({
                    groups: this.traineeGroups.map(group => ({
                        group_id: group.id,
                        group_name: group.name,
                        trainees_count: group.trainees_count,
                        instructor_id: group.current_instructor_id,
                        current_instructor_name: group.current_instructor_name,
                    })),
                }),
            }
        },
        watch: {
            traineeGroups: {
                handler(newGroups) {
                    // تحديث form.groups عند تحديث traineeGroups
                    this.form.groups = newGroups.map(group => ({
                        group_id: group.id,
                        group_name: group.name,
                        trainees_count: group.trainees_count,
                        instructor_id: group.current_instructor_id,
                        current_instructor_name: group.current_instructor_name,
                    }));
                },
                deep: true,
            },
        },
        methods: {
            submit() {
                this.form.post(route('back.trainees.link-groups.store'), {
                    preserveScroll: true,
                    onSuccess: (page) => {
                        // تحديث form.groups بالبيانات الجديدة من الخادم
                        if (page.props.traineeGroups) {
                            this.form.groups = page.props.traineeGroups.map(group => ({
                                group_id: group.id,
                                group_name: group.name,
                                trainees_count: group.trainees_count,
                                instructor_id: group.current_instructor_id,
                                current_instructor_name: group.current_instructor_name,
                            }));
                        }
                    }
                })
            },
            getInstructorName(instructorId) {
                if (!instructorId) return null;
                const instructor = this.instructors.find(inst => inst.id === instructorId);
                return instructor ? instructor.name : null;
            },
        },
    }
</script>

