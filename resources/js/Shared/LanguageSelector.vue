<template>
    <div class="ml-4">
        <a :href="'/language/'+[selectable_locale]">
            <icon class="w-5 h-5" :name="selectable_locale" />
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
