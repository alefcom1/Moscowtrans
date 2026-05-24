/** @type {import('tailwindcss').Config} */
export default {
  content: ['./src/**/*.{js,jsx}'],
  theme: {
    extend: {
      colors: {
        rtap: {
          bg:      'var(--rtap-bg)',
          surface: 'var(--rtap-surface)',
          border:  'var(--rtap-border)',
          text:    'var(--rtap-text)',
          accent:  'var(--rtap-accent)',
          gold:    'var(--rtap-gold)',
          green:   '#5ECB8F',
          purple:  '#A084E8',
          red:     '#E85555',
        },
      },
    },
  },
  plugins: [],
};
