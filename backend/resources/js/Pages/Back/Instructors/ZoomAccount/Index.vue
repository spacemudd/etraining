<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'instructors', link: route('back.instructors.index')},
                    {title_raw: instructor.name, link: route('back.instructors.show', instructor.id)},
                    {title_raw: $t('words.zoom-account'), link: route('back.instructors.zoom-account.index', instructor.id)}
                ]"
            ></breadcrumb-container>

            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-6 sm:col-span-2">
                    <jet-label for="account_id" value="account_id" />
                    <jet-input id="account_id" type="text" class="mt-1 block w-full" v-model="form.account_id" autocomplete="off" />
                </div>
                <div class="col-span-6 sm:col-span-2">
                    <jet-label for="client_id" value="client_id" />
                    <jet-input id="client_id" type="text" class="mt-1 block w-full" v-model="form.client_id" autocomplete="off" />
                </div>
                <div class="col-span-6 sm:col-span-2">
                    <jet-label for="client_secret" value="client_secret" />
                    <jet-input id="client_secret" type="text" class="mt-1 block w-full" v-model="form.client_secret" autocomplete="off" />
                </div>
                <div class="col-span-6 sm:col-span-2">
                    <jet-button @click.native="save" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            {{ $t('words.save') }}
                    </jet-button>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout'
    import BreadcrumbContainer from "@/Components/BreadcrumbContainer";
    import JetInput from '@/Jetstream/Input'
    import JetLabel from '@/Jetstream/Label'
    import JetButton from '@/Jetstream/Button'

    export default {
        metaInfo: { title: 'Instructor' },
        components: {
            BreadcrumbContainer,
            AppLayout,
            JetInput,
            JetLabel,
            JetButton,
        },
        props: {
            instructor: Object,
        },
        data() {
            return {
                form: this.$inertia.form({
                    instructor_id: this.instructor.id,
                    account_id: '',
                    client_id: '',
                    client_secret: '',
                }),
            }
        },
        mounted() {
            if (this.instructor.zoom_account) {
                this.form.account_id = this.instructor.zoom_account.account_id;
                this.form.client_id = this.instructor.zoom_account.client_id;
                this.form.client_secret = this.instructor.zoom_account.client_secret;
            }
        },
        methods: {
            save() {
                this.form.put(route('back.instructors.zoom-account.update', {instructor_id: this.instructor.id}));
            },
        },
    }
</script>
