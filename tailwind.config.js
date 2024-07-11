/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    'node_modules/flowbite/**/*.js',
    'node_modules/preline/dist/*.js',
    '**/**/*.{php,phtml}'
  ],
  theme: {
    extend: {
      fontFamily: {
        inter: ['Inter', 'sans-serif'],
      },
      animation: {
        'infinite-scroll': 'infinite-scroll 25s linear infinite',
      },
      keyframes: {
        'infinite-scroll': {
          from: { transform: 'translateX(0)' },
          to: { transform: 'translateX(-100%)' },
        },
      }   
    },
    screens: {
      'sm': '640px',
      'md': '768px',
      'lg': '1024px',
      'xl': '1280px',
      '2xl': '1536px',
    }
  },
  plugins: [
    require('flowbite/plugin'),
    require('preline/plugin'),
    require('autoprefixer'),
    require('cssnano')({ preset: 'default', }),
  ],
  darkMode: 'class',
}