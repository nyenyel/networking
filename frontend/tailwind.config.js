const { addIconSelectors, addDynamicIconSelectors  } = require('@iconify/tailwind');

/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "index.html",
    "./src/**/*.{js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      keyframes: {
        reverseSpin: {
          '0%': { transform: 'rotate(0deg)' },
          '100%': { transform: 'rotate(-360deg)' }, // Reverse rotation
        },
      },
      animation: {
        'reverse-spin': 'reverseSpin 1s linear infinite', // Custom animation
      },
      colors:{
        dirty: '#f0f0f0',
        src: '#db1212',
        trc: '#1F9DF9',
        text: '#212121',
      },
      backgroundImage :{
        'home-bg': "url('/src/assets/bg-1.png')",
        'logo': "url('/src/assets/logo.png')",
      },
      fontFamily:{
        pf: ['pf'],
        sf: ['sf'],
        'sf-light': ['sf-light'],
        'sf-bold': ['sf-bold'],
        'sf-extrabold': ['sf-extrabold'],

      },
      scale: {
        '101': '1.005'
      }
    },
  },
  plugins: [
    addDynamicIconSelectors(),
    addIconSelectors([
      'mdi', 
      'mdi-light',
      'material-symbols',
      'pajamas'
    ]),
  ],
}