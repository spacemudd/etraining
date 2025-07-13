<template>
    <app-layout>
        <div class="container px-6 mx-auto grid">
            <h1 class="mt-10 text-lg text-center text-red-600">وثيقة الادوار والمسؤوليات</h1>

<!--            <h2 class="mt-2">فريق التدريب</h2>-->
<!--            <table class="w-full whitespace-no-wrap">-->
<!--            <thead>-->
<!--            <tr class="text-left font-bold">-->
<!--            	<th class="px-6 pt-6 pb-4">الاسم</th>-->
<!--                <th class="px-6 pt-6 pb-4">القسم</th>-->
<!--                <th class="px-6 pt-6 pb-4">التواصل</th>-->
<!--            </tr>-->
<!--            </thead>-->
<!--            	<tbody>-->
<!--                    <tr class="border-t">-->
<!--                        <td class="px-6 py-4">امية البلوشي</td>-->
<!--                        <td class="px-6 py-4">قسم التدريب</td>-->
<!--                        <td class="px-6 py-4">amiah.b@jasara-ksa.com <br/> 0555606571</td>-->
<!--                    </tr>-->
<!--                <tr class="border-t">-->
<!--                        <td class="px-6 py-4">سمر العلي</td>-->
<!--                        <td class="px-6 py-4">قسم تقنية المعلومات</td>-->
<!--                        <td class="px-6 py-4">info@ptc-ksa.net <br/> 0553139979</td>-->
<!--                    </tr>-->
<!--                <tr class="border-t">-->
<!--                        <td class="px-6 py-4">لمى الشقير</td>-->
<!--                        <td class="px-6 py-4">قسم الإدارة العامة</td>-->
<!--                        <td class="px-6 py-4">lama@ptc-ksa.net <br/> 0555432644</td>-->

<!--                    </tr>-->
<!--            	</tbody>-->
<!--            </table>-->

            <hr class="my-5 border-red-500"/>
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

        }
    },
    mounted() {

    },
    methods: {

    },
    beforeDestroy() {

    }
}
</script>
