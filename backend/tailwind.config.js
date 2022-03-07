const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
    purge: [
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
        './node_modules/@protonemedia/inertiajs-tables-laravel-query-builder/**/*.{js,vue}',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Nunito', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    variants: {
        opacity: ['responsive', 'hover', 'focus', 'disabled'],
        float: ['responsive', 'direction'],
        margin: ['hover', 'responsive', 'direction'],
        padding: ['responsive', 'direction'],
        right: ['responsive', 'direction'],
        textAlign: ['responsive', 'direction'],
        backgroundColor: ['disabled'],
        cursor: ['disabled'],
    },

    plugins: [
        require('@tailwindcss/ui'),
        require('tailwindcss-dir')(),
    ],
};
