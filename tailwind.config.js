export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    "./node_modules/flowbite/**/*.js"
  ],
  theme: {
    extend: {
      colors: {
        foreground: '#000000',
        'muted-foreground-1': '#6b7280',
        'muted-foreground-2': '#9ca3af',
        primary: '#3b82f6',
        'muted-hover': '#f3f4f6',
        'muted-focus': '#e5e7eb',
      }
    },
  },
  plugins: [
    require('flowbite/plugin')
  ],
}