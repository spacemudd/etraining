<!--
  - Copyright (c) 2020 - Clarastars, LLC  - All Rights Reserved.
  -
  - Unauthorized copying of this file via any medium is strictly prohibited.
  - This file is a proprietary of Clarastars LLC and is confidential / educational purpose only.
  -
  - https://clarastars.com - info@clarastars.com
  - @author Shafiq al-Shaar <shafiqalshaar@gmail.com>
  -->

<template>
    <div>
        <loading-screen v-if="loading || loadingPreselectedTrainees" />
        <treeselect v-else
                    v-model="value"
                    @input="selectedValue"
                    name="trainees"
                    :multiple="true"
                    :options="options"
                    :normalizer="normalizer"
                    :placeholder="$t('words.select')"
                    :no-children-text="$t('words.no-trainees')"
        />
    </div>
</template>

<script>
    import Treeselect from '@riophae/vue-treeselect'
    import '@riophae/vue-treeselect/dist/vue-treeselect.css'
    export default {
        components: { Treeselect },
        props: ['contract_id'],
        data() {
            return {
                value: null, // initial html value.
                options: null, // options for selection.
                loading: true,
                loadingPreselectedTrainees: true,
                normalizer: (node) => {
                    return {
                        id: node.id,
                        label: node.description,
                        children: node.trainees,
                    }
                },
            }
        },
        mounted() {
            this.getTraineesGroups();
            if (this.contract_id) {
                this.loadSelectedTrainees();
            } else {
                this.loadingPreselectedTrainees = false;
            }
        },
        methods: {
            getTraineesGroups() {
                axios.get('/back/employees-with-groups')
                    .then(response => {
                        this.options = response.data;
                    })
                    .catch(error => {
                        throw error;
                    })
                    .finally(() => {
                        this.loading = false;
                    })
            },
            selectedValue(event) {
                this.$emit('input', event);
            },
            loadSelectedEmployees() {
                this.loadingPreselectedEmployees = true;
                axios.get('/back/tasks/'+this.taskId+'/groups-and-employees')
                    .then(response => {
                        this.value = response.data;
                    })
                    .catch(error => {
                        throw error;
                    })
                    .finally(() => {
                        this.loadingPreselectedEmployees = false;
                    })
            },
        }
    }
</script>
