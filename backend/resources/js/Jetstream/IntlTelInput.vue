<template>
    <input type="tel" id="phone" ref="phone" class="form-control">
</template>

<script>
    import intlTelInput from "intl-tel-input";
    import telUtils from "intl-tel-input/build/js/utils";
    import { numberType } from 'intl-tel-input/build/js/utils';

    if (!window.intlTelInput) {
        require('intl-tel-input')
    }

    export default {
        data() {
            return {
                //
            }
        },
        mounted() {
            let vm = this;

            var input = document.querySelector("#phone");

            var iti = intlTelInput(input, {
                utilsScript: telUtils,
                placeholderNumberType: 'MOBILE',
                // initialCountry: "auto",
                // geoIpLookup: function(success, failure) {
                //     $.get("https://ipinfo.io", function() {}, "jsonp").always(function(resp) {
                //         var countryCode = (resp && resp.country) ? resp.country : "bh";
                //         success(countryCode);
                //     });
                // },
                preferredCountries: [
                    "sa",
                ],
            });

            input.addEventListener('countrychange', function(e) {
                vm.$emit('countrychange', iti.getSelectedCountryData());
            });

            input.addEventListener('input', function(e) {

                let theBeautifulMoonNumber = {
                    'number': iti.getNumber(),
                    'is_mobile': (iti.getNumberType() === 1) || (iti.getNumberType() === 2),
                    'country_iso2': iti.getSelectedCountryData() ? iti.getSelectedCountryData().iso2 : '',
                }

                vm.$emit('input', theBeautifulMoonNumber.number);

                //vm.$emit('input-object', this.);
                vm.emitTheBeautifulMoonNumber(theBeautifulMoonNumber, vm);
            });
        },
        methods: {
            emitTheBeautifulMoonNumber: _.debounce((val, vm) => {
                vm.$emit('input-object', val);
            }, 850),
        },
        // destroyed () {
        //     let vm = this;
        //     var input = document.querySelector("#phone");
        //
        //     input.removeEventListener('countrychange', function(e) {
        //         vm.$emit('countrychange', e);
        //     });
        //
        //     input.removeEventListener('input', function(e) {
        //         vm.$emit('input', e);
        //     });
        // }
    }
</script>

<style lang="scss">
    @import "~intl-tel-input/build/css/intlTelInput.min.css";

    .iti__country-list {
        [dir=rtl] & {
            text-align: right;
        }
    }

    .iti__dial-code {
        [dir=rtl] & {
            margin-right: 5px;
            display: inline-block;
        }
    }

    .iti__arrow {
        margin-left: 7px;
        [dir=rtl] & {
            margin-right: 7px;
        }
    }

    .iti__country-list {
        [dir=rtl] & {
            margin: 0 -185px 0px -1px;
        }
    }

</style>
