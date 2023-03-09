<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <div class="mt-4">
                <jet-form-section>
                    <template #title>
                        {{ $t('words.invite') }}
                    </template>

                    <template #description>

                    </template>

                    <template #form>
                        <div class="col-span-2 sm:col-span-2">
                            <jet-label for="phone" :value="$t('words.phone')" />
                            <jet-input id="phone" type="text" class="mt-1 block w-full" placeholder="966XXXXXXX" autocomplete="off" autofocus />
                        </div>
                    </template>

<!--                    <template #actions>-->
<!--                        <jet-action-message :on="form.recentlySuccessful" class="mr-3">-->
<!--                            {{ $t('words.saved-successfully') }}-->
<!--                        </jet-action-message>-->

<!--                        <inertia-link :href="route('back.instructors.index')" class="flex items-center justify-start rtl:ml-4 ltr:mr-4 rounded-md px-4 py-2 bg-white hover:bg-gray-300 text-right">-->
<!--                            {{ $t('words.cancel') }}-->
<!--                        </inertia-link>-->

<!--                        <jet-button :class="{ 'opacity-25': form.processing }" :disabled="form.processing">-->
<!--                            {{ $t('words.invite') }}-->
<!--                        </jet-button>-->
<!--                    </template>-->
                </jet-form-section>
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

        mounted() {

        },
    }
</script>
