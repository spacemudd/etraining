<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6 space-y-6">
            <!-- Breadcrumb -->
            <breadcrumb-container
                :crumbs="[ 
                    {title: 'dashboard', link: route('dashboard')}, 
                    {title: 'reports', link: route('back.reports.index')}, 
                    {title: 'course-attendances', link: route('back.reports.course-attendances.index')}
                ]"
            ></breadcrumb-container>

            <!-- Form Section -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold mb-4">Generate Report</h2>

                <form @submit.prevent="generateReport" class="space-y-4">
                    <!-- Courses Select -->
                    <div>
                        <label for="courseId" class="block text-sm font-medium text-gray-700 mb-1">Select Course</label>
                        <v-select 
                            id="courseId" 
                            v-model="form.courseId" 
                            :options="courses"
                            label="name_ar" 
                            placeholder="Select a course"
                            required
                            searchable
                            class="block w-full mt-1"
                        />
                    </div>

                    <!-- Companies Select -->
                    <div>
                        <label for="companyId" class="block text-sm font-medium text-gray-700 mb-1">Select Company</label>
                        <v-select 
                            id="companyId" 
                            v-model="form.companyId" 
                            :options="companies"
                            label="name_ar" 
                            placeholder="Select a company"
                            required
                            searchable
                            class="block w-full mt-1"
                        />
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button 
                            type="submit" 
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            :disabled="form.processing"
                        >
                            <span v-if="!form.processing">Generate Report</span>
                            <span v-else>
                                <BtnLoadingIndicator />
                                Processing...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </app-layout>
</template>

<script>
import JetLabel from '@/Jetstream/Label';
import AppLayout from '@/Layouts/AppLayout'
import IconNavigate from 'vue-ionicons/dist/ios-arrow-dropright'
import BreadcrumbContainer from "@/Components/BreadcrumbContainer";
import BtnLoadingIndicator from "../../../../Components/BtnLoadingIndicator";
import vSelect from 'vue-select'; 
import 'vue-select/dist/vue-select.css'; 


export default {
    props: [
        'courses', 
        'companies',
    ],
    components: {
        IconNavigate,
        AppLayout,
        JetLabel,
        BreadcrumbContainer,
        BtnLoadingIndicator,
        vSelect, 
    },
    data() {
        return {
            form: {
                courseId: null,
                companyId: null,
                processing: false,
            },
        }
    },
    methods: {
        generateReport() {
                this.form.processing = true;

                axios.post(route('reports.company-certificates.generate'), this.form)
                    .then(() => {
                        this.form.processing = false;
                        this.$notify({
                            type: 'success',
                            title: 'تم إرسال الطلب',
                            text: 'سيتم تجهيز التقرير وإرساله إليك عند الانتهاء.'
                        });
                    })
                    .catch(error => {
                        this.form.processing = false;
                        console.error('Error:', error);
                        this.$notify({
                            type: 'error',
                            title: 'خطأ',
                            text: 'حدث خطأ أثناء إرسال الطلب. الرجاء المحاولة لاحقًا.'
                        });
                    });
            }
    }
}
</script>

<style scoped>
@import 'vue-select/dist/vue-select.css';
</style>
