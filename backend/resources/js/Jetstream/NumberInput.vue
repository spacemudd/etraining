<template>

    <div>

        <intl-tel-input @input-object="processNumber" :class="{'is-invalid': errorBag.contact_number}" required></intl-tel-input>
        <span v-if="contactNumberError" class="invalid-feedback d-block" role="alert">
            <strong>{{ $t('words.please-enter-valid-phone-number') }}</strong>
        </span>
        <span v-if="errorBag.contact_number" class="invalid-feedback d-block" role="alert">
            <strong>{{ errorBag.contact_number[0] }}</strong>
        </span>
    </div>

</template>

<script>
    import JetInput from '@/Jetstream/Input'

    export default {
        components: {
            JetInput,
        },
        props: ['value'],
        data() {
            return {
                theBeautifulMoonNumber: null,
                form: {
                    plan_id: '',
                    email: '',
                    name: '',
                    country_iso2: '',
                    contact_number: '',
                    company_name: '',
                    password: '',
                    marketing_consent: true,
                },

                user: null,

                contactNumberError: false,

                errorBag: [],
            }
        },
        methods: {
            /**
             *
             * @param theBeautifulMoonNumber Check IntlTelInput.vue
             */
            processNumber(theBeautifulMoonNumber) {
                this.theBeautifulMoonNumber = theBeautifulMoonNumber;
                this.form.country_iso2 = theBeautifulMoonNumber.country_iso2;

                if (!this.theBeautifulMoonNumber.is_mobile) {
                    this.contactNumberError = true;
                } else {
                    this.contactNumberError = false;
                }
            },
        }
    }
</script>

<style lang="scss">
    .signup-disclaimer-container {
        margin-left: 10px;
        [dir=rtl] & {
            margin-left: unset;
            margin-right: 10px;
        }
    }
</style>
