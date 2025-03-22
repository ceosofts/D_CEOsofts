module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {},
  },
  plugins: [],
  // This ensures Tailwind's utilities don't conflict with Bootstrap
  corePlugins: {
    preflight: false,
  },
}
