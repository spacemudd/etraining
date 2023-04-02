<template>
    <app-layout>
        <div class="container px-6 mx-auto grid">
            <div class="grid grid-cols-1 gap-6 mt-5">
                <div class="col text-center">
                    <p class="text-red-500">الدعم الفني</p>
                    <p class="text-red-500">المساعدة الفنية والتعليمية للمدربين والمتدربين</p>
                    <br/>
                    <p>طوال مدة تنفيذ البرنامج.</p>
                    <br/>
                    <p>يتعهد مركز احترافية المدرب للتدريب بتقديم المساعدة الفنية والتعليمية للمدربين والمتدربين<br/> طوال مدة تنفيذ البرامج وذلك عن طريق:</p>
                    <br/>
                    <p>التواجد المستمر في غرفة المحاضرات للدعم الفني واللوجستي خلال فترة التدريب.</p>
                    <br/>
                    <p>التواصل مع المدرب والمتدرب من خلال قنوات الاتصال</p>
                    <br/>
                    <p>جوال</p>
                    <p>0555026885</p>
                    <br/>
                    <p>أو من خلال الواتس آب</p>
                    <a class="mt-10" href="https://api.whatsapp.com/send?phone=966553139979">
                        <ion-icon name="logo-whatsapp" class="w-10 h-10 text-green-600"></ion-icon>
                    </a>
                    <br/>
                    <br/>
                    <p>تقديم الدعم من خلال الايميل الخاص بمركز احترافية المدرب</p>
                    <p><a href="mailto:training@ptc-ksa.net">training@ptc-ksa.net</a></p>
                </div>
            </div>

            <div class="flex justify-center mt-5">
                    <a target="_blank"
                       rel="noopener noreferrer"
                       href="/obligations1.pdf"
                       class="bg-red-500 px-2 py-1 rounded mx-2 text-white hover:bg-red-700">
                        اقرار الالتزام
                    </a>
                    <a target="_blank"
                       rel="noopener noreferrer"
                       href="/obligations2.pdf"
                       class="bg-red-500 px-2 py-1 rounded mx-2 text-white hover:bg-red-700">
                        التعهد
                    </a>
            </div>


        </div>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayoutTrainee'
import Welcome from '@/Jetstream/Welcome'
import LanguageSelector from "@/Shared/LanguageSelector";
import HeaderCard from "@/Components/HeaderCard";
import JetLabel from '@/Jetstream/Label';
import JetInput from '@/Jetstream/Input';
import JetInputError from '@/Jetstream/InputError';
import JetTextarea from '@/Jetstream/Textarea';
import JetButton from '@/Jetstream/Button';

export default {
    components: {
        AppLayout,
        Welcome,
        LanguageSelector,
        HeaderCard,
        JetLabel,
        JetInput,
        JetInputError,
        JetTextarea,
        JetButton,
    },
    data() {
        return {
            createComplaintForm: this.$inertia.form({
                course_name: '',
                course_instructor: '',
                message: '',
            }, {
                bag: 'createComplaintForm',
            }),
        }
    },
    mounted() {

    },
    methods: {
        submitForm() {
            this.createComplaintForm.post(route('complaints.store'));
        }
    },
    beforeDestroy() {

    }
}
</script>
