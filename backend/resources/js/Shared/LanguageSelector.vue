<template>
    <div class="ltr:mr-4 rtl:ml-4">
        <a :href="'/language/'+[selectable_locale]">
            <icon :name="selectable_locale" />
        </a>
    </div>
</template>

<script>
    import Icon from './Icon'

    export default {
        components: {
            Icon,
        },

        computed: {
            selectable_locale() {
                if(this.$page.locale == 'ar') {
                    return 'en';
                }
                return 'ar'
            }
        },
    }
</script>
