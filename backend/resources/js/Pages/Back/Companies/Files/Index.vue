<template>
    <app-layout>
        <div class="container px-6 mx-auto grid pt-6">
            <breadcrumb-container
                :crumbs="[
                    {title: 'dashboard', link: route('dashboard')},
                    {title: 'companies', link: route('back.companies.index')},
                    {title_raw: company.name_ar, link: route('back.companies.show', company.id)},
                    {title_raw: $t('words.files')},
                ]"
            ></breadcrumb-container>

            <div class="bg-white rounded shadow overflow-x-auto my-5 p-5">

                <table class="w-full whitespace-no-wrap bg-white rounded-lg my-5 p-5 shadow text-sm">
                    <colgroup>
                        <col>
                        <col style="width:200px;">
                        <col style="width:200px;">
                        <col style="width:30px;">
                    </colgroup>
                <thead>
                <tr>
                	<th class="text-right">{{ $t('words.name') }}</th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
                </thead>
                	<tbody>
                			<tr v-for="file in company.logo_files" class="hover:bg-gray-100 focus-within:bg-gray-100">
                				<td class="border-t mt-2 p-1 px-2">
                                    <a target="_blank"
                                       :href="route('back.companies.files.show', {company_id: company.id, file: file.id})">
                                        {{ file.file_name }}
                                    </a>
                                </td>
                                <td class="border-t mt-2 p-1 px-2" dir="ltr">{{ file.created_at_timezone }}</td>
                                <td class="border-t mt-2 p-1 px-2" dir="ltr">{{ file.human_readable_size }}</td>
                                <td class="border-t mt-2 p-1 px-2" dir="ltr">
                                    <span class="cursor-pointer" @click="deleteFile(file.id)">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                    </span>
                                </td>
                			</tr>
                	</tbody>
                </table>
            </div>


            <!-- my comment -->
            <div class="bg-white rounded shadow overflow-x-auto my-5 p-5">
                <form @submit.prevent="submitForm" enctype="multipart/form-data">
                    <div class="importFile mt-10">
                        <input type="file"
                               name="attached_file"
                               ref="attached_file"
                               required
                               @change="importFileChanged">
                    </div>
                    <div class="flex mt-8">
                        <button type="submit"
                                :disabled="$wait.is('SAVING_FILE')"
                                class="inline-flex px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150 mx-4 tracking-normal">
                            {{ $t('words.upload') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout'
    import IconNavigate from 'vue-ionicons/dist/ios-arrow-dropright'
    import BreadcrumbContainer from "@/Components/BreadcrumbContainer";
    import JetLabel from '@/Jetstream/Label'
    import SelectTraineeGroup from "@/Components/SelectTraineeGroup";
    import { log } from 'logrocket';

    export default {
        metaInfo: { title: 'Files' },
        props: ['company'],
        components: {
            BreadcrumbContainer,
            IconNavigate,
            AppLayout,
            JetLabel,
            SelectTraineeGroup,
        },
        data() {
            return {
                attachedFile: '',
                formData: new FormData(),
            }
        },
        mounted() {

            this.$wait.end('SAVING_FILE');
        },
        methods: {
            importFileChanged(e, filename) {
                this.attachedFile = e.target.files[0];
                this.formData.append('logo_files', this.attachedFile);
            },
            submitForm() {
                this.$wait.start('SAVING_FILE');
                axios.post(route('back.companies.files.store', this.company.id), this.formData)
                    .then(response => {
                        this.$inertia.get(route('back.companies.files.index', this.company.id));
                    }).catch(error => {
                        throw error;
                    });
            },
            deleteFile(file_id) {
                this.$inertia.delete(route('back.companies.files.destroy', {
                    company_id: this.company.id,
                    file: file_id,
                }))
            },
        },
    }
</script>
