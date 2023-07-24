<template>
    <app-layout>
        <div class="container px-6 mx-auto grid">
            <h1 class="mt-10 text-lg text-center text-red-600">وثيقة الادوار والمسؤوليات</h1>

            <h2 class="mt-2">فريق التدريب</h2>
            <table class="w-full whitespace-no-wrap">
            <thead>
            <tr class="text-left font-bold">
            	<th class="px-6 pt-6 pb-4">الاسم</th>
                <th class="px-6 pt-6 pb-4">المهام الوظيفية</th>
                <th class="px-6 pt-6 pb-4">التواصل</th>
            </tr>
            </thead>
            	<tbody>
            			<!--<tr class="border-t">-->
            			<!--	<td class="px-6 py-4">سمر احمد ابو دش</td>-->
                        <!--    <td class="px-6 py-4">التدريب+ تقديم برامج تدريبية + متابعة المتدربات في التدريب جدولة اختبارات واوراق عمل</td>-->
                        <!--    <td class="px-6 py-4">samar@ptc-ksa.net</td>-->
            			<!--</tr>-->
                    <tr class="border-t">
                        <td class="px-6 py-4">--</td>
                        <td class="px-6 py-4">التدريب+ تقديم برامج تدريبية + متابعة المتدربات في التدريب جدولة اختبارات واوراق عمل</td>
                        <td class="px-6 py-4">moudi@ptc-ksa.net</td>
                    </tr>
            	</tbody>
            </table>

            <hr class="my-5 border-red-500"/>

            <h2>فريق الاداريين</h2>
            <table class="w-full whitespace-no-wrap">
                <thead>
                <tr class="text-left font-bold">
                    <th class="px-6 pt-6 pb-4">الاسم</th>
                    <th class="px-6 pt-6 pb-4">المهام الوظيفية</th>
                    <th class="px-6 pt-6 pb-4">التواصل</th>
                </tr>
                </thead>
                <tbody>
                <tr class="border-t">
                    <td class="px-6 py-4">--</td>
                    <td class="px-6 py-4">شوؤن المتدربات + متابعة واصدار الشهادات للمتدربات + متابعة الحضور والانصراف +اشراف عام</td>
                    <td class="px-6 py-4">jawaher@ptc-ksa.net</td>
                </tr>
                <!--<tr class="border-t">-->
                <!--    <td class="px-6 py-4">منيره صالح الحمدي</td>-->
                <!--    <td class="px-6 py-4">القبول والتسجيل والتنسيق والتواصل مع المتدربات</td>-->
                <!--    <td class="px-6 py-4">monira@ptc-ksa.net</td>-->
                <!--</tr>-->
                </tbody>
            </table>

            <hr class="my-5 border-red-500"/>

            <h2>فريق الفنيين</h2>
            <table class="w-full whitespace-no-wrap">
                <thead>
                <tr class="text-left font-bold">
                    <th class="px-6 pt-6 pb-4">الاسم</th>
                    <th class="px-6 pt-6 pb-4">المهام الوظيفية</th>
                    <th class="px-6 pt-6 pb-4">التواصل</th>
                </tr>
                </thead>
                <tbody>
                <tr class="border-t">
                    <td class="px-6 py-4">--</td>
                    <td class="px-6 py-4">عم الفني والتقني للمنصة التدريبية +  تقديم المساعدة</td>
                    <td class="px-6 py-4">info@ptc-ksa.net</td>
                </tr>
                </tbody>
            </table>
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
