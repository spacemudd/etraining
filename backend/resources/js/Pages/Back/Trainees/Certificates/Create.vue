<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'trainees', link: route('back.trainees.index')},
                    {title_raw: trainee.name, link: route('back.trainees.show', trainee.id)},
                    {title: 'issue-new-certificate'},
                ]"
            ></breadcrumb-container>

            <!-- قائمة الشهادات الحالية مع عرض الملفات -->
            <div v-if="trainee.custom_certificates && trainee.custom_certificates.length > 0" class="mt-4 mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">الشهادات المسجلة</h3>
                <ul class="space-y-2">
                    <li
                        v-for="cert in trainee.custom_certificates"
                        :key="cert.id"
                        class="flex items-center justify-between py-2 px-3 bg-white rounded-md border border-gray-100 hover:bg-gray-50"
                    >
                        <span class="text-sm text-gray-700">{{ cert.title }} — {{ cert.issued_at_formatted }}</span>
                        <a
                            v-if="cert.has_certificate_file"
                            href="#"
                            class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 text-sm"
                            @click.prevent="openCertificateFile(cert)"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                            عرض الملف
                        </a>
                        <span v-else class="text-xs text-gray-400">لا يوجد ملف</span>
                    </li>
                </ul>
            </div>

            <div class="mt-4">
                <jet-form-section @submitted="createCertificate">
                    <template #title>
                        {{ $t('words.issue-new-certificate') }}
                    </template>

                    <template #description>
                        {{ $t('words.issue-new-certificate') }}
                    </template>

                    <template #form>
                        <div class="col-span-4 sm:col-span-4">
                            <jet-label for="title" :value="$t('words.certificate-title')" />
                            <jet-input 
                                id="title" 
                                type="text" 
                                class="mt-1 block w-full" 
                                v-model="form.title" 
                                autocomplete="off" 
                                autofocus 
                            />
                            <jet-input-error :message="form.error('title')" class="mt-2" />
                        </div>

                        <div class="col-span-4 sm:col-span-4">
                            <jet-label for="issued_at" :value="$t('words.issue-date')" />
                            <jet-input 
                                id="issued_at" 
                                type="date" 
                                class="mt-1 block w-full" 
                                v-model="form.issued_at" 
                                autocomplete="off" 
                            />
                            <jet-input-error :message="form.error('issued_at')" class="mt-2" />
                        </div>

                        <div class="col-span-4 sm:col-span-4">
                            <jet-label for="certificate_file" value="ملف الشهادة" />
                            <div
                                class="mt-1 flex flex-col justify-center items-center w-full border-2 border-gray-300 border-dashed rounded-lg cursor-pointer hover:bg-gray-50"
                                :class="{ 'border-green-400 bg-green-50': form.certificate_file }"
                                @click="$refs.certificateFileInput.click()"
                                @dragover.prevent
                                @drop.prevent="onCertificateFileDrop"
                            >
                                <input
                                    ref="certificateFileInput"
                                    type="file"
                                    class="hidden"
                                    accept=".pdf,.jpg,.jpeg,.png"
                                    @change="onCertificateFileChange"
                                />
                                <p class="py-4 text-sm text-gray-600" :class="{ 'text-green-600': form.certificate_file }">
                                    {{ form.certificate_file ? form.certificate_file.name : 'انقر لاختيار الملف أو اسحبه هنا' }}
                                </p>
                                <p class="text-xs text-gray-500 mb-2">PDF, JPG, PNG (حد أقصى 10 ميجابايت)</p>
                            </div>
                            <jet-input-error :message="form.error('certificate_file')" class="mt-2" />
                        </div>
                    </template>

                    <template #actions>
                        <jet-action-message :on="form.recentlySuccessful" class="mr-3">
                            {{ $t('words.created-successfully') }}
                        </jet-action-message>

                        <inertia-link 
                            :href="route('back.trainees.show', trainee.id)"
                            class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring focus:ring-gray-300 disabled:opacity-25 transition mr-2"
                        >
                            {{ $t('words.cancel') }}
                        </inertia-link>

                        <jet-button 
                            :class="{ 'opacity-25': submitting }" 
                            :disabled="submitting"
                        >
                            {{ submitting ? $t('words.saving') || 'جاري الحفظ...' : $t('words.save') }}
                        </jet-button>
                    </template>
                </jet-form-section>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import JetSectionBorder from '@/Jetstream/SectionBorder'
import JetInput from '@/Jetstream/Input'
import JetInputError from '@/Jetstream/InputError'
import JetActionMessage from '@/Jetstream/ActionMessage';
import JetButton from '@/Jetstream/Button';
import JetFormSection from '@/Jetstream/FormSection';
import JetLabel from '@/Jetstream/Label';
import BreadcrumbContainer from "@/Components/BreadcrumbContainer";

export default {
    props: [
        'trainee',
    ],

    components: {
        AppLayout,
        JetSectionBorder,
        JetInput,
        JetInputError,
        JetActionMessage,
        JetButton,
        JetFormSection,
        JetLabel,
        BreadcrumbContainer,
    },

    data() {
        return {
            submitting: false,
            form: this.$inertia.form({
                title: '',
                issued_at: new Date().toISOString().split('T')[0],
                certificate_file: null,
            }, {
                bag: 'createCertificate',
            })
        }
    },

    methods: {
        onCertificateFileChange(event) {
            const file = event.target.files[0];
            if (file) {
                this.form.certificate_file = file;
            }
        },
        onCertificateFileDrop(event) {
            const file = event.dataTransfer.files[0];
            if (file) {
                this.form.certificate_file = file;
            }
        },
        createCertificate() {
            const url = route('back.trainees.custom-certificates.store', this.trainee.id);
            const formData = new FormData();
            formData.append('title', this.form.title);
            formData.append('issued_at', this.form.issued_at);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '');
            if (this.form.certificate_file) {
                formData.append('certificate_file', this.form.certificate_file);
            }

            const headers = {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            };
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (csrfToken) {
                headers['X-CSRF-TOKEN'] = csrfToken.getAttribute('content');
            }

            this.submitting = true;
            axios.post(url, formData, { headers })
                .then((response) => {
                    if (response.data && response.data.redirect) {
                        this.$inertia.visit(response.data.redirect);
                    } else {
                        this.$inertia.visit(route('back.trainees.show', this.trainee.id));
                    }
                })
                .catch((error) => {
                    this.submitting = false;
                    if (error.response && error.response.status === 422 && error.response.data.errors) {
                        const errors = error.response.data.errors;
                        Object.keys(errors).forEach((key) => {
                            this.form.setError(key, errors[key][0]);
                        });
                    } else {
                        alert(error.response?.data?.message || 'حدث خطأ أثناء حفظ الشهادة. يرجى المحاولة مرة أخرى.');
                    }
                });
        },
        async openCertificateFile(certificate) {
            if (!certificate.has_certificate_file) return;
            try {
                const url = route('back.trainees.custom-certificates.file-url', {
                    trainee_id: this.trainee.id,
                    id: certificate.id,
                });
                const response = await axios.get(url);
                if (response.data && response.data.file_url) {
                    window.open(response.data.file_url, '_blank', 'noopener,noreferrer');
                } else {
                    alert('لم يتم العثور على رابط الملف');
                }
            } catch (err) {
                const msg = err.response?.data?.error || err.response?.data?.message || 'حدث خطأ أثناء فتح الملف';
                alert(msg);
            }
        },
    },
}
</script> 