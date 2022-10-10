<!--
  - Copyright (c) 2020 - Clarastars, LLC  - All Rights Reserved.
  -
  - Unauthorized copying of this file via any medium is strictly prohibited.
  - This file is a proprietary of Clarastars LLC and is confidential / educational purpose only.
  -
  - https://clarastars.com - info@clarastars.com
  - @author Shafiq al-Shaar <shafiqalshaar@gmail.com>
  -->

<style>
    .hide_create_option .selectize-dropdown .create {
        display: none;
    }
</style>

<template>
    <selectize ref="companyDiv" id="group" v-model="selected" :settings="settings" :required="required" :disabled="disabled"></selectize>
</template>

<script>
    import Selectize from 'vue2-selectize';

    export default {
        components: {
            Selectize,
        },
        props: {
            selectedItem: {
                type: Object,
                required: false,
                default: null,
            },
            disabled : {
                type: Boolean,
                default: false,
            },
            required: {
                type: Boolean,
                default: false,
            },
        },
        data() {
            return {
                selected: null,
                settings: {
                    preload: true,
                    // create: this.createItem,
                    valueField: 'id',
                    labelField: 'name_ar',
                    searchField: ['name_ar'],
                    selectOnTab: true,
                    onChange: this.onChange,
                    openOnFocus: false,
                    loadThrottle: 400,
                    load: (query, callback) => this.getItems(query, callback),
                    render: {
                        option_create: function(data, escape) {
                            return '<div class="create mx-2"><span class="mx-2 px-2 bg-green-500 text-white rounded hover:bg-green-600">'+this.$t('words.new')+'</span> <strong>' + escape(data.input) + '</strong>&hellip;</div>';
                        }.bind(this),
                    },
                    /**
                     * When selecting an item.
                     **/
                    // onItemAdd: function(value, $item) {
                    //     let selectedProduct = this.$refs.productName.$el.selectize.sifter.items[value];
                    //     this.$emit('input', selectedProduct);
                    // }.bind(this),
                },
            }
        },
        mounted() {
            if (this.selectedItem) {
                this.selected = this.selectedItem.id;
                this.$refs.companyDiv.$el.selectize.setValue(this.selectedItem.id);
                this.$refs.companyDiv.$el.selectize.setTextboxValue(this.selectedItem.name_ar);
            }
        },
        methods: {
            getItems(query, callback) {
                axios.get(route('back.companies.index'))
                .then(response => {
                    callback(response.data);
                })
            },
            /**
             * Defines the structure of a new items
             * added to the list.
             *
             * @param input
             * @param callback
             */
            createItem(input, callback) {
                callback({
                    name: input,
                    id: input,
                })
            },
            onChange(input) {
                let selectedItem = this.$refs.companyDiv.$el.selectize.sifter.items[input];
                if (selectedItem === undefined) selectedItem = null;
                this.$emit('input', selectedItem.id);
            },
        }
    }
</script>
