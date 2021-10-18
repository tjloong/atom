const colors = require('tailwindcss/colors')
const defaultTheme = require('tailwindcss/defaultTheme')

module.exports = {
    mode: 'jit',
    
    darkMode: false, // or 'media' or 'class'
    
    purge: {
        content: [
            './resources/**/*.blade.php',
            './resources/**/*.js',
            './resources/**/*.vue',
            './vendor/jiannius/atom/resources/**/*.js',
            './vendor/jiannius/atom/resources/**/*.vue',
            './vendor/jiannius/atom/resources/**/*.blade.php',
        ],
    },
    
    theme: {
        extend: {
            colors: {
                theme: {
                    light: colors.blue[100],
                    DEFAULT: colors.blue[500],
                    dark: colors.blue[800],
                },
            },
            borderColor: theme => ({
                DEFAULT: theme('colors.gray.200', 'currentColor'),
            }),
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            boxShadow: theme => ({
                outline: '0 0 0 2px ' + theme('colors.blueGray.500'),
            }),
            fill: theme => theme('colors'),
        },
    },

    variants: {
        extend: {
            fill: ['focus', 'group-hover'],
        },
    },
    
    plugins: [
        require('@tailwindcss/forms'), 
        require('@tailwindcss/typography'),
    ],
}
