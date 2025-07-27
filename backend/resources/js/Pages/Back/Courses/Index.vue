<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'courses', link: route('back.courses.index')},
                ]"
            ></breadcrumb-container>
            <div class="flex justify-between">
                <h1 class="mb-8 font-bold text-3xl">{{ $t('words.courses') }}</h1>
                <div class="mb-6 flex justify-between items-center">

                    <!--<search-filter v-model="form.search" class="w-full max-w-md mr-4" @reset="reset">-->
                    <!--    <label class="block text-gray-700">Trashed:</label>-->
                    <!--    <select v-model="form.trashed" class="mt-1 w-full form-select">-->
                    <!--        <option :value="null" />-->
                    <!--        <option value="with">With Trashed</option>-->
                    <!--        <option value="only">Only Trashed</option>-->
                    <!--    </select>-->
                    <!--</search-filter>-->

                    <inertia-link class="btn-gray" :href="route('back.courses.create')">
                        <span>{{ $t('words.new') }}</span>
                    </inertia-link>

                    <!-- Issue Certificates Button -->
                    <button class="btn-gray ml-2" @click="showCertificateModal = true">
                        <span>{{ $t('words.issue-certificates', 'Issue certificates') }} - إصدار شهادات</span>
                    </button>

                    <inertia-link :href="route('back.courses.today')" class="rounded items-center mr-3 justify-start float-left px-3 py-2.5 bg-yellow-200 hover:bg-yellow-300 text-left">
                        {{ $t('words.show-today-courses') }}
                    </inertia-link>


                </div>
            </div>
            <div class="bg-white rounded shadow overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <tr class="text-left font-bold">
                        <th class="px-6 pt-6 pb-4">{{ $t('words.name') }}</th>
                        <th class="px-6 pt-6 pb-4">{{ $t('words.course-approval-code') }}</th>
                        <th class="px-6 pt-6 pb-4">{{ $t('words.instructor') }}</th>
                        <th class="px-6 pt-6 pb-4">{{ $t('words.recommended-trainees-count') }}</th>
                    </tr>
                    <tr v-for="course in courses.data" :key="course.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
                        <td class="border-t">
                            <div class="px-6 py-4 flex items-center focus:text-indigo-500">
                                <inertia-link :href="route('back.courses.show', course.id)">
                                    {{ course.name_ar }}
                                    <br/>

                                    <div v-if="course.is_pending_approval"
                                         class="text-sm inline-block mt-2 p-1 px-2 bg-red-300 rounded-lg">
                                        {{ $t('words.pending-approval') }}
                                    </div>

                                    <span v-else-if="course.is_approved" class="text-sm inline-block mt-2 p-1 px-2 bg-green-300 rounded-lg">
                                        {{ $t('words.approved') }}
                                    </span>


                                    <span v-if="course.closest_course_batch == 'empty'" class="text-sm inline-block mt-2 p-1 px-2 bg-red-400 rounded-lg">
                                        {{ $t('words.not-set') }}
                                    </span>

                                    <span v-else-if="course.closest_course_batch == 'Today'" class="text-sm inline-block mt-2 p-1 px-2 bg-yellow-400 rounded-lg">
                                        {{ $t('words.today') }}
                                    </span>

                                    <span v-else-if="course.closest_course_batch < currentDate && course.closest_course_batch != 'empty'" class="text-sm inline-block mt-2 p-1 px-2 bg-blue-300 rounded-lg">
                                        {{ course.closest_course_batch }}
                                    </span>

                                    <span v-else-if="course.closest_course_batch > currentDate" class="text-sm inline-block mt-2 p-1 px-2 bg-green-500 rounded-lg">

                                        {{ course.closest_course_batch }}
                                    </span>




                                </inertia-link>
                            </div>
                        </td>
                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('back.courses.show', course.id)">
                                {{ course.approval_code }}
                            </inertia-link>
                        </td>
                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center" :href="route('back.courses.show', course.id)" tabindex="-1">
                                <div v-if="course.instructor">
                                    {{ course.instructor.name }}
                                </div>
                            </inertia-link>
                        </td>
                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center" :href="route('back.courses.show', course.id)" tabindex="-1">
                                {{ course.classroom_count }}
                            </inertia-link>
                        </td>
                        <td class="border-t w-px">
                            <inertia-link class="px-4 flex items-center" :href="route('back.courses.show', course.id)" tabindex="-1">
                                <ion-icon name="arrow-forward-outline" class="block w-6 h-6 fill-gray-400"></ion-icon>
                            </inertia-link>
                        </td>
                    </tr>
                    <tr v-if="courses.data.length === 0">
                        <td class="border-t px-6 py-4" colspan="4">
                            <empty-slate>
                                <template #actions>
                                    <inertia-link class="btn-gray mt-2 block" :href="route('back.courses.create')">
                                        <span>{{ $t('words.new') }}</span>
                                    </inertia-link>
                                </template>
                            </empty-slate>
                        </td>
                    </tr>
                </table>
            </div>
            <pagination :links="courses.links" />
        </div>
    </app-layout>

    <!-- Certificate Issue Modal -->
    <div v-if="showCertificateModal" class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-lg relative">
            <button class="absolute top-2 right-2 text-gray-500 hover:text-gray-700" @click="showCertificateModal = false">&times;</button>
            <h2 class="text-xl font-bold mb-4">{{ $t('words.issue-certificates', 'Issue certificates') }} - إصدار شهادات</h2>
            <form @submit.prevent="handleCertificateZipUpload">
                <label class="block mb-2">Select Course:</label>
                <select v-model="selectedCourseId" required class="mb-4 w-full border rounded p-2">
                    <option v-for="course in courses.data" :key="course.id" :value="course.id">
                        {{ course.name_ar }}
                    </option>
                </select>
                <label class="block mb-2">Upload .zip file containing certificates (ID_Name.pdf):</label>
                <input type="file" accept=".zip" @change="onZipFileChange" required class="mb-4" />
                <button type="submit" class="btn-gray">{{ $t('words.upload', 'Upload') }}</button>
            </form>
            <div v-if="uploadError" class="text-red-500 mt-2">{{ uploadError }}</div>
            <div v-if="matchedTrainees.length || unmatchedTrainees.length" class="mt-4">
                <div v-if="matchedTrainees.length">
                    <h3 class="font-bold">Matched Trainees ({{ matchedTrainees.length }})</h3>
                    <ul class="mb-2">
                        <li v-for="t in matchedTrainees" :key="t.id">{{ t.identity_number }} - {{ t.name }}</li>
                    </ul>
                </div>
                <div v-if="unmatchedTrainees.length">
                    <h3 class="font-bold">Unmatched Trainees ({{ unmatchedTrainees.length }})</h3>
                    <ul>
                        <li v-for="(t, idx) in unmatchedTrainees" :key="t.filename" class="mb-2">
                            <div>
                                <span>{{ t.identity_number || 'Unknown ID' }} - {{ t.filename }}</span>
                                <input
                                    type="text"
                                    v-model="t.searchQuery"
                                    @input="searchTrainees(idx)"
                                    placeholder="Search trainee by name or ID"
                                    class="border rounded p-1 ml-2"
                                />
                                <div v-if="t.searchResults && t.searchResults.length" class="bg-white border rounded shadow mt-1 max-h-32 overflow-y-auto z-50 absolute">
                                    <div
                                        v-for="trainee in t.searchResults"
                                        :key="trainee.id"
                                        @click="selectTraineeForUnmatched(idx, trainee)"
                                        class="p-2 hover:bg-gray-100 cursor-pointer"
                                    >
                                        {{ trainee.identity_number }} - {{ trainee.name }}
                                    </div>
                                </div>
                                <div v-if="t.selectedTrainee" class="inline-block ml-2 text-green-700">
                                    Linked: {{ t.selectedTrainee.identity_number }} - {{ t.selectedTrainee.name }}
                                    <button @click="removeLinkedTrainee(idx)" class="ml-1 text-red-500">&times;</button>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <button
                v-if="matchedTrainees.length || (unmatchedTrainees.length && allUnmatchedLinked)"
                class="btn-primary mt-4"
                :disabled="!allUnmatchedLinked"
                @click="submitCertificatesImport"
            >
                Submit & Queue Certificates
            </button>
        </div>
    </div>
</template>

<script>
    // import Icon from '@/Shared/Icon'
    // import Layout from '@/Shared/Layout'
    import mapValues from 'lodash/mapValues'
    import Pagination from '@/Shared/Pagination'
    import pickBy from 'lodash/pickBy'
    // import SearchFilter from '@/Shared/SearchFilter'
    import throttle from 'lodash/throttle'
    import AppLayout from '@/Layouts/AppLayout'
    import IconNavigate from 'vue-ionicons/dist/ios-arrow-dropright'
    import BreadcrumbContainer from "@/Components/BreadcrumbContainer";
    import EmptySlate from "@/Components/EmptySlate";

    export default {
        metaInfo: { title: 'Courses' },
        // layout: Layout,
        components: {
            EmptySlate,
            BreadcrumbContainer,
            IconNavigate,
            AppLayout,
            // Icon,
            Pagination,
            // SearchFilter,
        },
        props: {
            courses: Object,
            filters: Object,
        },
        data() {
            return {
                form: {
                    // search: this.filters.search,
                    // trashed: this.filters.trashed,
                },
                currentDate: '',
                showCertificateModal: false,
                certificateZipFile: null,
                uploadError: '',
                selectedCourseId: '',
                matchedTrainees: [],
                unmatchedTrainees: [],
                searchTimeouts: {},
                lastImportId: null,
            }
        },
        computed: {
            allUnmatchedLinked() {
                return this.unmatchedTrainees.every(t => t.selectedTrainee);
            },
        },
        watch: {
            form: {
                handler: throttle(function() {
                    let query = pickBy(this.form)
                    this.$inertia.replace(this.route('courses', Object.keys(query).length ? query : { remember: 'forget' }))
                }, 150),
                deep: true,
            },
        },
        methods: {
            reset() {
                this.form = mapValues(this.form, () => null)
            },
            onZipFileChange(e) {
                this.certificateZipFile = e.target.files[0];
            },
            async handleCertificateZipUpload() {
                if (!this.certificateZipFile || !this.selectedCourseId) {
                    this.uploadError = 'Please select a course and a .zip file.';
                    return;
                }
                this.uploadError = '';
                const formData = new FormData();
                formData.append('zip', this.certificateZipFile);
                formData.append('course_id', this.selectedCourseId);
                try {
                    const response = await axios.post('/certificates/import/upload-zip', formData, {
                        headers: { 'Content-Type': 'multipart/form-data' }
                    });
                    this.matchedTrainees = response.data.matched || [];
                    this.unmatchedTrainees = (response.data.unmatched || []).map(t => ({ ...t, searchQuery: '', searchResults: [], selectedTrainee: null }));
                    this.lastImportId = response.data.import_id;
                    // Keep modal open to allow user to review and link unmatched
                } catch (err) {
                    this.uploadError = 'Upload failed.';
                }
            },
            searchTrainees(idx) {
                clearTimeout(this.searchTimeouts[idx]);
                const query = this.unmatchedTrainees[idx].searchQuery;
                if (!query || query.length < 2) {
                    this.$set(this.unmatchedTrainees[idx], 'searchResults', []);
                    return;
                }
                this.searchTimeouts[idx] = setTimeout(() => {
                    axios.get('/search', { params: { search: query, trainees: true } })
                        .then(res => {
                            this.$set(this.unmatchedTrainees[idx], 'searchResults', res.data);
                        });
                }, 300);
            },
            selectTraineeForUnmatched(idx, trainee) {
                this.$set(this.unmatchedTrainees[idx], 'selectedTrainee', trainee);
                this.$set(this.unmatchedTrainees[idx], 'searchResults', []);
                this.$set(this.unmatchedTrainees[idx], 'searchQuery', `${trainee.identity_number} - ${trainee.name}`);
            },
            removeLinkedTrainee(idx) {
                this.$set(this.unmatchedTrainees[idx], 'selectedTrainee', null);
            },
            async submitCertificatesImport() {
                // Gather mapping for unmatched
                const mappings = this.unmatchedTrainees.map(t => ({
                    filename: t.filename,
                    trainee_id: t.selectedTrainee.id,
                }));
                try {
                    const response = await axios.post('/certificates/import/finalize', {
                        import_id: this.lastImportId || (this.matchedTrainees[0] && this.matchedTrainees[0].import_id),
                        mappings,
                    });
                    alert('Certificates queued for sending!');
                    this.showCertificateModal = false;
                    this.certificateZipFile = null;
                    this.selectedCourseId = '';
                    this.matchedTrainees = [];
                    this.unmatchedTrainees = [];
                } catch (err) {
                    alert('Submission failed.');
                }
            },
        },
        mounted() {

            const today = new Date();

            function formatDate(date, format) {

                var newFormat = format.replace('mm', date.getMonth() + 1)
                .replace('yy', date.getFullYear())
                .replace('dd', date.getDate());

                return newFormat;
            }

            this.currentDate = formatDate(today, 'yy-mm-dd');
        }
    }
</script>
