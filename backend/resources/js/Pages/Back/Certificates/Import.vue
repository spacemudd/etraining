<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'certificates', link: route('back.certificates.import')},
                ]"
            ></breadcrumb-container>
            <div class="flex justify-between">
                <h1 class="mb-8 font-bold text-3xl">{{ $t('words.certificates') }}</h1>
            </div>
            <div class="w-full max-w-2xl mx-auto">
                <form @submit.prevent="handleCertificateZipUpload" class="border-2 p-5 bg-gray-100 text-center">
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
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import BreadcrumbContainer from "@/Components/BreadcrumbContainer";
export default {
    metaInfo: { title: 'Certificates' },
    components: {
        BreadcrumbContainer,
        AppLayout,
    },
    props: {
        courses: Object,
    },
    data() {
        return {
            selectedCourseId: '',
            certificateZipFile: null,
            uploadError: '',
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
    methods: {
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
            const mappings = this.unmatchedTrainees.map(t => ({
                filename: t.filename,
                trainee_id: t.selectedTrainee.id,
            }));
            try {
                const response = await axios.post('/certificates/import/finalize', {
                    import_id: this.lastImportId,
                    mappings,
                });
                alert('Certificates queued for sending!');
                this.selectedCourseId = '';
                this.certificateZipFile = null;
                this.matchedTrainees = [];
                this.unmatchedTrainees = [];
            } catch (err) {
                alert('Submission failed.');
            }
        },
    },
}
</script>
