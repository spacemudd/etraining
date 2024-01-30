<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'trainees', link: route('back.trainees.index')},
                    {title_raw: trainee.name},
                ]"
            ></breadcrumb-container>

            <div class="grid grid-cols-6 gap-6">
                <h1>{{ $t('words.block-trainee') }}</h1>

                <form @submit.prevent="saveForm">
                    <div class="col-span-6 sm:col-span-2">
                        <select class="mt-1 block w-full bg-gray-100 appearance-none border border-gray-300 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:border-gray-500"
                                v-model="deleted_remark"
                                id="reason">
                            <option value="عدم سداد">عدم سداد</option>
                            <option value="استبعاد من الشركة">استبعاد من الشركة</option>
                            <option value="استقالة">استقالة</option>
                            <option value="انسحاب">انسحاب</option>
                            <option value="عمل مشاكل">عمل مشاكل</option>
                            <option value="غير مسجلة في الشركة">غير مسجلة في الشركة</option>
                            <option value="عدم الالتزام في شروط المعهد">عدم الالتزام في شروط المعهد</option>
                            <option value="رجيع">رجيع</option>
                            <option value="غير مؤهل">غير مؤهل</option>
                            <option value="حساب مكرر">حساب مكرر</option>
                            <option value="استبعاد لعدم الحضور">استبعاد لعدم الحضور</option>
                        </select>
                        <button type="submit" class="mt-5 items-center justify-start float-left rounded-md px-4 py-2 bg-red-300 hover:bg-red-400">
                            {{ $t('words.block-trainee') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout'
    import JetSectionBorder from '@/Jetstream/SectionBorder'
    import Breadcrumb from "@/Components/Breadcrumb";
    import JetDialogModal from '@/Jetstream/DialogModal'
    import JetInput from '@/Jetstream/Input'
    import JetButton from '@/Jetstream/Button';
    import JetFormSection from '@/Jetstream/FormSection';
    import JetTextarea from '@/Jetstream/Textarea';
    import JetLabel from '@/Jetstream/Label';
    import BreadcrumbContainer from "@/Components/BreadcrumbContainer";

    export default {
        props: ['sessions', 'trainee'],

        components: {
            AppLayout,
            JetSectionBorder,
            Breadcrumb,
            JetDialogModal,
            JetInput,
            JetButton,
            JetFormSection,
            JetLabel,
            BreadcrumbContainer,
            JetTextarea,
        },
        data() {
            return {
                deleted_remark: null,
            }
        },
        methods: {
            saveForm() {
                if (confirm(this.$t('words.are-you-sure'))) {
                    this.$inertia.post(route('back.trainees.block.store', {trainee_id: this.trainee.id}), {
                        deleted_remark: this.deleted_remark,
                    });
                }
            },
        }
    }
</script>
